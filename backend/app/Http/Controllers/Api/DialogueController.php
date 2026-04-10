<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DialogueChannel;
use App\Models\DialogueMessage;
use App\Models\DialogueMessageAttachment;
use App\Models\DialogueReport;
use App\Models\DialogueThread;
use App\Models\DialogueThreadRead;
use App\Models\UserBlock;
use App\Services\AuditLogger;
use App\Services\DialogueChannelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DialogueController extends Controller
{
    public function __construct(
        protected DialogueChannelService $channelService,
        protected AuditLogger $auditLogger,
    ) {}

    public function channels(Request $request): JsonResponse
    {
        $data = $this->channelService->channelsForUser($request->user());

        return response()->json(['data' => $data]);
    }

    public function threads(DialogueChannel $channel): JsonResponse
    {
        $threads = $channel->threads()
            ->with(['zanupfSection', 'zimbabweSection', 'creator'])
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        $data = $threads->map(function (DialogueThread $t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'status' => $t->status,
                'creator' => $t->creator?->only(['id', 'name', 'surname']),
                'constitution_links' => [
                    'zanupf' => $t->zanupfSection ? [
                        'section_id' => $t->zanupfSection->id,
                        'title' => $t->zanupfSection->title,
                    ] : null,
                    'zimbabwe' => $t->zimbabweSection ? [
                        'section_id' => $t->zimbabweSection->id,
                        'title' => $t->zimbabweSection->title,
                    ] : null,
                ],
                'created_at' => $t->created_at?->toIso8601String(),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function storeThread(Request $request, DialogueChannel $channel): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);
        $this->authorize('createThread', $channel);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'zanupf_section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'zimbabwe_section_id' => ['nullable', 'integer', 'exists:sections,id'],
        ]);

        $thread = DialogueThread::create([
            'dialogue_channel_id' => $channel->id,
            'created_by_user_id' => $user->id,
            'title' => $data['title'],
            'zanupf_section_id' => $data['zanupf_section_id'] ?? $channel->zanupf_section_id,
            'zimbabwe_section_id' => $data['zimbabwe_section_id'] ?? $channel->zimbabwe_section_id,
            'status' => 'open',
        ]);

        return response()->json(['data' => ['id' => $thread->id]], 201);
    }

    public function messages(Request $request, DialogueThread $thread): JsonResponse
    {
        $blockedIds = [];
        $user = $request->user();
        if ($user) {
            $blockedIds = UserBlock::where('blocker_user_id', $user->id)->pluck('blocked_user_id')->all();
        }

        $messages = $thread->messages()
            ->where('is_deleted', false)
            ->when(count($blockedIds) > 0, fn ($q) => $q->whereNotIn('user_id', $blockedIds))
            ->with(['user', 'attachments'])
            ->orderBy('created_at')
            ->take(200)
            ->get();

        // Mark this thread as read for the current user at the latest message
        if ($user && $messages->isNotEmpty()) {
            $latest = $messages->last();
            DialogueThreadRead::updateOrCreate(
                [
                    'dialogue_thread_id' => $thread->id,
                    'user_id' => $user->id,
                ],
                [
                    'last_read_at' => $latest->created_at ?? now(),
                ]
            );
        }

        $data = $messages->map(function (DialogueMessage $m) {
            return [
                'id' => $m->id,
                'body' => $m->body,
                'user' => $m->user?->only(['id', 'name', 'surname']),
                'created_at' => $m->created_at?->toIso8601String(),
                'attachments' => $m->attachments->map(function (DialogueMessageAttachment $a) {
                    $disk = $a->disk ?: 'public';
                    $url = $disk === 'public'
                        ? Storage::disk('public')->url($a->path)
                        : null;

                    return [
                        'id' => $a->id,
                        'type' => $a->type,
                        'url' => $url,
                        'name' => $a->original_name,
                        'mime' => $a->mime,
                        'size_bytes' => (int) $a->size_bytes,
                    ];
                })->values(),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function storeMessage(Request $request, DialogueThread $thread): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);
        $this->authorize('reply', $thread);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $msg = DialogueMessage::create([
            'dialogue_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => trim($data['body']),
            'is_pinned' => false,
            'is_deleted' => false,
        ]);

        $this->auditLogger->log(
            action: 'dialogue.message_sent',
            targetType: DialogueMessage::class,
            targetId: $msg->id,
            metadata: [
                'user_id' => $user->id,
                'thread_id' => $thread->id,
                'channel_id' => $thread->dialogue_channel_id,
                'thread_title' => $thread->title,
            ],
            request: $request
        );

        return response()->json(['data' => [
            'id' => $msg->id,
            'body' => $msg->body,
            'user' => $msg->user?->only(['id', 'name', 'surname']),
            'created_at' => $msg->created_at?->toIso8601String(),
            'attachments' => [],
        ]], 201);
    }

    public function reportMessage(Request $request, DialogueMessage $message): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $data = $request->validate([
            'reason' => ['required', 'string', Rule::in(['spam', 'harassment', 'hate', 'sexual', 'violence', 'misinformation', 'other'])],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        $threadId = (int) $message->dialogue_thread_id;

        DialogueReport::create([
            'reporter_user_id' => $user->id,
            'reported_user_id' => $message->user_id,
            'dialogue_thread_id' => $threadId ?: null,
            'dialogue_message_id' => $message->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => 'open',
        ]);

        return response()->json(['message' => 'Reported.'], 201);
    }

    public function reportThread(Request $request, DialogueThread $thread): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $data = $request->validate([
            'reason' => ['required', 'string', Rule::in(['spam', 'harassment', 'hate', 'sexual', 'violence', 'misinformation', 'other'])],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        DialogueReport::create([
            'reporter_user_id' => $user->id,
            'reported_user_id' => $thread->created_by_user_id,
            'dialogue_thread_id' => $thread->id,
            'dialogue_message_id' => null,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => 'open',
        ]);

        return response()->json(['message' => 'Reported.'], 201);
    }

    public function blockUser(Request $request, int $userId): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        if ($userId === (int) $user->id) {
            return response()->json(['message' => 'Cannot block yourself.'], 422);
        }

        UserBlock::firstOrCreate([
            'blocker_user_id' => $user->id,
            'blocked_user_id' => $userId,
        ]);

        return response()->json(['message' => 'Blocked.'], 201);
    }

    public function unblockUser(Request $request, int $userId): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        UserBlock::where('blocker_user_id', $user->id)
            ->where('blocked_user_id', $userId)
            ->delete();

        return response()->json(['message' => 'Unblocked.']);
    }
}

