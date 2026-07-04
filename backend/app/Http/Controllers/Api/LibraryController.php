<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LibraryCategory;
use App\Models\LibraryDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

/**
 * @group Public content
 *
 * Digital library categories and documents.
 *
 * @unauthenticated
 */
class LibraryController extends Controller
{
    private const CACHE_TTL = 600; // 10 minutes

    /**
     * List library categories
     *
     * Published top-level categories with document counts. Public; no auth required.
     *
     * @response 200 {"data":[{"id":1,"name":"Party documents","slug":"party-documents","description":"Official party publications","documents_count":12}]}
     */
    public function categories(): JsonResponse
    {
        $categories = Cache::remember('library.categories', self::CACHE_TTL, function () {
            return LibraryCategory::whereNull('parent_id')
                ->withCount(['documents' => fn ($q) => $q->whereNotNull('published_at')->where('published_at', '<=', now())])
                ->orderBy('order')
                ->orderBy('name')
                ->get();
        });

        $list = $categories->map(function (LibraryCategory $cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'description' => $cat->description,
                'documents_count' => $cat->documents_count ?? 0,
            ];
        });

        return response()->json(['data' => $list]);
    }

    /**
     * List library documents
     *
     * Paginated published documents. Role-restricted items are omitted from results.
     * Optional bearer token unlocks member-only documents.
     *
     * @queryParam category_id integer Filter by category ID. Example: 1
     * @queryParam type string Filter by document type. Example: policy
     * @queryParam language string Filter by language code. Example: en
     * @queryParam per_page integer Results per page (max 50). Example: 20
     * @response 200 {"data":[{"id":5,"title":"Code of conduct","slug":"code-of-conduct","abstract":"Member conduct guidelines","document_type":"policy","language":"en","published_at":"2026-01-15T08:00:00+00:00","category":{"id":1,"name":"Party documents","slug":"party-documents"},"has_file":true}],"meta":{"current_page":1,"last_page":1,"per_page":20,"total":1}}
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = LibraryDocument::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('category:id,name,slug');

        if ($request->has('category_id')) {
            $query->where('library_category_id', $request->integer('category_id'));
        }
        if ($request->filled('type')) {
            $query->where('document_type', $request->input('type'));
        }
        if ($request->filled('language')) {
            $query->where('language', $request->input('language'));
        }

        $documents = $query->orderByDesc('published_at')->paginate(min(50, $request->integer('per_page', 20)));

        $filtered = $documents->getCollection()->filter(
            fn (LibraryDocument $doc) => Gate::forUser($user)->allows('view', $doc)
        )->values();

        $documents->setCollection($filtered);

        $data = $documents->getCollection()->map(fn (LibraryDocument $doc) => [
            'id' => $doc->id,
            'title' => $doc->title,
            'slug' => $doc->slug,
            'abstract' => $doc->abstract,
            'document_type' => $doc->document_type,
            'language' => $doc->language,
            'published_at' => $doc->published_at?->toIso8601String(),
            'category' => $doc->category ? [
                'id' => $doc->category->id,
                'name' => $doc->category->name,
                'slug' => $doc->category->slug,
            ] : null,
            'has_file' => ! empty($doc->file_path),
        ]);

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ],
        ]);
    }

    /**
     * Show a library document
     *
     * Returns full body text for published documents the caller may access.
     *
     * @urlParam document integer required Document ID. Example: 5
     * @response 200 {"data":{"id":5,"title":"Code of conduct","slug":"code-of-conduct","abstract":"Member conduct guidelines","body":"...","document_type":"policy","language":"en","published_at":"2026-01-15T08:00:00+00:00","category":{"id":1,"name":"Party documents","slug":"party-documents"},"has_file":true}}
     * @response 404 {"message":"Document not found."}
     */
    public function show(LibraryDocument $document): JsonResponse
    {
        if (! $document->isPublished()) {
            return response()->json(['message' => 'Document not found.'], 404);
        }

        $this->authorize('view', $document);

        $document->load('category:id,name,slug');

        return response()->json([
            'data' => [
                'id' => $document->id,
                'title' => $document->title,
                'slug' => $document->slug,
                'abstract' => $document->abstract,
                'body' => $document->body,
                'document_type' => $document->document_type,
                'language' => $document->language,
                'published_at' => $document->published_at?->toIso8601String(),
                'category' => $document->category ? [
                    'id' => $document->category->id,
                    'name' => $document->category->name,
                    'slug' => $document->category->slug,
                ] : null,
                'has_file' => ! empty($document->file_path),
            ],
        ]);
    }
}
