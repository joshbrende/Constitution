<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryCategory;
use App\Models\LibraryDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(): View
    {
        $categories = LibraryCategory::whereNull('parent_id')
            ->withCount('documents')
            ->orderBy('order')
            ->orderBy('name')
            ->get();
        $documents = LibraryDocument::with('category:id,name,slug')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.library.index', compact('categories', 'documents'));
    }

    // ——— Categories ———
    public function categoriesIndex(): View
    {
        $categories = LibraryCategory::withCount('documents')
            ->with('parent:id,name')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('admin.library.categories', compact('categories'));
    }

    public function categoryCreate(): View
    {
        $parents = LibraryCategory::whereNull('parent_id')->orderBy('order')->orderBy('name')->get();
        return view('admin.library.category-form', ['category' => null, 'parents' => $parents]);
    }

    public function categoryStore(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'library');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:library_categories,slug'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:library_categories,id'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        LibraryCategory::create($data);
        $this->clearLibraryCache();
        return redirect()->route('admin.library.categories.index')->with('success', 'Category created.');
    }

    public function categoryEdit(LibraryCategory $category): View
    {
        $parents = LibraryCategory::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('order')->orderBy('name')->get();
        return view('admin.library.category-form', ['category' => $category, 'parents' => $parents]);
    }

    public function categoryUpdate(Request $request, LibraryCategory $category): RedirectResponse
    {
        $this->authorize('admin.section', 'library');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:library_categories,slug,' . $category->id],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:library_categories,id'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['parent_id'] = $data['parent_id'] ?? null;
        if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
            $data['parent_id'] = null;
        }
        $category->update($data);
        $this->clearLibraryCache();
        return redirect()->route('admin.library.categories.index')->with('success', 'Category updated.');
    }

    public function categoryDestroy(LibraryCategory $category): RedirectResponse
    {
        $this->authorize('admin.section', 'library');
        $category->delete();
        $this->clearLibraryCache();
        return redirect()->route('admin.library.categories.index')->with('success', 'Category deleted.');
    }

    // ——— Documents ———
    public function documentsIndex(Request $request): View
    {
        $query = LibraryDocument::with('category:id,name,slug');
        if ($request->filled('category_id')) {
            $query->where('library_category_id', $request->integer('category_id'));
        }
        if ($request->filled('type')) {
            $query->where('document_type', $request->input('type'));
        }
        $documents = $query->orderByDesc('published_at')->orderByDesc('created_at')->paginate(20);
        $categories = LibraryCategory::orderBy('order')->orderBy('name')->get();
        $documentTypes = LibraryDocument::documentTypes();

        return view('admin.library.documents', compact('documents', 'categories', 'documentTypes'));
    }

    public function documentCreate(): View
    {
        $categories = LibraryCategory::orderBy('order')->orderBy('name')->get();
        return view('admin.library.document-form', [
            'document' => null,
            'categories' => $categories,
            'documentTypes' => LibraryDocument::documentTypes(),
            'accessRules' => LibraryDocument::accessRules(),
        ]);
    }

    public function documentStore(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'library');
        $data = $this->validateDocument($request);
        $data['created_by'] = $request->user()?->id;
        LibraryDocument::create($data);
        $this->clearLibraryCache();
        return redirect()->route('admin.library.documents.index')->with('success', 'Document created.');
    }

    public function documentEdit(LibraryDocument $document): View
    {
        $categories = LibraryCategory::orderBy('order')->orderBy('name')->get();
        return view('admin.library.document-form', [
            'document' => $document,
            'categories' => $categories,
            'documentTypes' => LibraryDocument::documentTypes(),
            'accessRules' => LibraryDocument::accessRules(),
        ]);
    }

    public function documentUpdate(Request $request, LibraryDocument $document): RedirectResponse
    {
        $this->authorize('admin.section', 'library');
        $data = $this->validateDocument($request, $document);
        $document->update($data);
        $this->clearLibraryCache();
        return redirect()->route('admin.library.documents.index')->with('success', 'Document updated.');
    }

    public function documentDestroy(LibraryDocument $document): RedirectResponse
    {
        $this->authorize('admin.section', 'library');
        $document->delete();
        $this->clearLibraryCache();
        return redirect()->route('admin.library.documents.index')->with('success', 'Document deleted.');
    }

    private function validateDocument(Request $request, ?LibraryDocument $document = null): array
    {
        $slugRule = ['nullable', 'string', 'max:255'];
        if ($document) {
            $slugRule[] = 'unique:library_documents,slug,' . $document->id;
        } else {
            $slugRule[] = 'unique:library_documents,slug';
        }

        $data = $request->validate([
            'library_category_id' => ['nullable', 'exists:library_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => $slugRule,
            'abstract' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'document_type' => ['required', 'string', 'in:' . implode(',', array_keys(LibraryDocument::documentTypes()))],
            'language' => ['required', 'string', 'max:10'],
            'published_at' => ['nullable', 'date'],
            'access_rule' => ['required', 'string', 'in:public,member,leadership'],
            'file_path' => ['nullable', 'string', 'max:500'],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['library_category_id'] = $data['library_category_id'] ?: null;
        if (isset($data['body']) && $data['body'] !== '') {
            $data['body'] = \App\Support\HtmlSanitizer::sanitize($data['body']);
        }
        return $data;
    }

    private function clearLibraryCache(): void
    {
        Cache::forget('library.categories');
    }
}
