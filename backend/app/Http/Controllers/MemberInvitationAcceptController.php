<?php

namespace App\Http\Controllers;

use App\Enums\MembershipSource;
use App\Models\MemberInvitation;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use App\Rules\ZimbabweNationalIdRule;
use App\Services\AuditLogger;
use App\Services\CertificateApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use InvalidArgumentException;

class MemberInvitationAcceptController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected CertificateApplicationService $applications,
    ) {}

    public function show(string $token): View|RedirectResponse
    {
        $invitation = $this->findInvitation($token);
        if (! $invitation) {
            abort(404);
        }

        if (! $invitation->isValid()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => __('This membership invitation is no longer valid.')]);
        }

        if (User::query()->where('email', $invitation->email)->exists()) {
            return redirect()
                ->route('login')
                ->with('status', __('An account with this email already exists. Sign in below.'));
        }

        return view('auth.member-invitation', [
            'invitation' => $invitation,
            'token' => $token,
            'requireNationalId' => (bool) SiteSetting::get('require_national_id', true),
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->findInvitation($token);
        if (! $invitation || ! $invitation->isValid()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => __('This membership invitation is no longer valid.')]);
        }

        $requireNationalId = (bool) SiteSetting::get('require_national_id', true);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'national_id' => array_filter([
                $requireNationalId ? 'required' : 'nullable',
                'string',
                'max:32',
                new ZimbabweNationalIdRule,
            ]),
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'accept_terms' => ['required', 'accepted'],
        ]);

        if (User::query()->where('email', $invitation->email)->exists()) {
            return redirect()
                ->route('login')
                ->with('status', __('An account with this email already exists. Sign in below.'));
        }

        try {
            $user = DB::transaction(function () use ($invitation, $data) {
                $studentRole = Role::firstOrCreate(
                    ['slug' => 'student'],
                    ['name' => 'Student', 'description' => 'Learner in ZANU PF Academy.']
                );

                $user = User::create([
                    'name' => $data['name'],
                    'surname' => $data['surname'],
                    'email' => $invitation->email,
                    'password' => $data['password'],
                    'national_id' => $data['national_id'] ?? $invitation->national_id,
                    'province_id' => $invitation->province_id,
                    'wing' => $invitation->wing,
                    'accepted_terms_at' => now(),
                    'membership_source' => MembershipSource::Invite->value,
                ]);

                $user->roles()->syncWithoutDetaching([$studentRole->id]);
                $this->applications->createFromInviteAdmission($user, MembershipSource::Invite);

                $invitation->accepted_at = now();
                $invitation->save();

                return $user;
            });
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        $this->auditLogger->log(
            action: 'membership.invite_accepted',
            targetType: User::class,
            targetId: $user->id,
            metadata: ['email' => $user->email, 'invitation_id' => $invitation->id],
            request: $request,
            actorUserId: $user->id,
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard')
            ->with('success', __('Welcome. Complete certificate payment to finalise membership and receive your membership number.'));
    }

    private function findInvitation(string $token): ?MemberInvitation
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }

        return MemberInvitation::query()
            ->where('token_hash', MemberInvitation::hashToken($token))
            ->first();
    }
}
