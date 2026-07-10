<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserWebPushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Profile
 *
 * PWA Web Push (VAPID) subscription registration.
 */
class WebPushSubscriptionController extends Controller
{
    /**
     * Register Web Push subscription
     *
     * @authenticated
     * @bodyParam endpoint string required Push service endpoint URL.
     * @bodyParam keys object required Subscription keys.
     * @bodyParam keys.p256dh string required P-256 DH key.
     * @bodyParam keys.auth string required Auth secret.
     * @bodyParam device_name string optional Browser label.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:2048', 'url'],
            'keys' => ['required', 'array'],
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth' => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $existing = UserWebPushSubscription::query()
            ->where('endpoint', $validated['endpoint'])
            ->first();

        if ($existing && (int) $existing->user_id !== (int) $user->id) {
            return response()->json([
                'message' => 'This push subscription is already registered to another account.',
            ], 409);
        }

        $subscription = UserWebPushSubscription::updateOrCreate(
            ['endpoint' => $validated['endpoint']],
            [
                'user_id' => $user->id,
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'device_name' => $validated['device_name'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'data' => [
                'id' => $subscription->id,
                'registered' => true,
            ],
        ]);
    }

    /**
     * Unregister Web Push subscription
     *
     * @authenticated
     * @bodyParam endpoint string required The subscription endpoint to remove.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:2048'],
        ]);

        UserWebPushSubscription::query()
            ->where('user_id', $user->id)
            ->where('endpoint', $validated['endpoint'])
            ->delete();

        return response()->json([], 204);
    }
}
