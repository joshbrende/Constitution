<?php $__env->startSection('title', 'My learning'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h1 class="h2 mb-1">My learning</h1>
    <p class="text-muted mb-4">Your progress, certificates, and activity.</p>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3">
                        <i class="bi bi-star display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e((int) $user->points); ?></div>
                        <small class="text-muted">Points</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?php echo e(route('leaderboard.index')); ?>" class="small text-decoration-none">Leaderboard <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                        <i class="bi bi-award display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($user->badges->count()); ?></div>
                        <small class="text-muted">Badges</small>
                    </div>
                </div>
                <?php if($user->badges->isNotEmpty()): ?>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <div class="d-flex flex-wrap gap-1">
                        <?php $__currentLoopData = $user->badges->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-secondary" title="<?php echo e($b->description ?? $b->name); ?>"><?php echo e($b->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 me-3">
                        <i class="bi bi-journal-bookmark display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($inProgressCount); ?></div>
                        <small class="text-muted">In progress</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?php echo e(route('courses.my')); ?>" class="small text-decoration-none">My courses <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3">
                        <i class="bi bi-patch-check display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($certificateCount); ?></div>
                        <small class="text-muted">Certificates</small>
                    </div>
                </div>
                <?php if($certificateCount > 0): ?>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?php echo e(route('certificates.show', $certificates->first())); ?>" class="small text-decoration-none">View <i class="bi bi-arrow-right"></i></a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if($resumeCourse && $resumeUnit): ?>
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="text-muted small mb-1">Continue learning</div>
                <h5 class="mb-1"><?php echo e($resumeCourse->title); ?></h5>
                <p class="mb-0 text-muted small">
                    You last viewed: <strong><?php echo e($resumeUnit->title); ?></strong>
                    <?php if($resumeEnrollment): ?>
                        · <?php echo e((int)($resumeEnrollment->progress_percentage ?? 0)); ?>% complete
                    <?php endif; ?>
                </p>
            </div>
            <div class="text-md-end">
                <a href="<?php echo e(route('learn.show', ['course' => $resumeCourse, 'unit' => $resumeUnit->id])); ?>" class="btn btn-primary">
                    <i class="bi bi-play-circle me-1"></i>Resume
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($recommendedCourses->isNotEmpty()): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-stars me-2"></i>Recommended for you</h5>
            <a href="<?php echo e(route('courses.index')); ?>" class="small text-decoration-none">Browse all</a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php $__currentLoopData = $recommendedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <a href="<?php echo e(route('courses.show', $rc)); ?>" class="text-decoration-none text-reset">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-truncate mb-1"><?php echo e($rc->title); ?></h6>
                                <?php if($rc->short_description): ?>
                                <p class="card-text text-muted small mb-2"><?php echo e(\Illuminate\Support\Str::limit($rc->short_description, 80)); ?></p>
                                <?php endif; ?>
                                <?php if($rc->instructor): ?>
                                <p class="card-text text-muted small mb-0">
                                    <i class="bi bi-person me-1"></i><?php echo e($rc->instructor->name); ?>

                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>In progress</h5>
                    <a href="<?php echo e(route('courses.my')); ?>" class="small">My courses</a>
                </div>
                <div class="card-body">
                    <?php if($inProgress->isEmpty()): ?>
                    <p class="text-muted mb-0">No courses in progress. <a href="<?php echo e(route('courses.index')); ?>">Browse courses</a> to get started.</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $inProgress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('learn.show', ['course' => $e->course, 'start' => 1])); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span class="text-truncate me-2"><?php echo e($e->course->title); ?></span>
                            <span class="badge bg-primary rounded-pill"><?php echo e((int)($e->progress_percentage ?? 0)); ?>%</span>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-award me-2"></i>Certificates</h5>
                    <?php if($certificates->isNotEmpty()): ?>
                    <a href="<?php echo e(route('courses.my')); ?>" class="small">My courses</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if($certificates->isEmpty()): ?>
                    <p class="text-muted mb-0">No certificates yet. Complete a course to earn one.</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('certificates.show', $cert)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span class="text-truncate me-2"><?php echo e($cert->course->title ?? 'Course'); ?></span>
                            <i class="bi bi-download text-muted"></i>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if($recentCompletions->isNotEmpty()): ?>
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent activity</h5>
        </div>
        <div class="card-body">
            <ul class="list-unstyled mb-0">
                <?php $__currentLoopData = $recentCompletions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong><?php echo e($uc->unit->title ?? 'Unit'); ?></strong> in <?php echo e($uc->course->title ?? 'Course'); ?>

                    </span>
                    <small class="text-muted"><?php echo e($uc->completed_at?->format('d M Y H:i')); ?></small>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="<?php echo e(route('courses.index')); ?>" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Browse courses</a>
        <a href="<?php echo e(route('courses.my')); ?>" class="btn btn-outline-secondary ms-2">My courses</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/learner/dashboard.blade.php ENDPATH**/ ?>