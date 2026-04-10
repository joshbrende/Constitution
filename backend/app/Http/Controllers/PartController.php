<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Part;
use Illuminate\Http\Request;

class PartController extends Controller
{
    public function index(Request $request)
    {
        $doc = $request->query('doc', 'zanupf');
        $doc = in_array($doc, ['zanupf', 'zimbabwe', 'amendment3']) ? $doc : 'zanupf';

        $parts = Part::with(['chapters' => fn ($q) => $q->where('constitution_slug', $doc)->orderBy('order')->with(['sections' => fn ($q) => $q->orderBy('order')])])->orderBy('order')->get();
        $parts = $parts->filter(fn ($p) => $p->chapters->isNotEmpty());
        $parts = $this->normalizeStructure($parts);

        // Seeder creates chapters with part_id=null but no parts; return them as a virtual part
        if ($parts->isEmpty()) {
            $chapters = Chapter::with(['sections' => fn ($q) => $q->orderBy('order')])
                ->whereNull('part_id')
                ->where('constitution_slug', $doc)
                ->orderBy('order')
                ->get();
            if ($chapters->isNotEmpty()) {
                $chapters = $this->dedupeChapters($chapters);
                $title = match ($doc) {
                    'zimbabwe' => 'Constitution of Zimbabwe',
                    'amendment3' => (string) config('constitution.amendment3_chapter_title'),
                    default => 'The Constitution',
                };
                $parts = collect([['id' => 0, 'title' => $title, 'number' => null, 'order' => 0, 'constitution_slug' => $doc, 'chapters' => $chapters]]);
            }
        }

        return response()->json($parts->values());
    }

    private function normalizeStructure($parts)
    {
        return $parts->map(function ($part) {
            $part->setRelation('chapters', $this->dedupeChapters($part->chapters));
            return $part;
        });
    }

    private function dedupeChapters($chapters)
    {
        return $chapters
            ->unique(fn ($chapter) => mb_strtolower(trim((string) $chapter->number . '|' . (string) $chapter->title)))
            ->values()
            ->map(function ($chapter) {
                $sections = $chapter->sections
                    ->unique(fn ($section) => mb_strtolower(trim((string) $section->logical_number . '|' . (string) $section->title)))
                    ->values();
                $chapter->setRelation('sections', $sections);
                return $chapter;
            });
    }
}
