<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriorityProject;
use App\Models\PriorityProjectLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Public content
 *
 * Priority projects listing and likes.
 */
class PriorityProjectsController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $projects = PriorityProject::published()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        $likedIds = $this->likedProjectIdsForUser($user, $projects->pluck('id')->all());

        $data = $projects->map(fn (PriorityProject $p) => $this->formatProject($p, $likedIds));

        return response()->json(['data' => $data]);
    }

    public function show(PriorityProject $priority_project): JsonResponse
    {
        abort_unless($this->isPublished($priority_project), 404);

        $user = Auth::user();
        $likedIds = $this->likedProjectIdsForUser($user, [$priority_project->id]);

        return response()->json([
            'data' => $this->formatProject($priority_project, $likedIds),
        ]);
    }

    public function like(PriorityProject $priority_project): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user, 401);
        abort_unless($this->isPublished($priority_project), 404);
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

    /**
     * @param  list<int>  $projectIds
     * @return list<int>
     */
    private function likedProjectIdsForUser(?\Illuminate\Contracts\Auth\Authenticatable $user, array $projectIds): array
    {
        if (! $user || $projectIds === []) {
            return [];
        }

        return PriorityProjectLike::where('user_id', $user->id)
            ->whereIn('priority_project_id', $projectIds)
            ->pluck('priority_project_id')
            ->all();
    }

    /**
     * @param  list<int>  $likedIds
     * @return array<string, mixed>
     */
    private function formatProject(PriorityProject $project, array $likedIds): array
    {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'summary' => $project->summary,
            'body' => $project->body,
            'image_url' => $project->image_url,
            'likes_count' => $project->likes_count,
            'liked' => in_array($project->id, $likedIds, true),
            'published_at' => $project->published_at?->toIso8601String(),
        ];
    }

    private function isPublished(PriorityProject $project): bool
    {
        return $project->is_published
            && $project->published_at !== null
            && $project->published_at->isPast();
    }
}

