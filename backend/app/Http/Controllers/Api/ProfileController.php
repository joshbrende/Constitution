<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\ZimbabweNationalIdRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles', 'province:id,name,code']);

        return response()->json(['data' => $user]);
    }

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

