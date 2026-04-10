<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Section;
use App\Services\AmendmentOfficialPdfService;
use Illuminate\Http\Request;

class WebConstitutionController extends Controller
{
    public const DOCS = [
        'zanupf' => ['title' => 'Constitution of ZANU PF', 'label' => 'ZANU PF', 'section_label' => 'Article'],
        'zimbabwe' => ['title' => 'Constitution of Zimbabwe', 'label' => 'Zimbabwe', 'section_label' => 'Section'],
        'amendment3' => [
            'title' => '',
            'label' => 'Amendment No. 3',
            'section_label' => 'Clause',
        ],
    ];

    /**
     * Show the constitution reader with navigation.
     * @param  string|null  $doc  'zanupf' | 'zimbabwe'
     */
    public function index(Request $request, ?string $doc = null, ?Section $section = null)
    {
        $doc = $doc ?? 'zanupf';
        if (! array_key_exists($doc, self::DOCS)) {
            abort(404, 'Constitution not found.');
        }

        $docMeta = self::DOCS[$doc];
        if ($doc === 'amendment3') {
            $docMeta['title'] = (string) config('constitution.amendment3_chapter_title');
        }

        $chapters = Chapter::with(['sections' => function ($query) {
            $query->orderBy('order');
        }])
            ->where('constitution_slug', $doc)
            ->orderBy('order')
            ->get();

        if (! $section?->exists || $section->chapter->constitution_slug !== $doc) {
            $firstSection = $chapters->first()?->sections->first();
            $section = $firstSection ?? null;
        }

        $currentVersion = null;

        if ($section) {
            $currentVersion = $section->currentVersion()->first()
                ?? $section->versions()
                    ->where('status', 'published')
                    ->orderByDesc('version_number')
                    ->first();
            if ($doc === 'amendment3') {
                $section->load(['amendmentClauseRelations' => fn ($q) => $q->with('zimbabweSection')]);
            }
        }

        return view('sections.constitution', [
            'doc' => $doc,
            'docMeta' => $docMeta,
            'chapters' => $chapters,
            'activeSection' => $section,
            'activeVersion' => $currentVersion,
            'amendmentOfficialPdfUrl' => $doc === 'amendment3' ? AmendmentOfficialPdfService::urlForRequest($request) : null,
        ]);
    }
}

