<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
        </ol>
    </nav>

    <h1 class="h2 mb-1">Admin Dashboard</h1>
    <p class="text-muted mb-4">Overview of courses, users, enrollments, and attendance. Quick access to management actions.</p>

    
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3">
                        <i class="bi bi-journal-text display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($stats['courses']); ?></div>
                        <small class="text-muted">Courses</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?php echo e(route('courses.index')); ?>" class="small text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                        <i class="bi bi-people display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($stats['users']); ?></div>
                        <small class="text-muted">Users</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="small text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 me-3">
                        <i class="bi bi-bookmark-check display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($stats['enrollments']); ?></div>
                        <small class="text-muted">Enrollments</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3">
                        <i class="bi bi-person-lines-fill display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($stats['attendance_records']); ?></div>
                        <small class="text-muted">Attendance records</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick actions</h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('courses.index')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-journal-text me-2"></i>All courses
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('courses.create')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-plus-lg me-2"></i>Create course
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-people me-2"></i>Users
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('admin.badges.index')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-award me-2"></i>Badges
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('admin.tags.index')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-tags me-2"></i>Tags
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('courses.instructor')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-mortarboard me-2"></i>Instructing
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('instructor.stats')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-bar-chart me-2"></i>Stats
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('instructor.results')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-clipboard-check me-2"></i>Knowledge Check results
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('admin.analytics.index')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-graph-up-arrow me-2"></i>Course analytics
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('notifications.index')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-bell me-2"></i>Notifications
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('admin.instructor-requests.index')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-person-plus me-2"></i>Instructor requests
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('admin.facilitator-ratings.index')); ?>" class="btn btn-outline-primary w-100 text-start">
                        <i class="bi bi-star me-2"></i>Facilitator ratings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-6 mb-4" id="attendance">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Attendance by course</h5>
                    <?php if($attendanceByCourse->isNotEmpty()): ?>
                    <span class="badge bg-secondary"><?php echo e($attendanceByCourse->count()); ?> course(s)</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if($attendanceByCourse->isEmpty()): ?>
                    <p class="text-muted mb-0">No attendance records yet. Attendance is captured on the Day 1 unit of each course.</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $attendanceByCourse; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('courses.attendance', $c)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span class="text-truncate me-2"><?php echo e($c->title); ?></span>
                            <span class="badge bg-primary rounded-pill"><?php echo e($c->attendance_count); ?></span>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <p class="small text-muted mt-2 mb-0">
                        Open a course’s attendance to review entries and download the CSV export.
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bookmark-check me-2"></i>Recent enrollments</h5>
                    <?php if($recentEnrollments->isNotEmpty()): ?>
                    <a href="<?php echo e(route('courses.index')); ?>" class="small text-decoration-none">All courses</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if($recentEnrollments->isEmpty()): ?>
                    <p class="text-muted mb-0">No enrollments yet.</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $recentEnrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <strong><?php echo e($e->user->name ?? 'User #' . $e->user_id); ?></strong>
                                <span class="text-muted"> → </span>
                                <span><?php echo e(\Illuminate\Support\Str::limit($e->course->title ?? 'Course', 40)); ?></span>
                                <br>
                                <small class="text-muted"><?php echo e($e->enrolled_at?->format('d M Y H:i')); ?></small>
                            </div>
                            <a href="<?php echo e(route('courses.show', $e->course)); ?>" class="btn btn-sm btn-outline-secondary">View</a>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>