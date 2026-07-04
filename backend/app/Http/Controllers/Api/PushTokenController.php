<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPushToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Profile
 *
 * Mobile push notification token registration (Expo).
 */
class PushTokenController extends Controller
{
    /**
     * Register push token
     *
     * Stores an Expo push token for the authenticated user (iOS or Android).
     *
     * @authenticated
     * @bodyParam expo_push_token string required Expo push token. Example: ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]
     * @bodyParam platform string required Device platform. Example: android
     * @bodyParam device_name string optional Human-readable device name. Example: Pixel 8
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $validated = $request->validate([
            'expo_push_token' => ['required', 'string', 'max:255', 'regex:/^ExponentPushToken\[/'],
            'platform' => ['required', 'string', Rule::in(['ios', 'android'])],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $token = UserPushToken::updateOrCreate(
            ['expo_push_token' => $validated['expo_push_token']],
            [
                'user_id' => $user->id,
                'platform' => $validated['platform'],
                'device_name' => $validated['device_name'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'data' => [
                'id' => $token->id,
                'registered' => true,
            ],
        ]);
    }

    /**
     * Unregister push token
     *
     * Removes an Expo push token on logout or when the user disables notifications.
     *
     * @authenticated
     * @bodyParam expo_push_token string required The token to remove. Example: ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $validated = $request->validate([
            'expo_push_token' => ['required', 'string', 'max:255'],
        ]);

        UserPushToken::query()
            ->where('user_id', $user->id)
            ->where('expo_push_token', $validated['expo_push_token'])
            ->delete();

        return response()->json([], 204);
    }
}
