# Course search

Search the course catalog by title, description, or short description.

## Query parameter

- **`q`** — Search term (trimmed). If non-empty, results are filtered with `LIKE %term%` on `title`, `description`, and `short_description`. `%` and `_` in the term are escaped so they match literally.

## Backend (CourseController::index)

- `$search = trim((string) $request->get('q', ''));`
- If `$search` is non-empty: build `$like = '%' . escaped($search) . '%'` (escaping `\`, `%`, `_`) and apply:
  - `$query->where(function ($q) use ($like) {
      $q->where('title','like',$like)
        ->orWhere('description','like',$like)
        ->orWhere('short_description','like',$like);
    });`
- Pagination uses `$query->paginate(12)->withQueryString()` so `q` and `order` are kept in links.

## View (courses/index)

- **Search form:** GET to `route('courses.index')`, `name="q"`, placeholder "Search courses…", value `request('q')`, hidden `name="order"` with `request('order','newest')`, and a Search button. Submitting keeps the current sort.
- **Order form:** hidden `<input name="q" value="{{ request('q') }}">` so changing order preserves the search term.
- **Empty state:** If `request('q')` is set: "No courses match '…'. [Clear search](route('courses.index'))"; otherwise "No courses yet."

## Interaction with order and pagination

- `q` and `order` are preserved when changing sort or moving between paginated pages (via `withQueryString()` and the hidden inputs in the forms).
