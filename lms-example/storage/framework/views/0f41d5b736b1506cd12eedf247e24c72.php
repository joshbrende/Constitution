<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> – <?php echo e(config('app.name')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .admin-sidebar { min-height: calc(100vh - 56px - 120px); }
        .admin-sidebar .nav-link { border-radius: 0.375rem; }
        .admin-sidebar .nav-link.active { background: rgba(13, 110, 253, 0.15); color: #0d6efd; }
        .admin-sidebar .nav-link:hover:not(.active) { background: rgba(0,0,0,.05); }
        @media (max-width: 767.98px) {
            .admin-sidebar { min-height: auto; margin-bottom: 1rem !important; }
            .admin-sidebar .card-body { padding: 0.5rem; }
            .admin-sidebar .nav-link { padding: 0.5rem 0.75rem; font-size: 0.875rem; }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo e(route('admin.dashboard')); ?>">
                <strong>TTM Group</strong> LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('admin.dashboard')); ?>">Admin</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('courses.index')); ?>">All Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('courses.my')); ?>">My Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('leaderboard.index')); ?>">Leaderboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('instructor.dashboard')); ?>">Instructing</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('profile.edit')); ?>"><?php echo e(auth()->user()->name); ?></a></li>
                    <li class="nav-item">
                        <form action="<?php echo e(route('logout')); ?>" method="post" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
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
                <nav class="card shadow-sm admin-sidebar">
                    <div class="card-body py-2">
                        <div class="small text-muted text-uppercase px-3 py-2">Admin</div>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                                    <i class="bi bi-grid-1x2 me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('help.admin') ? 'active' : ''); ?>" href="<?php echo e(route('help.admin')); ?>">
                                    <i class="bi bi-question-circle me-2"></i>Help
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.users*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.users.index')); ?>">
                                    <i class="bi bi-people me-2"></i>Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.badges*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.badges.index')); ?>">
                                    <i class="bi bi-award me-2"></i>Badges
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.tags*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.tags.index')); ?>">
                                    <i class="bi bi-tags me-2"></i>Tags
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('courses.index') ? 'active' : ''); ?>" href="<?php echo e(route('courses.index')); ?>">
                                    <i class="bi bi-journal-text me-2"></i>Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('courses.create') ? 'active' : ''); ?>" href="<?php echo e(route('courses.create')); ?>">
                                    <i class="bi bi-plus-lg me-2"></i>Create course
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('courses.instructor') || request()->routeIs('courses.attendance') ? 'active' : ''); ?>" href="<?php echo e(route('courses.instructor')); ?>">
                                    <i class="bi bi-mortarboard me-2"></i>Instructing
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('instructor.stats') ? 'active' : ''); ?>" href="<?php echo e(route('instructor.stats')); ?>">
                                    <i class="bi bi-bar-chart me-2"></i>Stats
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
                                </a>
                            </li>
                            <li class="nav-item">
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
                                <a class="nav-link <?php echo e(request()->routeIs('admin.certificate-signatures*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.certificate-signatures.index')); ?>">
                                    <i class="bi bi-pen me-2"></i>Certificate signatures
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.certificate-templates*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.certificate-templates.index')); ?>">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>Certificate templates
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('courses.attendance') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>#attendance">
                                    <i class="bi bi-person-lines-fill me-2"></i>Attendance
                                </a>
                            </li>
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
<?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/layouts/admin.blade.php ENDPATH**/ ?>