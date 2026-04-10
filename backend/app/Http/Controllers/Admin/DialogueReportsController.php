<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DialogueMessage;
use App\Models\DialogueReport;
use App\Models\DialogueThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DialogueReportsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('admin.section', 'dialogue');

        $status = (string) $request->query('status', 'open');
        if (! in_array($status, ['open', 'reviewed', 'resolved', 'rejected', 'all'], true)) {
            $status = 'open';
        }

        $query = DialogueReport::query()
            ->with([
                'reporter:id,name,surname,email',
                'reportedUser:id,name,surname,email',
                'thread:id,title,status',
                'message:id,dialogue_thread_id,user_id,body,is_deleted,created_at',
            ])
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->paginate(30)->withQueryString();

        return view('admin.dialogue.reports', compact('reports', 'status'));
    }

    public function resolve(Request $request, DialogueReport $report): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');

        $data = $request->validate([
            'status' => ['required', 'string', 'in:reviewed,resolved,rejected'],
            'resolution_action' => ['nullable', 'string', 'max:80'],
        ]);

        $report->update([
            'status' => $data['status'],
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
            'resolution_action' => $data['resolution_action'] ?? null,
        ]);

        return back()->with('success', 'Report updated.');
    }

    public function removeMessage(Request $request, DialogueReport $report): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');

        $message = $report->message;
        if ($message instanceof DialogueMessage) {
            $message->update(['is_deleted' => true]);
        }

        $report->update([
            'status' => 'resolved',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
            'resolution_action' => 'message_removed',
        ]);

        return back()->with('success', 'Message removed and report resolved.');
    }

    public function lockThread(Request $request, DialogueReport $report): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');

        $thread = $report->thread;
        if ($thread instanceof DialogueThread) {
            $thread->update(['status' => 'locked']);
        }

        $report->update([
            'status' => 'resolved',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
            'resolution_action' => 'thread_locked',
        ]);

        return back()->with('success', 'Thread locked and report resolved.');
    }
}

