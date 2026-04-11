<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\LibraryDocument;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AdminQuickSearchController extends Controller
{
    private const MIN_Q = 2;

    private const PER_GROUP = 5;

    public function __invoke(Request $request): JsonResponse
    {
        $this->authorize('admin.section', 'admin');

        $qRaw = trim((string) $request->query('q', ''));
        if (mb_strlen($qRaw) < self::MIN_Q) {
            return response()->json(['data' => [
                'q' => $qRaw,
                'groups' => [],
            ]]);
        }

        $like = $this->buildLikePattern($qRaw);

        $groups = array_values(array_filter([
            $this->usersGroup($like),
            $this->coursesGroup($like),
            $this->sectionsGroup($like),
            $this->libraryDocumentsGroup($like),
            $this->certificatesGroup($like),
        ]));

        return response()->json([
            'data' => [
                'q' => $qRaw,
                'groups' => $groups,
            ],
        ]);
    }

    private function buildLikePattern(string $qRaw): string
    {
        $q = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $qRaw);

        return "%{$q}%";
    }

    /**
     * @return array{key: string, title: string, items: Collection<int, array<string, mixed>>}|null
     */
    private function usersGroup(string $like): ?array
    {
        $users = User::query()
            ->select(['id', 'name', 'surname', 'email', 'national_id'])
            ->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('surname', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('national_id', 'like', $like);
            })
            ->orderByDesc('id')
            ->limit(self::PER_GROUP)
            ->get()
            ->map(fn (User $u) => [
                'id' => $u->id,
                'label' => trim(($u->name ?? '') . ' ' . ($u->surname ?? '')) ?: ($u->email ?? 'User'),
                'meta' => $u->email,
                'url' => route('admin.users.edit', ['user' => $u->id]),
            ])
            ->values();

        return $this->nonEmptyGroup('users', 'Users', $users);
    }

    /**
     * @return array{key: string, title: string, items: Collection<int, array<string, mixed>>}|null
     */
    private function coursesGroup(string $like): ?array
    {
        $courses = Course::query()
            ->select(['id', 'code', 'title', 'status', 'grants_membership'])
            ->where(function ($sub) use ($like) {
                $sub->where('title', 'like', $like)
                    ->orWhere('code', 'like', $like);
            })
            ->orderByRaw("CASE status WHEN 'published' THEN 0 WHEN 'draft' THEN 1 ELSE 2 END")
            ->orderBy('title')
            ->limit(self::PER_GROUP)
            ->get()
            ->map(fn (Course $c) => [
                'id' => $c->id,
                'label' => $c->title,
                'meta' => trim(($c->code ? $c->code . ' • ' : '') . (string) ($c->status ?? '')),
                'url' => route('admin.academy.courses.edit', ['course' => $c->id]),
            ])
            ->values();

        return $this->nonEmptyGroup('courses', 'Academy courses', $courses);
    }

    /**
     * @return array{key: string, title: string, items: Collection<int, array<string, mixed>>}|null
     */
    private function sectionsGroup(string $like): ?array
    {
        $sections = Section::query()
            ->select(['id', 'logical_number', 'title', 'slug'])
            ->where(function ($sub) use ($like) {
                $sub->where('title', 'like', $like)
                    ->orWhere('logical_number', 'like', $like);
            })
            ->orderBy('id')
            ->limit(self::PER_GROUP)
            ->get()
            ->map(fn (Section $s) => [
                'id' => $s->id,
                'label' => ($s->logical_number ? $s->logical_number . ' ' : '') . $s->title,
                'meta' => 'Constitution section',
                'url' => route('admin.constitution.sections.edit', ['section' => $s->id]),
            ])
            ->values();

        return $this->nonEmptyGroup('sections', 'Constitution sections', $sections);
    }

    /**
     * @return array{key: string, title: string, items: Collection<int, array<string, mixed>>}|null
     */
    private function libraryDocumentsGroup(string $like): ?array
    {
        $docs = LibraryDocument::query()
            ->select(['id', 'title', 'document_type', 'published_at'])
            ->where('title', 'like', $like)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(self::PER_GROUP)
            ->get()
            ->map(fn (LibraryDocument $d) => [
                'id' => $d->id,
                'label' => $d->title,
                'meta' => $d->document_type ? ucfirst((string) $d->document_type) : 'Library document',
                'url' => route('admin.library.documents.edit', ['document' => $d->id]),
            ])
            ->values();

        return $this->nonEmptyGroup('library', 'Library documents', $docs);
    }

    /**
     * @return array{key: string, title: string, items: Collection<int, array<string, mixed>>}|null
     */
    private function certificatesGroup(string $like): ?array
    {
        $certs = Certificate::query()
            ->select(['id', 'certificate_number', 'public_id', 'verification_code', 'issued_at', 'user_id', 'course_id'])
            ->with(['user:id,name,surname,email', 'course:id,title'])
            ->where(function ($sub) use ($like) {
                $sub->where('certificate_number', 'like', $like)
                    ->orWhere('public_id', 'like', $like)
                    ->orWhere('verification_code', 'like', $like);
            })
            ->orderByDesc('issued_at')
            ->limit(self::PER_GROUP)
            ->get()
            ->map(function (Certificate $c) {
                $name = $c->user ? trim(($c->user->name ?? '') . ' ' . ($c->user->surname ?? '')) : null;
                $course = $c->course?->title;
                $meta = trim(($name ? $name . ' • ' : '') . ($course ?: ''));

                return [
                    'id' => $c->id,
                    'label' => (string) ($c->certificate_number ?? 'Certificate'),
                    'meta' => $meta !== '' ? $meta : 'Certificate',
                    'url' => route('admin.certificates.index', [
                        'q' => (string) ($c->certificate_number ?? ''),
                        'search_mode' => 'certificate_number',
                    ]),
                ];
            })
            ->values();

        return $this->nonEmptyGroup('certificates', 'Certificates', $certs);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $items
     * @return array{key: string, title: string, items: Collection<int, array<string, mixed>>}|null
     */
    private function nonEmptyGroup(string $key, string $title, Collection $items): ?array
    {
        if ($items->isEmpty()) {
            return null;
        }

        return [
            'key' => $key,
            'title' => $title,
            'items' => $items,
        ];
    }
}
