<?php

namespace App\Http\Controllers;

use App\Models\BackendUserInvitation;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class BackendUserInvitationController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger
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
                ->withErrors(['email' => __('This invitation is no longer valid. Ask an administrator to send a new one.')]);
        }

        if (User::query()->where('email', $invitation->email)->exists()) {
            return redirect()
                ->route('login')
                ->with('status', __('An account with this email already exists. Sign in below.'));
        }

        return view('auth.backend-invitation', [
            'email' => $invitation->email,
            'token' => $token,
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->findInvitation($token);
        if (! $invitation || ! $invitation->isValid()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => __('This invitation is no longer valid. Ask an administrator to send a new one.')]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'accept_terms' => ['required', 'accepted'],
        ]);

        if (User::query()->where('email', $invitation->email)->exists()) {
            return redirect()
                ->route('login')
                ->with('status', __('An account with this email already exists. Sign in below.'));
        }

        $user = DB::transaction(function () use ($invitation, $data) {
            $user = User::create([
                'name' => $data['name'],
                'surname' => $data['surname'],
                'email' => $invitation->email,
                'password' => $data['password'],
                'accepted_terms_at' => now(),
            ]);

            $roleIds = array_values(array_unique(array_map('intval', $invitation->role_ids ?? [])));
            $user->roles()->sync($roleIds);

            $invitation->accepted_at = now();
            $invitation->save();

            return $user;
        });

        $this->auditLogger->log(
            action: 'auth.backend_invitation.accepted',
            targetType: User::class,
            targetId: $user->id,
            metadata: ['email' => $user->email],
            request: $request
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', __('Welcome! Your account is ready.'));
    }

    private function findInvitation(string $token): ?BackendUserInvitation
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }

        return BackendUserInvitation::query()
            ->where('token_hash', BackendUserInvitation::hashToken($token))
            ->first();
    }
}
