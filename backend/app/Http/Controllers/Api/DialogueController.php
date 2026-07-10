<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DialogueChannel;
use App\Models\DialogueMessage;
use App\Models\DialogueReport;
use App\Models\DialogueThread;
use App\Models\DialogueThreadRead;
use App\Models\UserBlock;
use App\Services\AuditLogger;
use App\Services\DialogueChannelService;
use App\Support\DialogueMessagePresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * @group Dialogue
 *
 * Channels, threads, messages, reporting, and user blocks.
 */
class DialogueController extends Controller
{
    public function __construct(
        protected DialogueChannelService $channelService,
        protected AuditLogger $auditLogger,
    ) {}

    /**
     * List dialogue channels
     *
     * Returns channels visible to the authenticated user (role and province scoped).
     *
     * @authenticated
     * @response 200 {"data":[{"id":1,"slug":"national","name":"National dialogue","description":"Party-wide discussion"}]}
     */
    public function channels(Request $request): JsonResponse
    {
        $data = $this->channelService->channelsForUser($request->user());

        return response()->json(['data' => $data]);
    }

    /**
     * List threads in a channel
     *
     * Returns up to 50 most recent threads with constitution section links when present.
     *
     * @authenticated
     * @urlParam channel integer required Dialogue channel ID. Example: 1
     * @response 200 {"data":[{"id":12,"title":"Section 4 discussion","status":"open","creator":{"id":3,"name":"Tariro","surname":"Moyo"},"constitution_links":{"zanupf":{"section_id":42,"title":"Article 4"},"zimbabwe":null},"created_at":"2026-05-01T10:00:00+00:00"}]}
     */
    public function threads(Request $request, DialogueChannel $channel): JsonResponse
    {
        $this->authorize('view', $channel);

        $threads = $channel->threads()
            ->with(['zanupfSection', 'zimbabweSection', 'creator'])
            ->withCount(['messages as messages_count' => fn ($q) => $q->where('is_deleted', false)])
            ->withMax(['messages' => fn ($q) => $q->where('is_deleted', false)], 'created_at')
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        $data = $threads->map(function (DialogueThread $t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'status' => $t->status,
                'messages_count' => (int) ($t->messages_count ?? 0),
                'last_message_at' => $t->messages_max_created_at
                    ? Carbon::parse($t->messages_max_created_at)->toIso8601String()
                    : null,
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

    /**
     * Create a thread
     *
     * Opens a new discussion thread in the given channel.
     *
     * @authenticated
     * @urlParam channel integer required Dialogue channel ID. Example: 1
     * @bodyParam title string required Thread title. Example: Clarification on Article 12
     * @bodyParam zanupf_section_id integer optional ZANU PF constitution section to link. Example: 42
     * @bodyParam zimbabwe_section_id integer optional Zimbabwe constitution section to link. Example: 18
     * @response 201 {"data":{"id":99}}
     */
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

    /**
     * List messages in a thread
     *
     * Returns up to 200 messages (newest blocked users filtered out). Marks the thread read for the caller.
     *
     * @authenticated
     * @urlParam thread integer required Dialogue thread ID. Example: 12
     * @response 200 {"data":[{"id":501,"body":"Welcome to the discussion.","user":{"id":3,"name":"Tariro","surname":"Moyo"},"created_at":"2026-05-01T10:05:00+00:00","attachments":[]}]}
     */
    public function messages(Request $request, DialogueThread $thread): JsonResponse
    {
        $this->authorize('view', $thread);

        $blockedIds = [];
        $user = $request->user();
        if ($user) {
            $blockedIds = UserBlock::where('blocker_user_id', $user->id)->pluck('blocked_user_id')->all();
        }

        $since = $request->query('since');
        $sinceAt = null;
        if (is_string($since) && $since !== '') {
            try {
                $sinceAt = Carbon::parse($since);
            } catch (\Throwable) {
                return response()->json(['message' => 'Invalid since timestamp.'], 422);
            }
        }

        $messagesQuery = $thread->messages()
            ->when(count($blockedIds) > 0, fn ($q) => $q->whereNotIn('user_id', $blockedIds))
            ->with(['user', 'attachments'])
            ->when($sinceAt, fn ($q) => $q->where('updated_at', '>', $sinceAt))
            ->orderByDesc('is_pinned')
            ->orderBy('created_at');

        if ($sinceAt) {
            $messages = $messagesQuery->get();
        } else {
            $messages = $messagesQuery->take(200)->get();
        }

        // Mark this thread as read for the current user at the latest visible message
        if ($user && $messages->isNotEmpty() && ! $sinceAt) {
            $latest = $messages->sortBy('created_at')->last();
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

        $data = $messages->map(fn (DialogueMessage $m) => DialogueMessagePresenter::toArray($m))->values();

        return response()->json(['data' => $data]);
    }

    /**
     * Post a message
     *
     * Adds a reply to the thread. HTML tags are stripped from the body.
     *
     * @authenticated
     * @urlParam thread integer required Dialogue thread ID. Example: 12
     * @bodyParam body string required Message text (max 4000 chars). Example: Thank you for the clarification.
     * @response 201 {"data":{"id":502,"body":"Thank you for the clarification.","user":{"id":3,"name":"Tariro","surname":"Moyo"},"created_at":"2026-05-01T10:06:00+00:00","attachments":[]}}
     */
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
            'body' => strip_tags(trim($data['body'])),
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

        return response()->json(['data' => DialogueMessagePresenter::toArray($msg->load('user'))], 201);
    }

    /**
     * Report a message
     *
     * @authenticated
     * @urlParam message integer required Message ID. Example: 501
     * @bodyParam reason string required One of: spam, harassment, hate, sexual, violence, misinformation, other. Example: spam
     * @bodyParam details string optional Extra context (max 1000 chars). Example: Repeated promotional links
     * @response 201 {"message":"Reported."}
     */
    public function reportMessage(Request $request, DialogueMessage $message): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $message->loadMissing('thread.channel');
        abort_unless($message->thread, 404);
        $this->authorize('view', $message->thread);

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

    /**
     * Report a thread
     *
     * @authenticated
     * @urlParam thread integer required Thread ID. Example: 12
     * @bodyParam reason string required One of: spam, harassment, hate, sexual, violence, misinformation, other. Example: harassment
     * @bodyParam details string optional Extra context. Example: Off-topic personal attacks
     * @response 201 {"message":"Reported."}
     */
    public function reportThread(Request $request, DialogueThread $thread): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $this->authorize('view', $thread);

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

    /**
     * Block a user
     *
     * Hides the blocked user's messages in dialogue views for the caller.
     *
     * @authenticated
     * @urlParam userId integer required User ID to block. Example: 7
     * @response 201 {"message":"Blocked."}
     * @response 422 {"message":"Cannot block yourself."}
     */
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

    /**
     * Unblock a user
     *
     * @authenticated
     * @urlParam userId integer required User ID to unblock. Example: 7
     * @response 200 {"message":"Unblocked."}
     */
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

