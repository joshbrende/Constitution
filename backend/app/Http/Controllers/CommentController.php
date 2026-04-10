<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Section $section): JsonResponse
    {
        $comments = SectionComment::where('section_id', $section->id)
            ->with('user:id,name,surname')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'body' => $c->body,
                'created_at' => $c->created_at->toIso8601String(),
                'user' => $c->user ? [
                    'name' => trim($c->user->name . ' ' . $c->user->surname),
                ] : null,
            ]);

        return response()->json($comments);
    }

    public function store(Request $request, Section $section): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->authorize('comment', $section);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment = SectionComment::create([
            'section_id' => $section->id,
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        $comment->load('user:id,name,surname');

        return response()->json([
            'id' => $comment->id,
            'body' => $comment->body,
            'created_at' => $comment->created_at->toIso8601String(),
            'user' => ['name' => trim($comment->user->name . ' ' . $comment->user->surname)],
        ], 201);
    }
}
