<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'instructions', 'learning_guidelines',
        'assessment_guide', 'short_description', 'instructor_id', 'certificate_template_id',
        'featured_image', 'video_preview', 'status', 'access', 'price', 'duration', 'max_students',
        'curriculum', 'prerequisites', 'meta_title', 'meta_description', 'meta_keywords',
        'enrollment_count', 'rating', 'rating_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function certificateTemplate(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'course_tag');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class)->orderByRaw('`order` asc');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function instructorRequests(): HasMany
    {
        return $this->hasMany(InstructorRequest::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class)->where('is_approved', 1)->latest();
    }

    public function getCurriculumAttribute(): \Illuminate\Support\Collection
    {
        $items = collect();
        foreach ($this->units as $unit) {
            $isQuiz = $unit->unit_type === 'quiz';
            $items->push([
                'type' => $unit->unit_type,
                'id' => $unit->id,
                'title' => $unit->title,
                'duration' => $unit->duration ? (int) $unit->duration . ' min' : '—',
                'icon' => $isQuiz ? 'bi bi-question-circle' : 'bi bi-file-text',
            ]);
        }
        return $items;
    }

    /**
     * Structured curriculum for 3-day flow:
     * DAY 1: Day 1 unit + Module 1–4
     * DAY 2: Day 2 unit + Module 5–8
     * DAY 3: Day 3 unit + Module 9–12
     * Trailing: Course Closure & Certification
     * Units follow DB order (Day 2 after Module 4, Day 3 after Module 8).
     * Returns ['days' => [1=>['standalones'=>[], 'modules'=>[1=>[],...]], ...], 'trailing'=>[]]
     */
    public function getStructuredCurriculumAttribute(): array
    {
        $days = [
            1 => ['standalones' => collect(), 'modules' => []],
            2 => ['standalones' => collect(), 'modules' => []],
            3 => ['standalones' => collect(), 'modules' => []],
        ];
        $trailing = collect();

        foreach ($this->units as $unit) {
            $isQuiz = $unit->unit_type === 'quiz';
            $item = [
                'type' => $unit->unit_type,
                'id' => $unit->id,
                'title' => $unit->title,
                'duration' => $unit->duration ? (int) $unit->duration . ' min' : '—',
                'icon' => $isQuiz ? 'bi bi-question-circle' : 'bi bi-file-text',
            ];

            $title = $unit->title;
            if (preg_match('/^Day\s*1\b/i', $title)) {
                $days[1]['standalones']->push($item);
                continue;
            }
            if (preg_match('/^Day\s*2\b/i', $title)) {
                $days[2]['standalones']->push($item);
                continue;
            }
            if (preg_match('/^Day\s*3\b/i', $title)) {
                $days[3]['standalones']->push($item);
                continue;
            }
            if (preg_match('/Course\s*Closure|Certification\s*$/i', $title) || stripos($title, 'Course Closure') !== false) {
                $trailing->push($item);
                continue;
            }
            if (preg_match('/Module\s*(\d+)/i', $title, $m)) {
                $n = (int) $m[1];
                $d = $n <= 4 ? 1 : ($n <= 8 ? 2 : 3);
                if (!isset($days[$d]['modules'][$n])) {
                    $days[$d]['modules'][$n] = collect();
                }
                $days[$d]['modules'][$n]->push($item);
                continue;
            }
            $days[1]['standalones']->push($item);
        }

        foreach ([1, 2, 3] as $d) {
            ksort($days[$d]['modules']);
        }
        return ['days' => $days, 'trailing' => $trailing];
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
