<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\RefreshToken;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        $provinces = Province::orderBy('sort_order')->orderBy('name')->get();

        return view('auth.register', compact('provinces'));
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function showResetPasswordForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($data['email']);
        $cacheKey = 'password_reset:' . sha1($email);
        $attempts = (int) Cache::get($cacheKey, 0);

        if ($attempts >= 3) {
            return back()
                ->withErrors(['email' => 'Too many password reset requests for this email. Please try again in about an hour.'])
                ->onlyInput('email');
        }

        Cache::put($cacheKey, $attempts + 1, 3600);

        try {
            PasswordBroker::sendResetLink(['email' => $email]);
        } catch (\Exception $e) {
            report($e);
        }

        return back()
            ->with('status', config('auth.password_reset_ack_message'))
            ->onlyInput('email');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = PasswordBroker::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use ($request) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                // Invalidate API sessions after a password change.
                $user->tokens()->delete();
                RefreshToken::where('user_id', $user->id)->whereNull('revoked_at')->update([
                    'revoked_at' => now(),
                ]);

                event(new PasswordReset($user));

                $this->auditLogger->log(
                    action: 'auth.web.password_reset',
                    targetType: User::class,
                    targetId: $user->id,
                    metadata: ['email' => $user->email],
                    request: $request,
                    actorUserId: $user->id,
                );
            }
        );

        if ($status === PasswordBroker::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', __($status));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->onlyInput('email');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'accept_terms' => ['required', 'accepted'],
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => $data['password'],
            'province_id' => $data['province_id'],
            'accepted_terms_at' => now(),
            'membership_standing' => \App\Enums\MembershipStanding::Applicant->value,
        ]);

        // Security: new registrations start as student only.
        // Membership is granted only after passing the membership assessment.
        $studentRole = Role::firstOrCreate(
            ['slug' => 'student'],
            ['name' => 'Student', 'description' => 'Learner in ZANU PF Academy.']
        );
        $user->roles()->syncWithoutDetaching([$studentRole->id]);

        $this->auditLogger->log(
            action: 'auth.web.registered',
            targetType: User::class,
            targetId: $user->id,
            metadata: ['email' => $user->email, 'source' => 'web'],
            request: $request,
            actorUserId: $user->id,
        );

        Auth::login($user);
        $request->session()->regenerate();

        return $this->safeRedirectIntended($request, '/dashboard');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $this->auditLogger->log(
                action: 'auth.web.login_failed',
                targetType: User::class,
                targetId: null,
                metadata: ['email' => $credentials['email'] ?? null],
                request: $request
            );
            return back()
                ->withErrors(['email' => 'The provided credentials are incorrect.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $this->auditLogger->log(
            action: 'auth.web.logged_in',
            targetType: User::class,
            targetId: auth()->id(),
            metadata: ['email' => auth()->user()?->email],
            request: $request
        );

        return $this->safeRedirectIntended($request, '/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            $this->auditLogger->log(
                action: 'auth.web.logged_out',
                targetType: User::class,
                targetId: $user->id,
                metadata: ['email' => $user->email],
                request: $request,
                actorUserId: $user->id,
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect to the intended URL only if it matches the allowed host/path rules.
     * Falls back to the given default path when the intended URL is external or unsafe.
     */
    protected function safeRedirectIntended(Request $request, string $defaultPath): RedirectResponse
    {
        $intended = $request->session()->pull('url.intended');

        if (! is_string($intended) || $intended === '') {
            return redirect($defaultPath);
        }

        // Allow only relative paths starting with a single "/" (no "//example.com")
        if (str_starts_with($intended, '/') && ! str_starts_with($intended, '//')) {
            return redirect($intended);
        }

        // For absolute URLs, enforce allowlist based on host.
        $parsed = parse_url($intended);
        $host = $parsed['host'] ?? null;
        if (! $host) {
            return redirect($defaultPath);
        }

        $allowedRaw = $_ENV['REDIRECT_ALLOWED_HOSTS'] ?? $_SERVER['REDIRECT_ALLOWED_HOSTS'] ?? '';
        $allowed = array_values(array_filter(array_map(static function ($v) {
            $v = trim((string) $v);
            return $v !== '' ? $v : null;
        }, explode(',', $allowedRaw))));

        // If no explicit allowlist is set, fall back to current host only.
        if (empty($allowed)) {
            $allowed = [$request->getHost()];
        }

        if (in_array($host, $allowed, true)) {
            return redirect($intended);
        }

        return redirect($defaultPath);
    }
}

