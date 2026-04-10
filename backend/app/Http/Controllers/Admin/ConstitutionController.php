<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Part;
use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Services\AmendmentOfficialPdfService;

class ConstitutionController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    /** @return list<string> */
    private function actorRoleSlugs(): array
    {
        $user = auth()->user();
        if (! $user) {
            return [];
        }

        return $user->roles->pluck('slug')->map(fn ($s) => (string) $s)->values()->all();
    }

    public function index(): View
    {
        $parts = Part::with(['chapters.sections'])->orderBy('order')->get();
        $pendingCount = SectionVersion::where('status', 'in_review')->count();
        $amendmentChapter = Chapter::where('constitution_slug', 'amendment3')->whereNull('part_id')->first();
        $amendmentOfficialPdfAvailable = AmendmentOfficialPdfService::exists();

        return view('admin.constitution.index', compact('parts', 'pendingCount', 'amendmentChapter', 'amendmentOfficialPdfAvailable'));
    }

    public function uploadAmendmentOfficialPdf(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $disk = AmendmentOfficialPdfService::disk();
        $path = AmendmentOfficialPdfService::path();
        Storage::disk($disk)->put($path, file_get_contents($request->file('pdf')->getRealPath()));

        $amendmentChapterId = Chapter::where('constitution_slug', 'amendment3')->whereNull('part_id')->value('id');

        $this->auditLogger->log(
            action: 'constitution.amendment_official_pdf_uploaded',
            targetType: Chapter::class,
            targetId: $amendmentChapterId,
            metadata: [
                'path' => $path,
                'original_name' => $request->file('pdf')->getClientOriginalName(),
            ],
            request: $request
        );

        return redirect()->route('admin.constitution.index')->with('success', 'Official Amendment Bill PDF updated. Mobile apps will receive the new download link.');
    }

    public function partsIndex(): View
    {
        $parts = Part::withCount('chapters')->orderBy('order')->get();
        return view('admin.constitution.parts', compact('parts'));
    }

    public function partEdit(Part $part): View
    {
        return view('admin.constitution.part-edit', compact('part'));
    }

    public function partStore(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        $data = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['order'] ??= Part::max('order') + 1;
        Part::create($data);
        return redirect()->route('admin.constitution.parts')->with('success', 'Part created.');
    }

    public function partUpdate(Request $request, Part $part): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        $data = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);
        $part->update($data);
        return redirect()->route('admin.constitution.parts')->with('success', 'Part updated.');
    }

    public function partDestroy(Part $part): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        if ($part->chapters()->exists()) {
            return redirect()->route('admin.constitution.parts')->with('error', 'Cannot delete part with chapters.');
        }
        $part->delete();
        return redirect()->route('admin.constitution.parts')->with('success', 'Part deleted.');
    }

    public function chaptersIndex(Part $part): View
    {
        $chapters = $part->chapters()->withCount('sections')->orderBy('order')->get();
        return view('admin.constitution.chapters', compact('part', 'chapters'));
    }

    public function chapterEdit(Chapter $chapter): View
    {
        $part = $chapter->part;
        return view('admin.constitution.chapter-edit', compact('chapter', 'part'));
    }

    public function chapterStore(Request $request, Part $part): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        $data = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['part_id'] = $part->id;
        $data['order'] ??= $part->chapters()->max('order') + 1;
        Chapter::create($data);
        return back()->with('success', 'Chapter created.');
    }

    public function chapterUpdate(Request $request, Chapter $chapter): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        $data = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);
        $chapter->update($data);
        return back()->with('success', 'Chapter updated.');
    }

    public function chapterDestroy(Chapter $chapter): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        if ($chapter->sections()->exists()) {
            return back()->with('error', 'Cannot delete chapter with sections.');
        }
        $chapter->delete();
        return back()->with('success', 'Chapter deleted.');
    }

    public function sectionsIndex(Chapter $chapter): View
    {
        $sections = $chapter->sections()->with('versions')->orderBy('order')->get();
        return view('admin.constitution.sections', compact('chapter', 'sections'));
    }

    public function sectionEdit(Section $section): View
    {
        $chapter = $section->chapter;
        $currentVersion = $section->versions()
            ->whereNull('effective_to')
            ->where('status', 'published')
            ->latest('effective_from')
            ->first();
        $draftVersion = $section->versions()->where('status', 'draft')->first();
        $bodySource = $draftVersion ?? $currentVersion;
        $canPublishNow = auth()->user()?->can('admin.presidiumPublish') ?? false;
        return view('admin.constitution.section-edit', compact('section', 'chapter', 'bodySource', 'canPublishNow'));
    }

    public function sectionStore(Request $request, Chapter $chapter): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        $data = $request->validate([
            'logical_number' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);
        $order = $data['order'] ?? ($chapter->sections()->max('order') + 1);
        $slug = \Illuminate\Support\Str::slug($data['logical_number'] . '-' . $data['title']);
        $slug = Section::where('slug', $slug)->exists() ? $slug . '-' . uniqid() : $slug;

        $section = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => $data['logical_number'],
            'slug' => $slug,
            'title' => $data['title'],
            'order' => $order,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $section->id,
            'version_number' => 1,
            'body' => $data['body'],
            'status' => 'draft',
        ]);

        return back()->with('success', 'Section created as draft.');
    }

    public function sectionUpdate(Request $request, Section $section): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        $data = $request->validate([
            'logical_number' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'body' => ['nullable', 'string'],
            'publish_now' => ['boolean'],
        ]);
        $section->update([
            'logical_number' => $data['logical_number'],
            'title' => $data['title'],
            'order' => $data['order'] ?? $section->order,
            'is_active' => (bool) ($data['is_active'] ?? $section->is_active),
        ]);

        if (!empty($data['body'])) {
            $draft = $section->versions()->where('status', 'draft')->first();
            $publishNow = ! empty($data['publish_now'])
                && (auth()->user()?->can('admin.presidiumPublish') ?? false);

            if ($draft) {
                $draft->update(['body' => $data['body']]);
                if ($publishNow) {
                    $previousPublished = $section->versions()
                        ->where('status', 'published')
                        ->whereNull('effective_to')
                        ->first();
                    if ($previousPublished) {
                        $previousPublished->update(['effective_to' => now()->toDateString()]);
                    }
                    $draft->update([
                        'status' => 'published',
                        'effective_from' => now()->toDateString(),
                        'effective_to' => null,
                    ]);
                    $draft->refresh();
                    $this->auditLogger->log(
                        action: 'constitution.section_published_direct',
                        targetType: SectionVersion::class,
                        targetId: $draft->id,
                        metadata: [
                            'section_id' => $section->id,
                            'version_number' => $draft->version_number,
                            'workflow_channel' => 'direct_publish',
                            'presidium_review_bypassed' => true,
                            'note' => 'Published from section editor with Publish now (Presidium/System Admin only).',
                            'actor_roles' => $this->actorRoleSlugs(),
                        ],
                        request: $request
                    );

                    return back()->with('success', 'Body updated and published.');
                }
                return back()->with('success', 'Body updated. Draft saved. Go to Amendments to submit for approval.');
            }

            $nextNum = $section->versions()->max('version_number') + 1;
            $newVersion = SectionVersion::create([
                'section_id' => $section->id,
                'version_number' => $nextNum,
                'body' => $data['body'],
                'status' => $publishNow ? 'published' : 'draft',
                'effective_from' => $publishNow ? now()->toDateString() : null,
                'effective_to' => null,
            ]);

            if ($publishNow) {
                $previousPublished = $section->versions()
                    ->where('status', 'published')
                    ->where('id', '!=', $newVersion->id)
                    ->whereNull('effective_to')
                    ->first();
                if ($previousPublished) {
                    $previousPublished->update(['effective_to' => now()->toDateString()]);
                }
                $this->auditLogger->log(
                    action: 'constitution.section_published_direct',
                    targetType: SectionVersion::class,
                    targetId: $newVersion->id,
                    metadata: [
                        'section_id' => $section->id,
                        'version_number' => $newVersion->version_number,
                        'workflow_channel' => 'direct_publish',
                        'presidium_review_bypassed' => true,
                        'note' => 'New version published immediately from section editor (Presidium/System Admin only).',
                        'actor_roles' => $this->actorRoleSlugs(),
                    ],
                    request: $request
                );

                return back()->with('success', 'Body updated and published.');
            }
            return back()->with('success', 'Body saved as draft. Go to Amendments to submit for approval.');
        }

        return back()->with('success', 'Section metadata updated.');
    }

    public function sectionDestroy(Section $section): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        $section->delete();
        return back()->with('success', 'Section deleted.');
    }

    public function versionsIndex(Section $section): View
    {
        $versions = $section->versions()->orderByDesc('version_number')->get();
        $canApprove = auth()->user()?->can('admin.presidiumPublish') ?? false;
        return view('admin.constitution.versions', compact('section', 'versions', 'canApprove'));
    }

    public function versionCreate(Section $section): View
    {
        $latest = $section->versions()->orderByDesc('version_number')->first();
        return view('admin.constitution.version-form', ['section' => $section, 'version' => null, 'latest' => $latest]);
    }

    public function versionStore(Request $request, Section $section): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        $data = $request->validate([
            'body' => ['required', 'string'],
            'law_reference' => ['nullable', 'string', 'max:255'],
        ]);
        $nextNum = $section->versions()->max('version_number') + 1;
        SectionVersion::create([
            'section_id' => $section->id,
            'version_number' => $nextNum,
            'body' => $data['body'],
            'law_reference' => $data['law_reference'] ?? null,
            'status' => 'draft',
        ]);
        return redirect()->route('admin.constitution.sections.versions', $section)
            ->with('success', 'New version created as draft.');
    }

    public function versionEdit(SectionVersion $sectionVersion): View
    {
        $section = $sectionVersion->section;
        if ($sectionVersion->status !== 'draft') {
            abort(403, 'Only draft versions can be edited.');
        }
        return view('admin.constitution.version-form', [
            'section' => $section,
            'version' => $sectionVersion,
            'latest' => $sectionVersion,
        ]);
    }

    public function versionUpdate(Request $request, SectionVersion $sectionVersion): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        if ($sectionVersion->status !== 'draft') {
            abort(403, 'Only draft versions can be edited.');
        }
        $data = $request->validate([
            'body' => ['required', 'string'],
            'law_reference' => ['nullable', 'string', 'max:255'],
        ]);
        $sectionVersion->update($data);
        return redirect()->route('admin.constitution.sections.versions', $sectionVersion->section)
            ->with('success', 'Draft updated.');
    }

    public function versionSubmitForApproval(SectionVersion $sectionVersion): RedirectResponse
    {
        $this->authorize('admin.section', 'constitution');
        if ($sectionVersion->status !== 'draft') {
            return back()->with('error', 'Only draft versions can be submitted.');
        }
        $sectionVersion->update(['status' => 'in_review']);
        $this->auditLogger->log(
            action: 'constitution.version_submitted_for_review',
            targetType: SectionVersion::class,
            targetId: $sectionVersion->id,
            metadata: [
                'section_id' => $sectionVersion->section_id,
                'version_number' => $sectionVersion->version_number,
                'workflow_channel' => 'presidium_review',
                'status_after' => 'in_review',
                'actor_roles' => $this->actorRoleSlugs(),
            ],
            request: request()
        );

        return back()->with('success', 'Submitted for Presidium approval.');
    }

    public function versionApprove(SectionVersion $sectionVersion): RedirectResponse
    {
        $this->authorize('admin.presidiumPublish');
        if ($sectionVersion->status !== 'in_review') {
            return back()->with('error', 'Only versions awaiting approval can be approved.');
        }

        $section = $sectionVersion->section;
        $previousPublished = $section->versions()
            ->where('status', 'published')
            ->whereNull('effective_to')
            ->first();

        if ($previousPublished) {
            $previousPublished->update(['effective_to' => now()->toDateString()]);
        }

        $sectionVersion->update([
            'status' => 'published',
            'effective_from' => now()->toDateString(),
            'effective_to' => null,
        ]);

        $this->auditLogger->log(
            action: 'constitution.version_approved',
            targetType: SectionVersion::class,
            targetId: $sectionVersion->id,
            metadata: [
                'section_id' => $sectionVersion->section_id,
                'version_number' => $sectionVersion->version_number,
                'workflow_channel' => 'presidium_review',
                'presidium_review_bypassed' => false,
                'status_before' => 'in_review',
                'status_after' => 'published',
                'actor_roles' => $this->actorRoleSlugs(),
            ],
            request: request()
        );

        return back()->with('success', 'Amendment approved and published.');
    }

    public function versionReject(SectionVersion $sectionVersion, Request $request): RedirectResponse
    {
        $this->authorize('admin.presidiumPublish');
        if ($sectionVersion->status !== 'in_review') {
            return back()->with('error', 'Only versions in review can be rejected.');
        }
        $sectionVersion->update(['status' => 'draft']);
        $this->auditLogger->log(
            action: 'constitution.version_rejected_to_draft',
            targetType: SectionVersion::class,
            targetId: $sectionVersion->id,
            metadata: [
                'section_id' => $sectionVersion->section_id,
                'version_number' => $sectionVersion->version_number,
                'workflow_channel' => 'presidium_review',
                'status_before' => 'in_review',
                'status_after' => 'draft',
                'actor_roles' => $this->actorRoleSlugs(),
            ],
            request: $request
        );

        return back()->with('success', 'Amendment returned to draft.');
    }
}
