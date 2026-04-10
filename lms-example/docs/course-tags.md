# Course categories / tags

Tags categorize courses. Admins manage tags; instructors/facilitators assign tags when creating or editing courses. The course catalog supports filtering by tag; `tag`, `q`, and `order` are preserved together.

## Migration

Run `php artisan migrate` to create:

- **`tags`** — `id`, `name`, `slug` (unique, max 100), `timestamps`
- **`course_tag`** — `course_id`, `tag_id`; composite primary key; FKs with `cascadeOnDelete`

## Models

- **`Tag`** — fillable: `name`, `slug`. `courses()`: `belongsToMany(Course::class, 'course_tag')`.
- **`Course`** — `tags()`: `belongsToMany(Tag::class, 'course_tag')`.

## Admin CRUD: tags

**Routes** (auth, `prefix('admin')`, `name('admin.')`):  
`admin.tags.index`, `create`, `store`, `edit`, `update`.

- **index** — `Tag::withCount('courses')->orderBy('name')`; list with “Create tag”, Edit, and course count.
- **create / store** — `name` (required), `slug` (optional). If slug is empty, `uniqueSlug(Str::slug($name))` generates a unique slug (e.g. `foo`, `foo-2`).
- **edit / update** — same fields; `uniqueSlug(..., $tag->id)` excludes the current tag when checking uniqueness.

`TagController::ensureAdmin()` aborts 403 for non-admins.

**Views:** `admin/tags/index`, `create`, `edit`, `_form` (name, slug). Admin layout: “Tags” link → `admin.tags.index`.

## Course form

`courses/_form`: “Tags” section with checkboxes `name="tags[]"` for each tag.  
`$tagIds = old('tags', ($course?->tags ?? collect())->pluck('id')->toArray())`.  
If no tags exist: “No tags yet. Create tags in Admin” with link to `admin.tags.index`.

`CourseController::create` and `edit` pass `$tags = Tag::orderBy('name')->get()`; `store` and `update` validate `tags` (nullable|array, `tags.*` exists:tags,id) and, after create/update, call `$course->tags()->sync($request->input('tags', []))`.

## Course catalog filter

`courses/index`: “Filter by tag” row with “All” and one badge per tag. Links to `courses.index` with `tag` (slug), `q`, and `order` preserved.

- **CourseController::index** — `request('tag')` → `whereHas('tags', fn($q) => $q->where('tags.slug', $tagSlug))`. Passes `$tags = Tag::orderBy('name')->get()`.

Search and order forms include `<input type="hidden" name="tag" value="{{ request('tag') }}">` so tag filter is kept when searching or changing order.

**Empty state:** If `request('q')` or `request('tag')` and no results: “No courses match … Clear filters” with link to `courses.index` (no params).
