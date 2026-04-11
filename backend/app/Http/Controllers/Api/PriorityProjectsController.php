<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriorityProject;
use App\Models\PriorityProjectLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PriorityProjectsController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $projects = PriorityProject::published()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        $likedIds = [];
        if ($user) {
            $likedIds = PriorityProjectLike::where('user_id', $user->id)
                ->whereIn('priority_project_id', $projects->pluck('id'))
                ->pluck('priority_project_id')
                ->all();
        }

        $data = $projects->map(function (PriorityProject $p) use ($likedIds) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'summary' => $p->summary,
                'body' => $p->body,
                'image_url' => $p->image_url,
                'likes_count' => $p->likes_count,
                'liked' => in_array($p->id, $likedIds, true),
                'published_at' => $p->published_at?->toIso8601String(),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function like(PriorityProject $priority_project): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user, 401);
        $this->authorize('like', $priority_project);

        $existing = PriorityProjectLike::where('priority_project_id', $priority_project->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $existing) {
            PriorityProjectLike::create([
                'priority_project_id' => $priority_project->id,
                'user_id' => $user->id,
            ]);
            $priority_project->increment('likes_count');
        }

        return response()->json([
            'data' => [
                'id' => $priority_project->id,
                'likes_count' => $priority_project->likes_count,
                'liked' => true,
            ],
        ]);
    }
}

