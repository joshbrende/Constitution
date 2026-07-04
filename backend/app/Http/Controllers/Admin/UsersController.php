<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MembershipStanding;
use App\Http\Controllers\Controller;
use App\Models\BackendUserInvitation;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use App\Notifications\BackendUserInvitationNotification;
use App\Notifications\BackendUserWelcomeNotification;
use App\Services\AdminAccessService;
use App\Services\AdminScopeService;
use App\Services\AuditLogger;
use App\Services\BackendRoleDutiesService;
use App\Services\MembershipStandingService;
use App\Services\RoleAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function __construct(
        protected RoleAssignmentService $roleAssignment,
        protected AdminAccessService $adminAccess,
        protected AdminScopeService $adminScope,
        protected BackendRoleDutiesService $roleDuties,
        protected AuditLogger $auditLogger,
        protected MembershipStandingService $membershipStanding,
    ) {}

    public function index(Request $request): View
    {
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);

        $query = User::query()
            ->with('roles')
            ->orderByDesc('id');

        $this->adminScope->applyToUserQuery($query, $admin);

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('surname', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(25)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'canProvisionBackendUsers' => $this->roleDuties->canProvisionBackendUsers($admin),
        ]);
    }

    public function createInvite(): View
    {
        $this->assertCanProvisionBackendUsers();

        return view('admin.users.invite', $this->provisionFormData());
    }

    public function storeInvite(Request $request): RedirectResponse
    {
        $this->assertCanProvisionBackendUsers();

        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $provisionableIds = $this->roleDuties->provisionableRoleIds(auth()->user());

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'distinct', Rule::in($provisionableIds)],
        ]);

        $email = $data['email'];
        $roleIds = array_values(array_unique(array_map('intval', $data['roles'])));

        BackendUserInvitation::query()
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->delete();

        $plainToken = Str::random(64);
        $invitation = BackendUserInvitation::create([
            'email' => $email,
            'token_hash' => BackendUserInvitation::hashToken($plainToken),
            'role_ids' => $roleIds,
            'invited_by_user_id' => (int) auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        $acceptUrl = url('/invitations/backend/'.$plainToken);
        $loginUrl = route('login');
        $roleSummary = Role::query()->whereIn('id', $roleIds)->orderBy('name')->pluck('name')->implode(', ');
        $dutyBriefs = $this->roleDuties->dutyBriefsForRoleIds($roleIds);
        $dutyText = $this->roleDuties->formatDutyBriefsForEmail($dutyBriefs);

        Notification::route('mail', $email)
            ->notify(new BackendUserInvitationNotification(
                acceptUrl: $acceptUrl,
                loginUrl: $loginUrl,
                email: $email,
                roleSummary: $roleSummary,
                dutyBriefs: $dutyBriefs,
                dutyText: $dutyText,
            ));

        $this->auditLogger->log(
            action: 'admin.users.invitation_sent',
            targetType: BackendUserInvitation::class,
            targetId: $invitation->id,
            metadata: [
                'email' => $email,
                'role_ids' => $roleIds,
                'duty_sections' => $this->roleDuties->accessibleSectionSlugsForRoleIds($roleIds),
            ],
            request: $request
        );

        return redirect()->route('admin.users.index')
            ->with('success', __('Invitation with role duties sent to :email.', ['email' => $email]));
    }

    public function createBackendUser(): View
    {
        $this->assertCanProvisionBackendUsers();

        return view('admin.users.create-backend', $this->provisionFormData());
    }

    public function storeBackendUser(Request $request): RedirectResponse
    {
        $this->assertCanProvisionBackendUsers();

        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $provisionableIds = $this->roleDuties->provisionableRoleIds(auth()->user());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'distinct', Rule::in($provisionableIds)],
        ]);

        $roleIds = array_values(array_unique(array_map('intval', $data['roles'])));
        $plainPassword = Str::password(16);

        $user = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => $plainPassword,
            'accepted_terms_at' => now(),
        ]);

        $user->roles()->sync($roleIds);

        $loginUrl = route('login');
        $roleSummary = Role::query()->whereIn('id', $roleIds)->orderBy('name')->pluck('name')->implode(', ');
        $dutyBriefs = $this->roleDuties->dutyBriefsForRoleIds($roleIds);

        $user->notify(new BackendUserWelcomeNotification(
            loginUrl: $loginUrl,
            email: $user->email,
            plainPassword: $plainPassword,
            roleSummary: $roleSummary,
            dutyBriefs: $dutyBriefs,
        ));

        $this->auditLogger->log(
            action: 'admin.users.backend_created',
            targetType: User::class,
            targetId: $user->id,
            metadata: [
                'email' => $user->email,
                'role_ids' => $roleIds,
                'duty_sections' => $this->roleDuties->accessibleSectionSlugsForRoleIds($roleIds),
            ],
            request: $request
        );

        return redirect()->route('admin.users.index')
            ->with('success', __('Backend user :email created. Welcome email with login details sent.', ['email' => $user->email]));
    }

    public function edit(User $user): View
    {
        $this->authorize('admin.section', 'users');

        $admin = auth()->user();
        abort_unless($admin instanceof User, 403);
        $this->adminScope->assertCanAccessUser($admin, $user);

        $user->load(['roles', 'province:id,name', 'branchAdmittedBy:id,name,surname', 'cadreDesignatedBy:id,name,surname']);
        $roles = Role::orderBy('name')->get();
        $assignableRoleIds = collect($this->roleAssignment->assignableRoleIds(auth()->user()));
        $roleDutyBriefs = $this->roleDuties->dutyBriefsForRoleIds(
            $roles->pluck('id')->map(fn ($id) => (int) $id)->all()
        );
        $roleDutyMap = collect($roleDutyBriefs)->keyBy('slug');
        $provinces = Province::orderBy('name')->get(['id', 'name']);
        $membershipStandings = MembershipStanding::cases();
        $wings = config('academy.user_wings', []);

        $this->auditLogger->log(
            action: 'admin.users.pii_viewed',
            targetType: User::class,
            targetId: $user->id,
            metadata: ['email' => $user->email],
            request: request()
        );

        return view('admin.users.edit', compact(
            'user',
            'roles',
            'assignableRoleIds',
            'roleDutyMap',
            'provinces',
            'membershipStandings',
            'wings',
        ));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('admin.section', 'users');

        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        $this->adminScope->assertCanAccessUser($admin, $user);

        $assignable = collect($this->roleAssignment->assignableRoleIds($admin));

        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'distinct', Rule::in($assignable->all())],
            'wing' => ['nullable', 'string', Rule::in(config('academy.user_wings', []))],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'membership_standing' => ['nullable', Rule::enum(MembershipStanding::class)],
            'suspension_reason' => ['nullable', 'string', 'max:500'],
            'branch_admitted' => ['nullable', 'boolean'],
            'branch_admission_note' => ['nullable', 'string', 'max:500'],
            'cadre_designated' => ['nullable', 'boolean'],
        ]);

        $profileBefore = [
            'wing' => $user->wing,
            'province_id' => $user->province_id,
            'membership_standing' => $user->membership_standing instanceof MembershipStanding
                ? $user->membership_standing->value
                : (string) $user->membership_standing,
        ];

        $user->forceFill([
            'wing' => $validated['wing'] ?? null,
            'province_id' => $validated['province_id'] ?? null,
        ]);

        if (! empty($validated['membership_standing'])) {
            $newStanding = MembershipStanding::from($validated['membership_standing']);
            $current = $this->membershipStanding->standing($user);

            if ($newStanding === MembershipStanding::Suspended && $current !== MembershipStanding::Suspended) {
                $user->save();
                $this->membershipStanding->markSuspended($user, $admin, $validated['suspension_reason'] ?? null);
            } elseif ($current === MembershipStanding::Suspended && $newStanding !== MembershipStanding::Suspended) {
                $user->save();
                $this->membershipStanding->reinstate($user, $admin, $newStanding);
            } elseif ($newStanding !== $current) {
                $user->forceFill(['membership_standing' => $newStanding->value])->save();
                $this->auditLogger->log(
                    action: 'admin.users.membership_standing_updated',
                    targetType: User::class,
                    targetId: $user->id,
                    metadata: [
                        'from' => $current->value,
                        'to' => $newStanding->value,
                        'admin_user_id' => $admin->id,
                    ],
                    request: $request
                );
            } else {
                $user->save();
            }
        } else {
            $user->save();
        }

        $wantsBranchAdmission = $request->boolean('branch_admitted');
        if ($wantsBranchAdmission && ! $user->hasBranchAdmission()) {
            $user->forceFill([
                'branch_admitted_at' => now(),
                'branch_admitted_by_user_id' => $admin->id,
                'branch_admission_note' => $validated['branch_admission_note'] ?? null,
            ])->save();
            $this->auditLogger->log(
                action: 'admin.users.branch_admission_confirmed',
                targetType: User::class,
                targetId: $user->id,
                metadata: ['admin_user_id' => $admin->id],
                request: $request
            );
        } elseif (! $wantsBranchAdmission && $user->hasBranchAdmission()) {
            $user->forceFill([
                'branch_admitted_at' => null,
                'branch_admitted_by_user_id' => null,
                'branch_admission_note' => null,
            ])->save();
            $this->auditLogger->log(
                action: 'admin.users.branch_admission_revoked',
                targetType: User::class,
                targetId: $user->id,
                metadata: ['admin_user_id' => $admin->id],
                request: $request
            );
        } elseif ($wantsBranchAdmission && $user->hasBranchAdmission()) {
            $user->forceFill([
                'branch_admission_note' => $validated['branch_admission_note'] ?? null,
            ])->save();
        }

        $wantsCadre = $request->boolean('cadre_designated');
        if ($wantsCadre && ! $user->isCadreDesignee()) {
            $user->forceFill([
                'cadre_designated_at' => now(),
                'cadre_designated_by_user_id' => $admin->id,
            ])->save();
            $this->auditLogger->log(
                action: 'admin.users.cadre_designated',
                targetType: User::class,
                targetId: $user->id,
                metadata: ['admin_user_id' => $admin->id],
                request: $request
            );
        } elseif (! $wantsCadre && $user->isCadreDesignee()) {
            $user->forceFill([
                'cadre_designated_at' => null,
                'cadre_designated_by_user_id' => null,
            ])->save();
            $this->auditLogger->log(
                action: 'admin.users.cadre_designation_revoked',
                targetType: User::class,
                targetId: $user->id,
                metadata: ['admin_user_id' => $admin->id],
                request: $request
            );
        }

        $user->refresh();

        $profileAfter = [
            'wing' => $user->wing,
            'province_id' => $user->province_id,
            'membership_standing' => $user->membership_standing instanceof MembershipStanding
                ? $user->membership_standing->value
                : (string) $user->membership_standing,
        ];

        if ($profileBefore !== $profileAfter && ($profileBefore['wing'] !== $profileAfter['wing'] || $profileBefore['province_id'] !== $profileAfter['province_id'])) {
            $this->auditLogger->log(
                action: 'admin.users.profile_updated',
                targetType: User::class,
                targetId: $user->id,
                metadata: ['before' => $profileBefore, 'after' => $profileAfter, 'admin_user_id' => $admin->id],
                request: $request
            );
        }

        $user->refresh();
        $user->load('roles');
        $beforeRoleIds = $user->roles->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();

        $requested = collect($request->input('roles', []))->map(fn ($v) => (int) $v)->unique();
        $locked = $user->roles->pluck('id')->diff($assignable);

        $final = $locked->merge($requested->intersect($assignable))->unique()->values()->all();

        $user->roles()->sync($final);

        $afterRoleIds = collect($final)->sort()->values()->all();
        if ($beforeRoleIds !== $afterRoleIds) {
            $this->auditLogger->log(
                action: 'admin.users.roles_updated',
                targetType: User::class,
                targetId: $user->id,
                metadata: [
                    'before_role_ids' => $beforeRoleIds,
                    'after_role_ids' => $afterRoleIds,
                    'duty_sections' => $this->roleDuties->accessibleSectionSlugsForRoleIds($afterRoleIds),
                ],
                request: $request
            );
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    private function assertCanProvisionBackendUsers(): void
    {
        $admin = auth()->user();
        abort_unless($admin instanceof User, 403);
        abort_unless($this->roleDuties->canProvisionBackendUsers($admin), 403);
        $this->authorize('admin.section', 'users');
    }

    /**
     * @return array{roles: \Illuminate\Support\Collection<int, Role>, roleDutyBriefs: list<array<string, mixed>>}
     */
    private function provisionFormData(): array
    {
        $roles = $this->roleDuties->provisionableRoles(auth()->user());
        $roleDutyBriefs = $this->roleDuties->dutyBriefsForRoleIds(
            $roles->pluck('id')->map(fn ($id) => (int) $id)->all()
        );

        return compact('roles', 'roleDutyBriefs');
    }
}
