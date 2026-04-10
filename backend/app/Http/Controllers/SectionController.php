<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $doc = $request->query('doc', 'zanupf');
        $doc = in_array($doc, ['zanupf', 'zimbabwe', 'amendment3']) ? $doc : 'zanupf';

        $term = '%' . addcslashes($q, '%_\\') . '%';

        $sections = Section::query()
            ->where('is_active', true)
            ->whereHas('chapter', fn ($q) => $q->where('constitution_slug', $doc))
            ->where(function ($query) use ($term) {
                $query->where('title', 'like', $term)
                    ->orWhere('logical_number', 'like', $term)
                    ->orWhere('slug', 'like', $term)
                    ->orWhereHas('currentVersion', function ($q) use ($term) {
                        $q->where('body', 'like', $term);
                    });
            })
            ->with(['chapter.part', 'currentVersion:id,section_id,body'])
            ->orderBy('order')
            ->limit(50)
            ->get()
            ->map(function (Section $s) use ($q) {
                $body = $s->currentVersion?->body ?? '';
                $snippet = $this->extractSnippet($body, $q);
                return [
                    'id' => $s->id,
                    'title' => $s->title,
                    'logical_number' => $s->logical_number,
                    'slug' => $s->slug,
                    'snippet' => $snippet,
                    'chapter' => $s->chapter ? ['id' => $s->chapter->id, 'title' => $s->chapter->title] : null,
                    'part' => $s->chapter?->part ? ['id' => $s->chapter->part->id, 'title' => $s->chapter->part->title] : null,
                ];
            });

        return response()->json($sections);
    }

    private function extractSnippet(string $body, string $term, int $len = 120): ?string
    {
        $term = preg_quote($term, '/');
        if (preg_match('/(.{0,60})(' . $term . ')(.{0,60})/iu', $body, $m)) {
            return trim($m[1] ? '...' . $m[1] : '') . ($m[2] ?? '') . trim(($m[3] ?? '') ? $m[3] . '...' : '');
        }
        $trimmed = mb_substr(trim($body), 0, $len);
        return $trimmed ? $trimmed . '...' : null;
    }

    public function show(Section $section)
    {
        $section->load([
            'chapter.part',
            'currentVersion.summaries' => function ($query) {
                $query->where('status', 'published');
            },
            'aliases',
        ]);

        if ($section->chapter?->constitution_slug === 'amendment3') {
            $section->load(['amendmentClauseRelations' => fn ($q) => $q->with('zimbabweSection:id,logical_number,title,slug')]);
        }

        return response()->json($section);
    }
}
