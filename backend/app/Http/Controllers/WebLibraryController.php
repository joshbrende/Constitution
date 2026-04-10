<?php

namespace App\Http\Controllers;

use App\Models\LibraryCategory;
use App\Models\LibraryDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WebLibraryController extends Controller
{
    /**
     * Public Digital Library page (dashboard sidebar "Digital Library").
     * Shows categories and documents the current user can access.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $categories = LibraryCategory::whereNull('parent_id')
            ->withCount(['documents' => fn ($q) => $q->whereNotNull('published_at')->where('published_at', '<=', now())])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $categoryId = $request->integer('category_id') ?: null;
        $query = LibraryDocument::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('category:id,name,slug');

        if ($categoryId) {
            $query->where('library_category_id', $categoryId);
        }

        $documents = $query->orderByDesc('published_at')->get();

        $documents = $documents->filter(
            fn (LibraryDocument $doc) => Gate::forUser($user)->allows('view', $doc)
        )->values();

        $canManage = $user?->can('admin.contentManage') ?? false;

        return view('sections.library', compact('categories', 'documents', 'categoryId', 'canManage'));
    }

    /**
     * Show a single document (web read view).
     */
    public function show(Request $request, LibraryDocument $document): View
    {
        if (! $document->isPublished()) {
            abort(404);
        }

        $this->authorize('view', $document);

        $document->load('category:id,name,slug');

        return view('sections.library-document', compact('document'));
    }
}
