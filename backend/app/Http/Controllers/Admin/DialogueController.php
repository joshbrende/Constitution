<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DialogueChannel;
use App\Models\DialogueMessage;
use App\Models\DialogueMessageAttachment;
use App\Models\DialogueThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DialogueController extends Controller
{
    private const MAX_ATTACHMENT_KB = 51200; // 50MB per file

    public function index(): View
    {
        $channels = DialogueChannel::withCount('threads')->orderBy('name')->get();

        return view('admin.dialogue.index', compact('channels'));
    }

    public function threads(DialogueChannel $channel): View
    {
        $threads = $channel->threads()
            ->withCount('messages')
            ->with(['zanupfSection', 'zimbabweSection', 'creator'])
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.dialogue.threads', compact('channel', 'threads'));
    }

    public function showThread(DialogueThread $thread): View
    {
        $thread->load(['channel', 'zanupfSection', 'zimbabweSection', 'creator']);
        $messages = $thread->messages()
            ->with('user')
            ->orderBy('created_at')
            ->paginate(50);

        return view('admin.dialogue.thread-show', compact('thread', 'messages'));
    }

    public function storeThread(Request $request, DialogueChannel $channel): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $thread = $channel->threads()->create([
            'created_by_user_id' => $request->user()->id,
            'title' => $data['title'],
            'zanupf_section_id' => $channel->zanupf_section_id,
            'zimbabwe_section_id' => $channel->zimbabwe_section_id,
            'status' => 'open',
        ]);

        return redirect()->route('admin.dialogue.threads.show', $thread)
            ->with('success', 'Thread created.');
    }

    public function lockThread(DialogueThread $thread): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');
        $thread->update(['status' => 'locked']);

        return back()->with('success', 'Thread locked.');
    }

    public function unlockThread(DialogueThread $thread): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');
        $thread->update(['status' => 'open']);

        return back()->with('success', 'Thread unlocked.');
    }

    public function storeMessage(Request $request, DialogueThread $thread): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');
        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => [
                'file',
                'max:'.self::MAX_ATTACHMENT_KB,
                'mimetypes:' . implode(',', [
                    // images
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'image/gif',
                    // pdf
                    'application/pdf',
                    // audio
                    'audio/mpeg',
                    'audio/mp4',
                    'audio/x-m4a',
                    'audio/aac',
                    'audio/ogg',
                    'audio/webm',
                    // video
                    'video/mp4',
                    'video/webm',
                    'video/quicktime',
                    'video/ogg',
                ]),
            ],
        ]);

        if ($thread->status !== 'open') {
            return back()->with('error', 'Thread is locked.');
        }

        $message = $thread->messages()->create([
            'user_id' => $request->user()->id,
            'body' => trim($data['body']),
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        $files = $request->file('attachments', []);
        if (is_array($files)) {
            foreach ($files as $file) {
                if (! $file) {
                    continue;
                }

                $mime = (string) ($file->getClientMimeType() ?? '');
                $type = $this->inferAttachmentType($mime, (string) $file->getClientOriginalExtension());
                $path = $file->store(
                    'dialogue/thread-'.$thread->id.'/message-'.$message->id,
                    ['disk' => 'public']
                );

                DialogueMessageAttachment::create([
                    'dialogue_message_id' => $message->id,
                    'type' => $type,
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $mime ?: null,
                    'size_bytes' => (int) ($file->getSize() ?? 0),
                ]);
            }
        }

        return back()->with('success', 'Message posted.');
    }

    private function inferAttachmentType(string $mime, string $extension): string
    {
        $m = strtolower(trim($mime));
        if (str_starts_with($m, 'image/')) {
            return 'image';
        }
        if ($m === 'application/pdf') {
            return 'pdf';
        }
        if (str_starts_with($m, 'audio/')) {
            return 'audio';
        }
        if (str_starts_with($m, 'video/')) {
            return 'video';
        }

        $ext = strtolower(trim($extension));
        return in_array($ext, ['pdf'], true) ? 'pdf' : 'other';
    }

    public function pinMessage(DialogueMessage $message): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');
        $message->update(['is_pinned' => true]);

        return back()->with('success', 'Message pinned.');
    }

    public function unpinMessage(DialogueMessage $message): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');
        $message->update(['is_pinned' => false]);

        return back()->with('success', 'Message unpinned.');
    }

    public function destroyMessage(DialogueMessage $message): RedirectResponse
    {
        $this->authorize('admin.section', 'dialogue');
        $message->update(['is_deleted' => true]);

        return back()->with('success', 'Message removed.');
    }
}


