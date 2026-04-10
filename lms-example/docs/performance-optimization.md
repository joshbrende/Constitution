# Performance Optimization Guide

This document outlines the performance optimizations implemented in the TTM Group LMS.

## Database Optimizations

### Indexes

Performance indexes have been added to commonly queried columns:

**Courses Table:**
- `status` - Filtering published courses
- `instructor_id` - Finding courses by instructor
- `enrollment_count` - Sorting by popularity
- `created_at` - Sorting by newest

**Enrollments Table:**
- Composite index on `(user_id, course_id)` - Fast enrollment lookups
- `status` - Filtering by enrollment status
- `progress_percentage` - Finding completed/in-progress enrollments
- `enrolled_at` - Sorting by enrollment date

**Units Table:**
- `course_id` - Loading course units
- `unit_type` - Filtering by type (lesson, quiz, assignment)
- `order` - Sorting units
- `quiz_id` - Finding quiz units

**Quiz Attempts Table:**
- Composite index on `(user_id, quiz_id)` - Fast attempt lookups
- `status` - Filtering passed/failed attempts
- `completed_at` - Sorting by completion date

**Other Tables:**
- `unit_completions`: Composite index on `(user_id, unit_id)`
- `certificates`: Composite index on `(user_id, course_id)`
- `users`: Index on `points` for leaderboard queries
- `tags`: Index on `slug` for tag filtering

### Query Optimizations

#### Eager Loading

Controllers use eager loading to prevent N+1 queries:

```php
// Before (N+1 problem)
$courses = Course::all();
foreach ($courses as $course) {
    echo $course->instructor->name; // Query per course
}

// After (optimized)
$courses = Course::with('instructor', 'tags')->get();
foreach ($courses as $course) {
    echo $course->instructor->name; // No additional queries
}
```

**Optimized Controllers:**
- `CourseController::index()` - Eager loads `instructor` and `tags`
- `CourseController::show()` - Optimized related data loading
- `LearnController::show()` - Uses collection methods instead of queries
- `LearnerController::dashboard()` - Already uses eager loading

#### Query Consolidation

Multiple queries consolidated where possible:

```php
// Before
$certificate = Certificate::where('user_id', $userId)->where('course_id', $courseId)->first();
$myReview = CourseReview::where('course_id', $courseId)->where('user_id', $userId)->first();

// After - Single query with conditions
if ($enrolled && $enrollment) {
    if ($enrollment->progress_percentage >= 100) {
        $certificate = Certificate::where('user_id', $userId)->where('course_id', $courseId)->first();
    }
    $myReview = CourseReview::where('course_id', $courseId)->where('user_id', $userId)->first();
}
```

## Caching Strategy

### Application Cache

**Configuration:**
- Config cached in production: `php artisan config:cache`
- Routes cached in production: `php artisan route:cache`
- Views cached in production: `php artisan view:cache`

**Cache Helper (`App\Helpers\CacheHelper`):**
- `getTags()` - Cache tags list (24 hours); used in course index, create, edit
- `getCourses()` - Cache courses list (60 minutes); available for future use
- `clearTagsCache()` - Clear tags cache (called by TagController and TagObserver)
- `clearCoursesCache()` - Clear courses cache

**Tag cache invalidation:**
- `TagController::store()` and `TagController::update()` call `CacheHelper::clearTagsCache()`
- `TagObserver` clears tag cache on Tag created, updated, deleted, restored, force deleted

**Artisan command:**
- `php artisan cache:clear-lms` — Clears LMS application cache (tags, courses)
- `php artisan cache:clear-lms --all` — Also runs config:clear, route:clear, view:clear

### Cache Usage

**When to Use:**
- Frequently accessed, rarely changed data (tags, course lists)
- Expensive queries (analytics, reports)
- Static content (help pages, documentation)

**When NOT to Use:**
- User-specific data (enrollments, progress)
- Frequently updated data (notifications, Q&A)
- Real-time data (current user session)

### Cache Clearing

Clear cache when:
- Courses are created/updated/deleted
- Tags are modified
- System configuration changes

```php
use App\Helpers\CacheHelper;

// After course update
CacheHelper::clearCoursesCache();

// After tag update
CacheHelper::clearTagsCache();
```

## Asset Optimization

### Current Setup

- Bootstrap 5.3.2 via CDN (jsDelivr)
- Bootstrap Icons via CDN
- No build process required (using CDN)

### Recommendations for Production

1. **Use CDN** (already implemented):
   - Bootstrap CSS/JS from jsDelivr CDN
   - Faster delivery, browser caching

2. **Image Optimization:**
   - Compress featured images before upload
   - Use WebP format where possible
   - Lazy load images below the fold

3. **Minification:**
   - Minify custom CSS/JS if added
   - Use Laravel Mix or Vite for asset compilation

4. **Browser Caching:**
   - Set appropriate cache headers for static assets
   - Use versioned filenames for cache busting

## Performance Monitoring

### Database Query Monitoring

Enable query logging in development:

```php
// In AppServiceProvider or middleware
DB::enableQueryLog();
// ... your code ...
dd(DB::getQueryLog());
```

### Laravel Debugbar

Install Laravel Debugbar for development:
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Performance Metrics to Monitor

- **Page Load Time:** Target < 2 seconds
- **Database Queries:** Minimize N+1 queries
- **Memory Usage:** Monitor peak memory
- **Cache Hit Rate:** Track cache effectiveness

## Best Practices

### Database

1. **Use Indexes:** All foreign keys and frequently queried columns indexed
2. **Eager Load:** Always eager load relationships when accessing them
3. **Limit Results:** Use pagination for large datasets
4. **Select Specific Columns:** Use `select()` when you don't need all columns

### Caching

1. **Cache Strategically:** Cache expensive operations, not everything
2. **Set Appropriate TTL:** Balance freshness vs performance
3. **Clear Cache Properly:** Invalidate cache when data changes
4. **Use Cache Tags:** For grouped cache invalidation (if using Redis)

### Code

1. **Avoid N+1 Queries:** Always eager load relationships
2. **Use Collections:** Prefer collection methods over multiple queries
3. **Batch Operations:** Use `chunk()` for large datasets
4. **Optimize Loops:** Minimize database queries inside loops

## Migration

To apply performance indexes:

```bash
php artisan migrate
```

This will add all performance indexes to the database.

## Production Checklist

- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `php artisan migrate` (to add indexes)
- [ ] Enable OPcache in PHP
- [ ] Configure Redis/Memcached for cache (optional)
- [ ] Set up CDN for static assets (if not using jsDelivr)
- [ ] Enable gzip compression
- [ ] Set up monitoring (Laravel Telescope, New Relic, etc.)

## Troubleshooting

### Slow Queries

1. Check query log for N+1 problems
2. Verify indexes are created: `SHOW INDEXES FROM table_name;`
3. Use `EXPLAIN` to analyze query plans
4. Consider adding composite indexes for common query patterns

### Cache Issues

1. Clear cache: `php artisan cache:clear`
2. Check cache driver configuration in `.env`
3. Verify cache directory permissions
4. Monitor cache hit/miss rates

### Memory Issues

1. Increase PHP memory limit: `memory_limit = 256M`
2. Use `chunk()` for large queries
3. Optimize eager loading (don't over-eager load)
4. Consider pagination for large datasets
