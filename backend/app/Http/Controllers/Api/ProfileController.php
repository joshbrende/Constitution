<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\ZimbabweNationalIdRule;
use App\Services\WingMembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Profile
 *
 * Member profile, provinces list, and account deletion.
 */
class ProfileController extends Controller
{
    /**
     * Get profile
     *
     * Returns the authenticated user with roles and province.
     *
     * **Try it:** run **Login** first, then click **Try it out** here and set
     * **Authorization** to `Bearer {access_token}` from the login response.
     *
     * @authenticated
     * @response 200 {"data":{"id":1,"name":"Tariro","surname":"Moyo","email":"member@example.org.zw","province":{"id":2,"name":"Harare","code":"harare"},"roles":[]}}
     * @response 401 scenario="Missing token" {"error":"unauthenticated","message":"Unauthenticated."}
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'roles',
            'province:id,name,code',
            'memberships' => fn ($q) => $q->where('status', 'active')->orderBy('wing'),
        ]);

        $payload = $user->toArray();
        $payload['memberships'] = $user->memberships->map(fn ($m) => [
            'wing' => $m->wing,
            'status' => $m->status instanceof \App\Enums\MembershipWingStatus
                ? $m->status->value
                : (string) $m->status,
            'joined_at' => optional($m->joined_at)->toIso8601String(),
        ])->values()->all();
        $payload['active_wings'] = app(WingMembershipService::class)->activeWings($user);

        return response()->json(['data' => $payload]);
    }

    /**
     * Update profile
     *
     * Update national ID and province. National ID format is validated server-side.
     *
     * @authenticated
     * @bodyParam national_id string optional Zimbabwe national ID. Example: 63-123456A78
     * @bodyParam province_id integer optional Province ID from GET /api/v1/provinces. Example: 2
     * @response 200 {"data":{"id":1,"national_id":"63-123456A78","province_id":2}}
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authorize('update', $user);

        $data = $request->validate([
            'national_id' => ['nullable', 'string', 'max:32', new ZimbabweNationalIdRule],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
        ]);

        $user->fill($data);
        $user->save();

        return response()->json(['data' => $user->fresh(['roles', 'province:id,name,code'])]);
    }

    /**
     * Delete account
     *
     * Permanently deletes the authenticated user and revokes all tokens.
     *
     * @authenticated
     * @response 204 scenario="Deleted" {}
     * @response 401 scenario="Missing token" {"message":"Unauthenticated."}
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Treat delete as a privileged self-service action; reuse update authorization.
        $this->authorize('update', $user);

        // Revoke access tokens immediately.
        $user->tokens()->delete();

        // Hard-delete the user and cascade related data (enrolments, attempts, certificates, messages, etc).
        // Audit logs will null out the actor on delete where configured.
        $user->delete();

        return response()->json([], 204);
    }
}
