<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Note;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class NoteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Note::where('user_id', $user->id)
            ->with(['course', 'unit'])
            ->orderByDesc('updated_at');

        if ($search = trim((string) $request->get('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('body', 'like', '%' . $search . '%')
                    ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', '%' . $search . '%'))
                    ->orWhereHas('unit', fn ($uq) => $uq->where('title', 'like', '%' . $search . '%'));
            });
        }

        $notes = $query->paginate(20)->withQueryString();

        return view('notes.index', [
            'notes' => $notes,
            'search' => $search ?? '',
        ]);
    }

    public function store(Request $request, Course $course, Unit $unit)
    {
        $user = Auth::user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment || $unit->course_id !== $course->id) {
            abort(403);
        }

        $valid = $request->validate([
            'body' => ['nullable', 'string', 'max:20000'],
        ]);

        $body = trim((string) ($valid['body'] ?? ''));

        if ($body === '') {
            // Empty body = delete any existing note for this unit
            Note::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('unit_id', $unit->id)
                ->delete();
        } else {
            Note::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'unit_id' => $unit->id,
                ],
                ['body' => $body]
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'deleted' => $body === '',
                'saved_at' => now()->toIso8601String(),
            ]);
        }

        return redirect()
            ->route('learn.show', ['course' => $course, 'unit' => $unit->id])
            ->with('message', 'Notes saved.');
    }
}

