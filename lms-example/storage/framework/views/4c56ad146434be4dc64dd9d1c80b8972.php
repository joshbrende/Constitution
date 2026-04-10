<?php $__env->startSection('title', 'All Courses'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <section class="mb-4">
        <h1 class="h2">All Courses</h1>
        <p class="text-muted">Browse and enroll in courses.</p>
    </section>

    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <span class="text-muted"><?php echo e($courses->total()); ?> course(s)</span>
        </div>
        <div class="col-md-6 text-md-end">
            <form action="<?php echo e(route('courses.index')); ?>" method="get" class="d-inline">
                <label class="me-2">Order:</label>
                <select name="order" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                    <option value="newest" <?php echo e(request('order') === 'newest' ? 'selected' : ''); ?>>Newest</option>
                    <option value="alphabetical" <?php echo e(request('order') === 'alphabetical' ? 'selected' : ''); ?>>Alphabetical</option>
                    <option value="popular" <?php echo e(request('order') === 'popular' ? 'selected' : ''); ?>>Most Members</option>
                </select>
            </form>
        </div>
    </div>

    <?php $enrolledIds = $enrolledCourseIds ?? collect(); ?>
    <?php if($courses->isEmpty()): ?>
    <div class="alert alert-info">No courses yet.</div>
    <?php else: ?>
    <ul class="list-unstyled row g-4">
        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $locked = !$enrolledIds->contains($c->id);
        ?>
        <li class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm <?php echo e($locked ? 'border-secondary' : ''); ?>" style="<?php echo e($locked ? 'opacity: 0.92;' : ''); ?>">
                <?php if($c->featured_image): ?>
                <div class="position-relative">
                    <img src="<?php echo e(asset('storage/' . $c->featured_image)); ?>" class="card-img-top" alt="" style="height:160px;object-fit:cover;">
                    <?php if($locked): ?>
                    <span class="position-absolute top-0 end-0 m-2 rounded-circle bg-dark bg-opacity-75 p-1" title="Enroll to unlock">
                        <i class="bi bi-lock-fill text-white"></i>
                    </span>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center position-relative" style="height:160px;">
                    <?php if($locked): ?>
                    <span class="position-absolute top-0 end-0 m-2 rounded-circle bg-dark bg-opacity-75 p-1" title="Enroll to unlock">
                        <i class="bi bi-lock-fill text-white"></i>
                    </span>
                    <?php endif; ?>
                    <i class="bi bi-journal-text text-white display-4"></i>
                </div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">
                        <?php if($locked): ?>
                        <span class="text-dark"><?php echo e($c->title); ?></span>
                        <span class="text-muted small fw-normal d-block"><i class="bi bi-lock me-1"></i> Locked — enroll to unlock</span>
                        <?php else: ?>
                        <a href="<?php echo e(route('courses.show', $c)); ?>" class="text-decoration-none text-dark"><?php echo e($c->title); ?></a>
                        <?php endif; ?>
                    </h5>
                    <p class="card-text text-muted small flex-grow-1"><?php echo e(Str::limit($c->short_description ?? $c->description, 100)); ?></p>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                        <small class="text-muted"><?php echo e($c->enrollment_count); ?> enrolled · <?php echo e($c->instructor->name ?? '—'); ?></small>
                        <?php if($locked): ?>
                        <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-sm btn-outline-primary">Login to enroll</a>
                        <?php else: ?>
                        <a href="<?php echo e(route('courses.show', $c)); ?>" class="btn btn-sm btn-primary">Enroll to unlock</a>
                        <?php endif; ?>
                        <?php else: ?>
                        <a href="<?php echo e(route('courses.show', $c)); ?>" class="btn btn-sm btn-outline-primary">View</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($courses->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/courses/index.blade.php ENDPATH**/ ?>