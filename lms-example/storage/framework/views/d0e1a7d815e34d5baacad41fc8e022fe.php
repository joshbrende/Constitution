<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Facilitator'); ?> – <?php echo e(config('app.name')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .facilitator-sidebar { min-height: calc(100vh - 56px - 120px); }
        .facilitator-sidebar .nav-link { border-radius: 0.375rem; }
        .facilitator-sidebar .nav-link.active { background: rgba(13, 110, 253, 0.15); color: #0d6efd; }
        .facilitator-sidebar .nav-link:hover:not(.active) { background: rgba(0,0,0,.05); }
        @media (max-width: 767.98px) {
            .facilitator-sidebar { min-height: auto; margin-bottom: 1rem !important; }
            .facilitator-sidebar .card-body { padding: 0.5rem; }
            .facilitator-sidebar .nav-link { padding: 0.5rem 0.75rem; font-size: 0.875rem; }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo e(auth()->check() && auth()->user()->canEditCourses() ? route('instructor.dashboard') : route('courses.index')); ?>">
                <strong>TTM Group</strong> LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('admin.dashboard')); ?>">Admin</a></li>
                    <?php endif; ?>
                    <?php if(!auth()->user()->canEditCourses() || auth()->user()->isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('courses.index')); ?>">All Courses</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('learner.dashboard')); ?>">My learning</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('courses.my')); ?>">My Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('notes.index')); ?>">My notes</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('leaderboard.index')); ?>">Leaderboard</a></li>
                    <?php if(auth()->user()->canEditCourses()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('instructor.dashboard')); ?>">Instructing</a></li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if(auth()->guard()->guest()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('courses.index')); ?>">All Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('leaderboard.index')); ?>">Leaderboard</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if(auth()->guard()->check()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                            <i class="bi bi-bell"></i>
                            <?php $unreadN = auth()->user()->unreadNotifications()->count(); ?>
                            <?php if($unreadN > 0): ?><span class="badge bg-danger rounded-pill ms-1"><?php echo e($unreadN > 9 ? '9+' : $unreadN); ?></span><?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php $__empty_1 = true; $__currentLoopData = auth()->user()->unreadNotifications()->limit(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li><a class="dropdown-item text-wrap" href="<?php echo e(route('notifications.read-and-go', $nb->id)); ?>"><?php echo e(is_array($nb->data) ? \Illuminate\Support\Str::limit($nb->data['message'] ?? 'Notification', 60) : 'Notification'); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li><span class="dropdown-item text-muted">No new notifications</span></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('notifications.index')); ?>">View all</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('profile.edit')); ?>"><?php echo e(auth()->user()->name); ?></a></li>
                    <li class="nav-item">
                        <form action="<?php echo e(route('logout')); ?>" method="post" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
                    <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('login')); ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('register')); ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <?php if(session('message')): ?>
        <div class="alert alert-info"><?php echo e(session('message')); ?></div>
        <?php endif; ?>
        <?php if(session('quiz_result')): ?>
        <?php $r = session('quiz_result'); ?>
        <div class="alert <?php echo e($r['passed'] ? 'alert-success' : 'alert-warning'); ?>">
            Knowledge Check: <?php echo e($r['score']); ?>% <?php echo e($r['passed'] ? '– Passed' : '– Try again'); ?>

        </div>
        <?php endif; ?>

        <div class="row">
            <aside class="col-md-3 col-lg-2 mb-4 mb-md-0">
                <nav class="card shadow-sm facilitator-sidebar">
                    <div class="card-body py-2">
                        <div class="small text-muted text-uppercase px-3 py-2">Facilitator</div>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('instructor.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('instructor.dashboard')); ?>">
                                    <i class="bi bi-grid-1x2 me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('help.facilitator') ? 'active' : ''); ?>" href="<?php echo e(route('help.facilitator')); ?>">
                                    <i class="bi bi-question-circle me-2"></i>Help
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('courses.instructor') || request()->routeIs('courses.edit') || request()->routeIs('units.edit') ? 'active' : ''); ?>" href="<?php echo e(route('courses.instructor')); ?>">
                                    <i class="bi bi-journal-text me-2"></i>Instructing
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('courses.create') ? 'active' : ''); ?>" href="<?php echo e(route('courses.create')); ?>">
                                    <i class="bi bi-plus-lg me-2"></i>Create course
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('instructor.stats') ? 'active' : ''); ?>" href="<?php echo e(route('instructor.stats')); ?>">
                                    <i class="bi bi-bar-chart me-2"></i>Stats
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('instructor.quiz-stats') ? 'active' : ''); ?>" href="<?php echo e(route('instructor.quiz-stats')); ?>">
                                    <i class="bi bi-pie-chart me-2"></i>Knowledge Check stats
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('instructor.results') ? 'active' : ''); ?>" href="<?php echo e(route('instructor.results')); ?>">
                                    <i class="bi bi-clipboard-check me-2"></i>Knowledge Check results
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('instructor.ratings') ? 'active' : ''); ?>" href="<?php echo e(route('instructor.ratings')); ?>">
                                    <i class="bi bi-star me-2"></i>My ratings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('instructor.submissions*') ? 'active' : ''); ?>" href="<?php echo e(route('instructor.submissions.index')); ?>">
                                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Submissions
                                    <?php if(($pendingSubmissionsCount ?? 0) > 0): ?><span class="badge bg-warning text-dark ms-1"><?php echo e($pendingSubmissionsCount > 99 ? '99+' : $pendingSubmissionsCount); ?></span><?php endif; ?>
                                </a>
                            </li>
                            <?php if(auth()->user()->isAdmin()): ?>
                            <li class="nav-item mt-2 pt-2 border-top">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.instructor-requests*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.instructor-requests.index')); ?>">
                                    <i class="bi bi-person-plus me-2"></i>Instructor requests
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.facilitator-ratings*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.facilitator-ratings.index')); ?>">
                                    <i class="bi bi-star me-2"></i>Facilitator ratings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                                    <i class="bi bi-person-lines-fill me-2"></i>Attendance
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
            </aside>
            <main class="col-md-9 col-lg-10">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-muted small">
            <div class="row">
                <div class="col-md-6">&copy; <?php echo e(date('Y')); ?> <strong>TTM Group</strong>. All rights reserved.</div>
                <div class="col-md-6 text-md-end">
                    <a href="<?php echo e(route('courses.index')); ?>" class="me-3">Courses</a>
                    <a href="<?php echo e(route('help.index')); ?>" class="me-3">Help</a>
                    <a href="https://ttm-group.co.za" target="_blank" rel="noopener noreferrer">TTM Group Website</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/layouts/facilitator.blade.php ENDPATH**/ ?>