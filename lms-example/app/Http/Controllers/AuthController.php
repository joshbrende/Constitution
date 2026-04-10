<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

final class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectForRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $valid = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($valid, (bool) $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $default = $user->isAdmin() ? route('admin.dashboard')
                : ($user->canEditCourses() ? route('instructor.dashboard') : route('courses.index'));
            return redirect()->intended($default);
        }

        return back()->withErrors(['email' => __('Invalid credentials.')])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectForRole(Auth::user());
        }
        return view('auth.register');
    }

    /** Redirect user to the correct landing page by role. */
    private function redirectForRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->canEditCourses()) {
            return redirect()->route('instructor.dashboard');
        }
        return redirect()->route('courses.index');
    }

    public function register(Request $request)
    {
        $valid = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = DB::transaction(function () use ($valid) {
            $user = User::create([
                'name' => $valid['name'],
                'email' => $valid['email'],
                'password' => $valid['password'],
            ]);

            $role = Role::firstOrCreate(
                ['name' => 'student', 'guard_name' => 'web'],
                ['guard_name' => 'web']
            );
            $user->roles()->sync([$role->id]);

            return $user;
        });

        $user->sendEmailVerificationNotification();

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('verification.notice')->with('status', 'Registration successful. Please check your email to verify your address.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('courses.index');
    }

    // ---- Password reset ----

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('courses.index');
        }
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $valid = $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($valid);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    public function showResetPassword(Request $request, string $token)
    {
        if (Auth::check()) {
            return redirect()->route('courses.index');
        }
        $email = (string) $request->query('email', '');
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $valid = $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $valid,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }

    // ---- Email verification ----

    public function verifyNotice(Request $request)
    {
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('courses.index');
        }
        return view('auth.verify-email');
    }

    public function verify(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired verification link.');
        }
        $user = User::findOrFail($request->route('id'));
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Your email is already verified. You can sign in.');
        }
        $user->markEmailAsVerified();
        return redirect()->route('login')->with('status', 'Your email has been verified. You can sign in.');
    }

    public function resendVerification(Request $request)
    {
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('courses.index');
        }
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent. Please check your email.');
    }
}
