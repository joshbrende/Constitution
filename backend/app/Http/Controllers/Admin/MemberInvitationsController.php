<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MembershipSource;
use App\Http\Controllers\Controller;
use App\Models\MemberInvitation;
use App\Models\Province;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\MemberAdminCreatedNotification;
use App\Notifications\MemberInvitationNotification;
use App\Rules\ZimbabweNationalIdRule;
use App\Services\AuditLogger;
use App\Services\CertificateApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

class MemberInvitationsController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected CertificateApplicationService $applications,
    ) {}

    public function create(): View
    {
        $this->authorize('admin.action', 'membership_invite');

        return view('admin.members.invite', [
            'provinces' => Province::orderBy('sort_order')->orderBy('name')->get(),
            'wings' => config('academy.user_wings', []),
            'requireNationalId' => (bool) SiteSetting::get('require_national_id', true),
        ]);
    }

    public function storeInvite(Request $request): RedirectResponse
    {
        $this->authorize('admin.action', 'membership_invite');

        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $requireNationalId = (bool) SiteSetting::get('require_national_id', true);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'national_id' => array_filter([
                $requireNationalId ? 'required' : 'nullable',
                'string',
                'max:32',
                new ZimbabweNationalIdRule,
            ]),
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'wing' => ['nullable', 'string', Rule::in(config('academy.user_wings', []))],
        ]);

        $email = $data['email'];

        $openInvite = MemberInvitation::query()
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($openInvite) {
            return back()
                ->withErrors(['email' => 'An open invitation already exists for this email.'])
                ->withInput();
        }

        MemberInvitation::query()
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->delete();

        $plainToken = Str::random(64);
        $invitation = MemberInvitation::create([
            'email' => $email,
            'token_hash' => MemberInvitation::hashToken($plainToken),
            'name' => $data['name'] ?? null,
            'surname' => $data['surname'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'province_id' => $data['province_id'] ?? null,
            'wing' => $data['wing'] ?? null,
            'invited_by_user_id' => (int) auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        $acceptUrl = url('/invitations/member/'.$plainToken);

        Notification::route('mail', $email)
            ->notify(new MemberInvitationNotification(
                acceptUrl: $acceptUrl,
                email: $email,
            ));

        $this->auditLogger->log(
            action: 'membership.invite_sent',
            targetType: MemberInvitation::class,
            targetId: $invitation->id,
            metadata: ['email' => $email],
            request: $request,
        );

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Membership invitation sent to '.$email.'.');
    }

    public function createMemberForm(): View
    {
        $this->authorize('admin.action', 'membership_invite');

        return view('admin.members.create', [
            'provinces' => Province::orderBy('sort_order')->orderBy('name')->get(),
            'wings' => config('academy.user_wings', []),
            'requireNationalId' => (bool) SiteSetting::get('require_national_id', true),
        ]);
    }

    public function storeMember(Request $request): RedirectResponse
    {
        $this->authorize('admin.action', 'membership_invite');

        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $requireNationalId = (bool) SiteSetting::get('require_national_id', true);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'national_id' => array_filter([
                $requireNationalId ? 'required' : 'nullable',
                'string',
                'max:32',
                new ZimbabweNationalIdRule,
            ]),
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'wing' => ['nullable', 'string', Rule::in(config('academy.user_wings', []))],
        ]);

        try {
            $temporaryPassword = Str::password(12);

            $user = DB::transaction(function () use ($data, $temporaryPassword) {
                $studentRole = Role::firstOrCreate(
                    ['slug' => 'student'],
                    ['name' => 'Student', 'description' => 'Learner in ZANU PF Academy.']
                );

                $user = User::create([
                    'name' => $data['name'],
                    'surname' => $data['surname'],
                    'email' => $data['email'],
                    'password' => $temporaryPassword,
                    'national_id' => $data['national_id'] ?? null,
                    'province_id' => $data['province_id'],
                    'wing' => $data['wing'] ?? null,
                    'accepted_terms_at' => now(),
                    'membership_source' => MembershipSource::AdminCreated->value,
                ]);

                $user->roles()->syncWithoutDetaching([$studentRole->id]);
                $this->applications->createFromInviteAdmission($user, MembershipSource::AdminCreated);

                return $user;
            });
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        Notification::route('mail', $user->email)
            ->notify(new MemberAdminCreatedNotification(
                loginUrl: route('login'),
                email: $user->email,
                temporaryPassword: $temporaryPassword,
            ));

        $this->auditLogger->log(
            action: 'membership.admin_created',
            targetType: User::class,
            targetId: $user->id,
            metadata: ['email' => $user->email],
            request: $request,
        );

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Member account created. They must complete certificate payment before receiving a membership number.');
    }
}
