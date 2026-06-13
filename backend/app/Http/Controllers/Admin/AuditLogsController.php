<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditArchiveService;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogsController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected AuditArchiveService $archiveService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('admin.section', 'audit_logs');

        $query = $this->filteredQuery($request);

        $this->auditLogger->log(
            action: 'audit_logs.viewed',
            targetType: AuditLog::class,
            targetId: null,
            metadata: [
                'filters' => [
                    'action' => $request->input('action'),
                    'actor_id' => $request->input('actor_id'),
                ],
            ],
            request: $request
        );

        $logs = $query->paginate(50)->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('admin.section', 'audit_logs');

        $query = $this->filteredQuery($request);
        $result = $this->archiveService->exportQueryToJsonl($query, 'admin-export');

        $this->auditLogger->log(
            action: 'audit_logs.exported',
            targetType: AuditLog::class,
            metadata: [
                'filters' => [
                    'action' => $request->input('action'),
                    'actor_id' => $request->input('actor_id'),
                    'from' => $request->input('from'),
                    'to' => $request->input('to'),
                ],
                'row_count' => $result['count'],
                'archive_path' => $result['path'],
            ],
            request: $request
        );

        return Storage::disk('local')->download(
            $result['path'],
            basename($result['path']),
            ['Content-Type' => 'application/x-ndjson']
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<AuditLog>
     */
    private function filteredQuery(Request $request)
    {
        $query = AuditLog::with('actor:id,name,email')->latest();

        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->input('action').'%');
        }

        if ($request->filled('actor_id')) {
            $query->where('actor_user_id', $request->input('actor_id'));
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', Carbon::parse((string) $request->input('from'))->startOfDay());
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', Carbon::parse((string) $request->input('to'))->endOfDay());
        }

        return $query;
    }
}
