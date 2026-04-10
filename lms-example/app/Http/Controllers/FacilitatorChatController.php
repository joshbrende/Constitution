<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\FacilitatorChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class FacilitatorChatController extends Controller
{
    /**
     * Standalone Q&A management page for the facilitator (no need to be in learn view).
     */
    public function instructorPage(Course $course): View
    {
        $user = Auth::user();
        if (!$user || !$user->canEditCourse($course)) {
            abort(403, 'Only the facilitator of this course can access Q&A management.');
        }

        return view('facilitator.facilitator-chat', ['course' => $course]);
    }
    /**
     * List Q&A messages for the course (and optional unit).
     * Used by both attendees (learn view) and facilitator (learn or dedicated Q&A page).
     */
    public function index(Request $request, Course $course): JsonResponse
    {
        $this->ensureAccess($course);

        $unitId = $request->query('unit_id') ? (int) $request->query('unit_id') : null;

        $query = FacilitatorChatMessage::where('course_id', $course->id)
            ->with(['user:id,name,surname', 'unit:id,title']);

        if ($unitId !== null) {
            $query->where(function ($q) use ($unitId) {
                $q->where('unit_id', $unitId)->orWhereNull('unit_id');
            });
        }

        $roots = (clone $query)
            ->whereNull('in_reply_to_id')
            ->orderByRaw("CASE type WHEN 'announcement' THEN 0 WHEN 'question' THEN 1 END")
            ->orderByDesc('created_at')
            ->get();

        $replies = (clone $query)
            ->whereNotNull('in_reply_to_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('in_reply_to_id');

        $items = $roots->map(function ($m) use ($replies) {
            return $this->messageToArray($m, $replies->get($m->id, collect()));
        });

        $user = Auth::user();
        $canEdit = $user && $user->canEditCourse($course);

        return response()->json([
            'items' => $items,
            'can_reply' => $canEdit,
            'can_announce' => $canEdit,
            'can_update_status' => $canEdit,
        ]);
    }

    public function store(Request $request, Course $course): JsonResponse
    {
        $this->ensureAccess($course);

        $user = Auth::user();
        $canEdit = $user && $user->canEditCourse($course);

        $rules = [
            'body' => 'required|string|max:4000',
            'type' => 'required|in:question,reply,announcement',
            'in_reply_to_id' => 'nullable|integer|exists:facilitator_chat_messages,id',
            'unit_id' => 'nullable|integer|exists:units,id',
        ];
        $valid = $request->validate($rules);

        $type = $valid['type'];
        $inReplyToId = isset($valid['in_reply_to_id']) ? (int) $valid['in_reply_to_id'] : null;
        $unitId = isset($valid['unit_id']) && $valid['unit_id'] ? (int) $valid['unit_id'] : null;

        if ($type === 'reply') {
            if (!$inReplyToId) {
                return response()->json(['message' => 'in_reply_to_id is required for type=reply'], 422);
            }
            if (!$canEdit) {
                return response()->json(['message' => 'Only the facilitator can reply.'], 403);
            }
            $parent = FacilitatorChatMessage::where('id', $inReplyToId)->where('course_id', $course->id)->firstOrFail();
            $unitId = $parent->unit_id; // inherit unit from question
        } elseif ($type === 'announcement') {
            if (!$canEdit) {
                return response()->json(['message' => 'Only the facilitator can post announcements.'], 403);
            }
            $inReplyToId = null;
        } elseif ($type === 'question') {
            $inReplyToId = null;
            // unit_id from request is ok; if from learn we pass current unit
        }

        if ($unitId && $course->units()->where('id', $unitId)->doesntExist()) {
            return response()->json(['message' => 'Invalid unit for this course.'], 422);
        }

        $m = FacilitatorChatMessage::create([
            'course_id' => $course->id,
            'unit_id' => $unitId,
            'user_id' => $user->id,
            'body' => $valid['body'],
            'type' => $type,
            'in_reply_to_id' => $inReplyToId,
            'status' => $type === 'question' ? 'pending' : null,
        ]);

        $m->load(['user:id,name,surname', 'unit:id,title']);

        if ($type === 'reply' && $inReplyToId && (int) $parent->user_id !== (int) $user->id) {
            $target = \App\Models\User::find($parent->user_id);
            if ($target) {
                $target->notify(new \App\Notifications\QAReplyNotification($m, $parent));
            }
        }

        return response()->json(['item' => $this->messageToArray($m, collect())], 201);
    }

    public function update(Request $request, Course $course, FacilitatorChatMessage $message): JsonResponse
    {
        $this->ensureAccess($course);

        if ($message->course_id !== (int) $course->id) {
            abort(404);
        }

        $user = Auth::user();
        if (!$user || !$user->canEditCourse($course)) {
            return response()->json(['message' => 'Only the facilitator can update status.'], 403);
        }

        if ($message->type !== 'question') {
            return response()->json(['message' => 'Only questions have a status.'], 422);
        }

        $valid = $request->validate(['status' => 'required|in:answered,dismissed']);

        $message->update(['status' => $valid['status']]);

        return response()->json(['item' => $this->messageToArray($message->load(['user:id,name,surname', 'unit:id,title']), $message->replies)]);
    }

    private function ensureAccess(Course $course): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'You must be logged in to access Q&A.');
        }

        $enrolled = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists();
        $canEdit = $user->canEditCourse($course);

        if (!$enrolled && !$canEdit) {
            abort(403, 'You must be enrolled or the facilitator to access Q&A.');
        }
    }

    private function messageToArray(FacilitatorChatMessage $m, $replies): array
    {
        return [
            'id' => $m->id,
            'course_id' => $m->course_id,
            'unit_id' => $m->unit_id,
            'unit_title' => $m->unit?->title,
            'user_id' => $m->user_id,
            'user_name' => trim(($m->user->name ?? '') . ' ' . ($m->user->surname ?? '')),
            'body' => $m->body,
            'type' => $m->type,
            'in_reply_to_id' => $m->in_reply_to_id,
            'status' => $m->status,
            'created_at' => $m->created_at->toIso8601String(),
            'replies' => $replies->map(fn ($r) => $this->messageToArray($r, collect()))->values()->all(),
        ];
    }
}
