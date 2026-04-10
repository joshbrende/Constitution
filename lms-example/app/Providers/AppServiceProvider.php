<?php

namespace App\Providers;

use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Tag;
use App\Observers\TagObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Set TCPDF font path to writable storage before TCPDF is ever loaded (certificate Great Vibes font).
        if (!defined('K_PATH_FONTS')) {
            $fontCachePath = storage_path('app/fonts/tcpdf');
            if (!File::isDirectory($fontCachePath)) {
                File::makeDirectory($fontCachePath, 0755, true);
            }
            // Copy TCPDF core fonts (helvetica, times) so TCPDF finds them in our path; Great Vibes is added at runtime.
            $tcpdfFonts = base_path('vendor/tecnickcom/tcpdf/fonts');
            $coreFonts = ['helvetica', 'helveticab', 'helveticai', 'helveticabi', 'times', 'timesb', 'timesi', 'timesbi', 'courier', 'courierb', 'courieri', 'courierbi'];
            foreach ($coreFonts as $name) {
                $src = $tcpdfFonts . DIRECTORY_SEPARATOR . $name . '.php';
                $dst = $fontCachePath . DIRECTORY_SEPARATOR . $name . '.php';
                if (is_file($src) && !is_file($dst)) {
                    @copy($src, $dst);
                }
            }
            define('K_PATH_FONTS', str_replace('\\', '/', $fontCachePath) . '/');
        }
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Tag::observe(TagObserver::class);

        View::composer('layouts.facilitator', function ($view) {
            $user = auth()->user();
            $pendingSubmissionsCount = 0;
            if ($user && $user->canEditCourses()) {
                $courseIds = $user->isAdmin()
                    ? Course::pluck('id')
                    : Course::where('instructor_id', $user->id)->pluck('id');
                $pendingSubmissionsCount = AssignmentSubmission::whereIn('course_id', $courseIds)
                    ->whereNotIn('status', ['graded', 'returned'])
                    ->count();
            }
            $view->with('pendingSubmissionsCount', $pendingSubmissionsCount);
        });

        // Production caching: run once at deploy time, not on boot:
        //   php artisan config:cache
        //   php artisan route:cache
        //   php artisan view:cache
    }
}
