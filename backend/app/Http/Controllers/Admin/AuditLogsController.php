<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogsController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('actor:id,name,email')
            ->latest();

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->input('action') . '%');
        }

        if ($request->filled('actor_id')) {
            $query->where('actor_user_id', $request->input('actor_id'));
        }

        $logs = $query->paginate(50)->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }
}
