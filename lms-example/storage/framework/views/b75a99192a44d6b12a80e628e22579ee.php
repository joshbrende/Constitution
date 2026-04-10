<?php $__env->startSection('title', 'Edit: ' . $course->title); ?>

<?php $__env->startSection('content'); ?>
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('instructor.dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.instructor')); ?>">Instructing</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.show', $course)); ?>"><?php echo e(\Illuminate\Support\Str::limit($course->title, 40)); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <h1 class="h2">Edit course</h1>
    <p class="text-muted">Only facilitators and admins can create or edit courses.</p>

    <div class="row">
        <div class="col-lg-8">
            <form action="<?php echo e(route('courses.update', $course)); ?>" method="post">
                <?php echo $__env->make('courses._form', ['course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </form>

            <?php if(auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('courses.attendance', $course)); ?>" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-person-lines-fill me-1"></i> View attendance register</a>
            <?php endif; ?>

            <hr class="my-4">
            <div class="d-flex align-items-center gap-2">
                <form action="<?php echo e(route('courses.destroy', $course)); ?>" method="post" class="d-inline"
                      onsubmit="return confirm('Delete this course? This cannot be undone.');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete course</button>
                </form>
            </div>

            <hr class="my-4">
            <h2 class="h5 mb-3">Curriculum – edit each module</h2>
            <p class="text-muted small mb-3">Click <strong>Edit</strong> on a unit to change its title, content, duration, type, media URLs, or order.</p>
            <?php echo $__env->make('courses._curriculum_edit', ['course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.facilitator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/courses/edit.blade.php ENDPATH**/ ?>