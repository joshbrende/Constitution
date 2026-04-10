<?php $__env->startSection('title', 'Facilitator Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Facilitator Dashboard</h1>
    <p class="text-muted mb-4">Overview of your courses, enrollments, and recent activity.</p>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3">
                        <i class="bi bi-journal-text display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($courses->count()); ?></div>
                        <small class="text-muted">Courses</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?php echo e(route('courses.instructor')); ?>" class="small text-decoration-none">View all <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                        <i class="bi bi-bookmark-check display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($totalEnrollments); ?></div>
                        <small class="text-muted">Enrollments</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent enrollments</h5>
                    <a href="<?php echo e(route('courses.instructor')); ?>" class="small">Instructing</a>
                </div>
                <div class="card-body">
                    <?php if($recentEnrollments->isEmpty()): ?>
                    <p class="text-muted mb-0">No enrollments yet. Enrollments appear here when learners join your courses.</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $recentEnrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo e($e->user->name ?? 'User #' . $e->user_id); ?></strong>
                                <span class="text-muted"> → </span>
                                <span><?php echo e(\Illuminate\Support\Str::limit($e->course->title ?? 'Course', 35)); ?></span>
                                <br>
                                <small class="text-muted"><?php echo e($e->enrolled_at?->format('d M Y H:i')); ?></small>
                            </div>
                            <a href="<?php echo e(route('courses.show', $e->course)); ?>" class="btn btn-sm btn-outline-primary">View course</a>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick actions</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo e(route('courses.create')); ?>" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-plus-lg me-2"></i>Create course
                    </a>
                    <a href="<?php echo e(route('courses.instructor')); ?>" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-journal-text me-2"></i>Instructing
                    </a>
                    <a href="<?php echo e(route('instructor.stats')); ?>" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-bar-chart me-2"></i>Stats
                    </a>
                    <a href="<?php echo e(route('instructor.results')); ?>" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-clipboard-check me-2"></i>Results
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.facilitator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training\lms\resources\views/facilitator/dashboard.blade.php ENDPATH**/ ?>