<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminActivityController extends Controller
{
    public function markSeen(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $data = $request->validate([
            'last_seen_audit_log_id' => ['required', 'integer', 'min:0'],
        ]);

        $lastSeen = (int) $data['last_seen_audit_log_id'];

        AdminActivityRead::updateOrCreate(
            ['user_id' => $user->id],
            ['last_seen_audit_log_id' => $lastSeen]
        );

        return response()->json(['ok' => true]);
    }
}

