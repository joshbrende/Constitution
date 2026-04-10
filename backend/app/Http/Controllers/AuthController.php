<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RefreshToken;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    private function issueAccessToken(User $user): string
    {
        $minutes = (int) env('ACCESS_TOKEN_EXPIRY_MINUTES', 15);
        $expiresAt = now()->addMinutes($minutes);

        return $user->createToken('access_token', ['*'], $expiresAt)->plainTextToken;
    }

    private function issueRefreshToken(User $user): string
    {
        $days = (int) env('REFRESH_TOKEN_EXPIRY_DAYS', 7);
        $token = Str::random(64);
        $tokenHash = hash('sha256', $token);

        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addDays($days),
            'revoked_at' => null,
        ]);

        return $token;
    }

    /**
     * Register a new user.
     *
     * Expected payload:
     * - name
     * - surname
     * - email
     * - password
     * - password_confirmation
     * - accept_terms (boolean)
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'accept_terms' => ['required', 'accepted'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => $data['password'],
            'accepted_terms_at' => now(),
        ]);

        // Assign default role: student only. Member role is granted after passing the membership course.
        $studentRole = Role::firstOrCreate(
            ['slug' => 'student'],
            ['name' => 'Student', 'description' => 'Learner in ZANU PF Academy.']
        );
        $user->roles()->attach($studentRole->id);

        // New user session: issue short-lived access token + long-lived refresh token.
        $accessToken = $this->issueAccessToken($user);
        $refreshToken = $this->issueRefreshToken($user);

        $this->auditLogger->log(
            action: 'auth.api.registered',
            targetType: User::class,
            targetId: $user->id,
            metadata: ['email' => $user->email, 'source' => 'api'],
            request: $request
        );

        return response()->json([
            'user' => $user->fresh('roles'),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ], 201);
    }

    /**
     * Login an existing user.
     *
     * Expected payload:
     * - email
     * - password
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            $this->auditLogger->log(
                action: 'auth.api.login_failed',
                targetType: User::class,
                targetId: $user?->id,
                metadata: ['email' => $credentials['email'] ?? null],
                request: $request
            );
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 422);
        }

        // Revoke existing access tokens and refresh tokens on new login
        // to reduce the impact of stolen credentials.
        $user->tokens()->delete();
        RefreshToken::where('user_id', $user->id)->whereNull('revoked_at')->update([
            'revoked_at' => now(),
        ]);

        $accessToken = $this->issueAccessToken($user);
        $refreshToken = $this->issueRefreshToken($user);

        $this->auditLogger->log(
            action: 'auth.api.logged_in',
            targetType: User::class,
            targetId: $user->id,
            metadata: ['email' => $user->email],
            request: $request
        );

        return response()->json([
            'user' => $user->load('roles'),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
    }

    /**
     * Logout (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $this->authorize('logoutApi', $user);
        }

        $user?->currentAccessToken()?->delete();

        if ($user) {
            RefreshToken::where('user_id', $user->id)->whereNull('revoked_at')->update([
                'revoked_at' => now(),
            ]);
            $this->auditLogger->log(
                action: 'auth.api.logged_out',
                targetType: User::class,
                targetId: $user->id,
                metadata: ['email' => $user->email],
                request: $request
            );
        }

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Refresh access token using refresh token (rotation).
     *
     * - Validates refresh token + expiry
     * - Revokes old refresh token
     * - Issues a new access token and a new refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $tokenHash = hash('sha256', $data['refresh_token']);

        $rt = RefreshToken::with('user')
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->first();

        if (! $rt || ! $rt->expires_at || $rt->expires_at->isPast() || ! $rt->user) {
            $this->auditLogger->log(
                action: 'auth.api.refresh_failed',
                targetType: RefreshToken::class,
                targetId: $rt?->id,
                metadata: ['reason' => 'invalid_or_expired_refresh_token'],
                request: $request
            );
            return response()->json([
                'message' => 'Refresh token expired or invalid. Please sign in again.',
            ], 401);
        }

        return DB::transaction(function () use ($rt) {
            // Rotate refresh token
            $rt->update(['revoked_at' => now()]);

            // Hardening: revoke all existing access tokens for the user.
            $user = $rt->user;
            $user->tokens()->delete();

            $accessToken = $this->issueAccessToken($user);
            $refreshToken = $this->issueRefreshToken($user);

            $this->auditLogger->log(
                action: 'auth.api.refresh_succeeded',
                targetType: User::class,
                targetId: $user->id,
                metadata: ['refresh_token_id' => $rt->id],
                request: request()
            );

            return response()->json([
                'user' => $user->fresh('roles'),
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ]);
        });
    }

    /**
     * Request a password reset link (mobile).
     *
     * Rate limited to 3 requests per email per hour.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($data['email']);
        $cacheKey = 'password_reset:' . sha1($email);
        $attempts = (int) Cache::get($cacheKey, 0);

        if ($attempts >= 3) {
            $this->auditLogger->log(
                action: 'auth.api.password_reset_rate_limited',
                targetType: User::class,
                targetId: null,
                metadata: ['email' => $email],
                request: $request
            );
            return response()->json([
                'message' => 'Too many password reset requests for this email. Please try again in about an hour.',
            ], 429);
        }

        Cache::put($cacheKey, $attempts + 1, 3600);

        $status = PasswordBroker::sendResetLink(['email' => $email]);

        if ($status === PasswordBroker::RESET_LINK_SENT) {
            $this->auditLogger->log(
                action: 'auth.api.password_reset_requested',
                targetType: User::class,
                targetId: null,
                metadata: ['email' => $email],
                request: $request
            );
            return response()->json([
                'message' => __($status),
            ]);
        }

        return response()->json([
            'message' => __($status),
        ], 422);
    }
}

