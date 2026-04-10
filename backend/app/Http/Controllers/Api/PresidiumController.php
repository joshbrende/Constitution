<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PresidiumMember;
use App\Models\PresidiumPublication;
use Illuminate\Http\JsonResponse;

class PresidiumController extends Controller
{
    public function index(): JsonResponse
    {
        $members = PresidiumMember::published()
            ->ordered()
            ->with(['zanupfSection.chapter', 'zimbabweSection.chapter'])
            ->get();

        $data = $members->map(function (PresidiumMember $m) {
            $zanupf = $m->zanupfSection;
            $zimbabwe = $m->zimbabweSection;

            return [
                'id' => $m->id,
                'name' => $m->name,
                'title' => $m->title,
                'role_slug' => $m->role_slug,
                'photo_url' => $m->photo_url,
                'bio' => $m->bio,
                'order' => $m->order,
                'links' => [
                    'zanupf' => $zanupf ? [
                        'section_id' => $zanupf->id,
                        'title' => $zanupf->title,
                        'chapter' => $zanupf->chapter?->title,
                        'constitution' => 'zanupf',
                    ] : null,
                    'zimbabwe' => $zimbabwe ? [
                        'section_id' => $zimbabwe->id,
                        'title' => $zimbabwe->title,
                        'chapter' => $zimbabwe->chapter?->title,
                        'constitution' => 'zimbabwe',
                    ] : null,
                ],
            ];
        });

        $publications = PresidiumPublication::query()
            ->published()
            ->ordered()
            ->get()
            ->map(fn (PresidiumPublication $p) => [
                'id' => $p->id,
                'slug' => $p->slug,
                'title' => $p->title,
                'author' => $p->author,
                'summary' => $p->summary,
                'cover_url' => $p->cover_url,
                'article_url' => $p->article_url,
                'purchase_url' => $p->purchase_url,
                'online_copy_url' => $p->online_copy_url,
                'is_featured' => (bool) $p->is_featured,
                'order' => (int) $p->order,
            ]);

        return response()->json([
            'data' => [
                'members' => $data,
                'publications' => $publications,
            ],
        ]);
    }
}

