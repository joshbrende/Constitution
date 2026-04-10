<?php $__env->startSection('title', 'Instructing'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-0 px-md-2">
    <h1 class="h2">Instructing Courses</h1>
    <p class="text-muted"><?php if(isset($canEdit) && $canEdit): ?> Facilitators see courses they instruct; admins see all courses. Only facilitators and admins can edit. <?php else: ?> Courses you instruct. <?php endif; ?></p>

    <?php if(isset($canEdit) && $canEdit): ?>
    <a href="<?php echo e(route('courses.create')); ?>" class="btn btn-primary mb-3"><i class="bi bi-plus-lg me-1"></i> Create course</a>
    <?php endif; ?>

    <?php if($courses->isEmpty()): ?>
    <div class="alert alert-info">You have no instructing courses. <?php if(isset($canEdit) && $canEdit): ?> <a href="<?php echo e(route('courses.create')); ?>">Create one</a>. <?php endif; ?></div>
    <?php else: ?>
    <div class="row g-4">
        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                    <p class="small text-muted mb-auto"><?php echo e($c->enrollment_count); ?> enrolled <?php if($c->instructor): ?> · <?php echo e($c->instructor->name); ?> <?php endif; ?></p>
                    <div class="d-flex gap-1 mt-2">
                        <a href="<?php echo e(route('courses.show', $c)); ?>" class="btn btn-sm btn-outline-primary">View</a>
                        <?php if(isset($canEdit) && $canEdit): ?>
                        <a href="<?php echo e(route('courses.edit', $c)); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($courses->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.facilitator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/courses/instructor-courses.blade.php ENDPATH**/ ?>