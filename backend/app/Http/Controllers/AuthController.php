<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Role;
use App\Models\RefreshToken;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\TokenAbilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * @group Authentication
 *
 * Register, login, token refresh, and password recovery for mobile and API clients.
 */
class AuthController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected TokenAbilityService $tokenAbilities
    ) {}

    private function issueAccessToken(User $user): string
    {
        $minutes = (int) config('api_tokens.access_token_expiry_minutes');
        $expiresAt = now()->addMinutes($minutes);
        $abilities = $this->tokenAbilities->abilitiesForUser($user);

        return $user->createToken('access_token', $abilities, $expiresAt)->plainTextToken;
    }

    private function issueRefreshToken(User $user, ?string $familyId = null): string
    {
        $days = (int) config('api_tokens.refresh_token_expiry_days');
        $token = Str::random(64);
        $tokenHash = hash('sha256', $token);

        RefreshToken::create([
            'user_id' => $user->id,
            'family_id' => $familyId ?: (string) Str::uuid(),
            'token_hash' => $tokenHash,
            'expires_at' => now()->addDays($days),
            'revoked_at' => null,
        ]);

        return $token;
    }

    /**
     * Register a new member account
     *
     * Creates a student-role user and returns Sanctum access and refresh tokens.
     *
     * @unauthenticated
     * @bodyParam name string required First name. Example: Tariro
     * @bodyParam surname string required Last name. Example: Moyo
     * @bodyParam email string required Unique email address. Example: member@example.org.zw
     * @bodyParam password string required Min 8 chars, mixed case, and a number. Example: SecurePass123!
     * @bodyParam password_confirmation string required Must match `password`. Example: SecurePass123!
     * @bodyParam accept_terms string required Must be `true` or `1`. Example: true
     * @bodyParam province_id integer optional Province ID from `GET /api/v1/provinces` (1=Bulawayo, 2=Harare, …). Example: 2
     * @response 201 scenario="Success" {"user":{},"access_token":"1|…","refresh_token":"…"}
     * @response 422 scenario="Validation error" {"message":"The email has already been taken.","errors":{"email":["The email has already been taken."]}}
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'accept_terms' => ['required', 'accepted'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => $data['password'],
            'province_id' => $data['province_id'] ?? null,
            'accepted_terms_at' => now(),
            'membership_standing' => \App\Enums\MembershipStanding::Applicant->value,
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
            request: $request,
            actorUserId: $user->id,
        );

        return response()->json([
            'user' => $user->fresh('roles'),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ], 201);
    }

    /**
     * Login
     *
     * Returns access and refresh tokens. Previous tokens for this user are revoked.
     *
     * @unauthenticated
     * @bodyParam email string required Account email. Example: member@example.org.zw
     * @bodyParam password string required Account password. Example: SecurePass123!
     * @response 200 {"user":{},"access_token":"1|…","refresh_token":"…"}
     * @response 422 scenario="Invalid credentials" {"message":"The provided credentials are incorrect."}
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
            request: $request,
            actorUserId: $user->id,
        );

        return response()->json([
            'user' => $user->load('roles'),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
    }

    /**
     * Logout
     *
     * Revokes the current access token and all refresh tokens for the user.
     *
     * **Try it in these docs:** run **Login** first, click **Try it out**, set **Authorization** to
     * `Bearer {access_token}` from the login response, then send.
     *
     * @authenticated
     * @response 200 {"message":"Logged out successfully."}
     * @response 401 scenario="Missing token" {"error":"unauthenticated","message":"Unauthenticated."}
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
                request: $request,
                actorUserId: $user->id,
            );
        }

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Refresh tokens
     *
     * Rotates the refresh token and issues a new access token. Rate limited.
     *
     * **Try it in these docs:** run **Login** (or **Register**) first, copy the `refresh_token`
     * from the JSON response, paste it into the field below, then send this request.
     * Each refresh returns a new refresh token — the old one is invalidated.
     *
     * @unauthenticated
     * @bodyParam refresh_token string required Paste `refresh_token` from the Login or Register response (64 chars). Example: PASTE_REFRESH_TOKEN_FROM_LOGIN
     * @response 200 {"user":{},"access_token":"1|…","refresh_token":"…"}
     * @response 401 scenario="Invalid token" {"message":"Refresh token expired or invalid. Please sign in again."}
     */
    public function refresh(Request $request): JsonResponse
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $tokenHash = hash('sha256', $data['refresh_token']);

        return DB::transaction(function () use ($tokenHash, $request) {
            $rt = RefreshToken::with('user')
                ->where('token_hash', $tokenHash)
                ->lockForUpdate()
                ->first();

            if (! $rt || ! $rt->user) {
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

            // Reuse of a rotated token: revoke the whole family and all access tokens.
            if ($rt->revoked_at !== null) {
                if ($rt->family_id) {
                    RefreshToken::where('family_id', $rt->family_id)
                        ->whereNull('revoked_at')
                        ->update(['revoked_at' => now()]);
                }
                $rt->user->tokens()->delete();

                $this->auditLogger->log(
                    action: 'auth.api.refresh_reuse_detected',
                    targetType: RefreshToken::class,
                    targetId: $rt->id,
                    metadata: [
                        'family_id' => $rt->family_id,
                        'user_id' => $rt->user_id,
                    ],
                    request: $request,
                    actorUserId: $rt->user_id,
                );

                return response()->json([
                    'message' => 'Refresh token expired or invalid. Please sign in again.',
                ], 401);
            }

            if (! $rt->expires_at || $rt->expires_at->isPast()) {
                $this->auditLogger->log(
                    action: 'auth.api.refresh_failed',
                    targetType: RefreshToken::class,
                    targetId: $rt->id,
                    metadata: ['reason' => 'invalid_or_expired_refresh_token'],
                    request: $request
                );

                return response()->json([
                    'message' => 'Refresh token expired or invalid. Please sign in again.',
                ], 401);
            }

            $rt->update(['revoked_at' => now()]);

            $user = $rt->user;
            $user->tokens()->delete();

            $familyId = $rt->family_id ?: (string) Str::uuid();
            $accessToken = $this->issueAccessToken($user);
            $refreshToken = $this->issueRefreshToken($user, $familyId);

            $this->auditLogger->log(
                action: 'auth.api.refresh_succeeded',
                targetType: User::class,
                targetId: $user->id,
                metadata: [
                    'refresh_token_id' => $rt->id,
                    'family_id' => $familyId,
                ],
                request: $request,
                actorUserId: $user->id,
            );

            return response()->json([
                'user' => $user->fresh('roles'),
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ]);
        });
    }

    /**
     * Forgot password
     *
     * Sends a password reset link when the email exists. Rate limited to 3 requests per hour per email.
     * Returns the same acknowledgment for unknown emails to prevent account enumeration.
     *
     * @unauthenticated
     * @bodyParam email string required Account email. Example: mobile.test@zanupf.org.zw
     * @response 200 scenario="Acknowledged" {"message":"If an account exists for this email address, we have sent a password reset link."}
     * @response 429 scenario="Rate limited" {"message":"Too many password reset requests for this email. Please try again in about an hour."}
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

        $status = null;
        try {
            $status = PasswordBroker::sendResetLink(['email' => $email]);
        } catch (\Exception $e) {
            report($e);
        }

        if ($status === PasswordBroker::RESET_LINK_SENT) {
            $this->auditLogger->log(
                action: 'auth.api.password_reset_requested',
                targetType: User::class,
                targetId: null,
                metadata: ['email' => $email],
                request: $request
            );
        }

        return response()->json([
            'message' => config('auth.password_reset_ack_message'),
        ]);
    }
}

