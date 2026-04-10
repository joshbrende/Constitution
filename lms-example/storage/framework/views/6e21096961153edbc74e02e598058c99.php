<?php $__env->startSection('title', 'My Courses'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1 class="h2">My Courses</h1>
    <p class="text-muted">Courses you are enrolled in.</p>

    <?php if($enrollments->isEmpty()): ?>
    <div class="alert alert-info">You are not enrolled in any courses. <a href="<?php echo e(route('courses.index')); ?>">Browse courses</a>.</div>
    <?php else: ?>
    <div class="row g-4">
        <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $c = $e->course; ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <?php if($c->featured_image): ?>
                <img src="<?php echo e(asset('storage/' . $c->featured_image)); ?>" class="card-img-top" alt="" style="height:140px;object-fit:cover;">
                <?php else: ?>
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height:140px;">
                    <i class="bi bi-journal-text text-white display-4"></i>
                </div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><a href="<?php echo e(route('courses.show', $c)); ?>" class="text-decoration-none text-dark"><?php echo e($c->title); ?></a></h5>
                    <div class="progress mb-2" style="height:6px;">
                        <div class="progress-bar" role="progressbar" style="width:<?php echo e($e->progress_percentage); ?>%" aria-valuenow="<?php echo e($e->progress_percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="small text-muted mb-auto"><?php echo e($e->progress_percentage); ?>% complete</p>
                    <a href="<?php echo e(route('learn.show', ['course' => $c, 'start' => 1])); ?>" class="btn btn-sm btn-outline-primary mt-2">Continue</a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($enrollments->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training\lms\resources\views/courses/my-courses.blade.php ENDPATH**/ ?>