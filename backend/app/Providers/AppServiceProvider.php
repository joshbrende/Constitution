<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\AdminContentPolicy;
use App\Services\AdminAccessService;
use App\Models\AdminActivityRead;
use App\Models\AuditLog;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure compatibility with older MySQL key length limits
        Schema::defaultStringLength(191);

        // TCPDF font cache for certificate generation (Great Vibes)
        if (! defined('K_PATH_FONTS')) {
            define('K_PATH_FONTS', rtrim(storage_path('app/fonts/tcpdf'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
        }

        $this->configureRateLimiting();
        $this->registerBladeDirectives();
        $this->registerAdminGates();
        $this->registerDashboardComposers();
    }

    protected function registerAdminGates(): void
    {
        $p = app(AdminContentPolicy::class);
        $adminAccess = app(AdminAccessService::class);

        Gate::define('admin.section', function (Authenticatable $user, string $section) use ($adminAccess) {
            return $adminAccess->canAccessSection($user, $section);
        });

        Gate::define('admin.presidiumPublish', fn (?User $user) => $p->presidiumPublish($user));
        Gate::define('admin.contentManage', fn (?User $user) => $p->contentManage($user));
    }

    protected function registerBladeDirectives(): void
    {
        Blade::if('canAccessSection', function (string $section) {
            return app(AdminAccessService::class)->canAccessSection(auth()->user(), $section);
        });
    }

    protected function configureRateLimiting(): void
    {
        // Default for all routes in routes/api.php (via bootstrap throttleApi).
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Certificates: 5 per minute per user (generate + download)
        RateLimiter::for('certificates', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(5)->by($request->user()->id)
                : Limit::perMinute(5)->by($request->ip());
        });

        // Assessments: 10 per minute per user (start + submit)
        RateLimiter::for('assessments', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(10)->by($request->user()->id)
                : Limit::perMinute(10)->by($request->ip());
        });

        // Public certificate verification:
        // - per IP cap
        // - tighter cap per IP + certificate identifiers combo
        RateLimiter::for('certificate-verify', function (Request $request) {
            $id = (string) $request->query('id', '');
            $number = strtoupper(trim((string) $request->query('number', '')));
            $code = strtoupper(trim((string) $request->query('code', '')));
            $combo = $id . '|' . $number . '|' . $code;

            return [
                Limit::perMinute(30)->by($request->ip()),
                Limit::perMinute(10)->by($request->ip() . '|' . $combo),
            ];
        });
    }

    protected function registerDashboardComposers(): void
    {
        View::composer('layouts.dashboard', function ($view) {
            $actions = [
                'academy.enrolled',
                'dialogue.message_sent',
                'membership.granted',
                'auth.api.registered',
            ];

            $lastSeen = (int) (auth()->id()
                ? (AdminActivityRead::where('user_id', auth()->id())->value('last_seen_audit_log_id') ?? 0)
                : 0);

            $items = AuditLog::query()
                ->with(['actor:id,name,surname,email'])
                ->whereIn('action', $actions)
                ->orderByDesc('created_at')
                ->take(8)
                ->get();

            $latestId = (int) ($items->max('id') ?? 0);
            $unreadCount = $latestId > 0
                ? (int) AuditLog::query()
                    ->whereIn('action', $actions)
                    ->where('id', '>', $lastSeen)
                    ->count()
                : 0;

            $feed = $items->map(function (AuditLog $log) {
                $meta = is_array($log->metadata) ? $log->metadata : [];

                $who = $log->actor
                    ? trim(($log->actor->name ?? '') . ' ' . ($log->actor->surname ?? ''))
                    : null;

                $when = $log->created_at?->diffForHumans();

                $title = 'Activity';
                $subtitle = $who ? $who : 'System';
                $url = route('dashboard');

                switch ($log->action) {
                    case 'academy.enrolled':
                        $title = 'Academy enrolment';
                        $subtitle = ($who ? "{$who} enrolled" : 'New enrolment') . (isset($meta['course_title']) ? " • {$meta['course_title']}" : '');
                        $url = route('admin.academy.index');
                        break;
                    case 'dialogue.message_sent':
                        $title = 'Dialogue message';
                        $subtitle = ($who ? "{$who} sent a message" : 'New message') . (isset($meta['thread_title']) ? " • {$meta['thread_title']}" : '');
                        if (isset($meta['thread_id'])) {
                            $url = route('admin.dialogue.threads.show', ['thread' => (int) $meta['thread_id']]);
                        } else {
                            $url = route('admin.dialogue.index');
                        }
                        break;
                    case 'membership.granted':
                        $title = 'Membership granted';
                        $subtitle = ($who ? "{$who} passed" : 'Membership granted') . (isset($meta['course_title']) ? " • {$meta['course_title']}" : '');
                        $url = route('admin.certificates.index');
                        break;
                    case 'auth.api.registered':
                        $title = 'New user registered';
                        $subtitle = isset($meta['email']) ? (string) $meta['email'] : ($who ?: 'New registration');
                        $url = route('admin.users.index');
                        break;
                }

                return [
                    'id' => $log->id,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'when' => $when,
                    'url' => $url,
                ];
            })->values()->all();

            $view->with('dashBellActivities', $feed);
            $view->with('dashBellUnreadCount', $unreadCount);
            $view->with('dashBellLatestAuditId', $latestId);
        });
    }
}
