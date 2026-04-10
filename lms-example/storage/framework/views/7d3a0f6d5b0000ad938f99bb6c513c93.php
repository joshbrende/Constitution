<?php $__env->startSection('title', $course->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.index')); ?>">Courses</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($course->title); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <h1 class="h2"><?php echo e($course->title); ?></h1>
            <?php if($course->short_description): ?>
            <p class="lead text-muted"><?php echo e($course->short_description); ?></p>
            <?php endif; ?>
            <p class="text-muted small">
                <i class="bi bi-people"></i> <?php echo e($course->enrollment_count); ?> students enrolled
                · <i class="bi bi-person"></i> <?php echo e($course->instructor->name ?? 'Instructor'); ?>

            </p>

            <div class="mt-4">
                <h3 class="h5">Course Description</h3>
                <div class="course-description">
                    <?php echo nl2br(e($course->description ?? 'No description.')); ?>

                </div>
            </div>

            <h3 class="h5 mt-4">Course Curriculum</h3>
            <div class="course-curriculum table-responsive">
                <table class="table table-sm">
                    <tbody>
                    <?php $__currentLoopData = $course->curriculum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-muted" style="width:40px;"><i class="<?php echo e($item['icon'] ?? 'bi bi-file-text'); ?>"></i></td>
                        <td><?php echo e($item['title']); ?></td>
                        <td class="text-muted small"><?php echo e($item['duration'] ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php if($course->curriculum->isEmpty()): ?>
            <p class="text-muted">No curriculum yet.</p>
            <?php endif; ?>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 1rem;">
                <?php if($course->featured_image): ?>
                <img src="<?php echo e(asset('storage/' . $course->featured_image)); ?>" class="card-img-top" alt="">
                <?php else: ?>
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height:180px;">
                    <i class="bi bi-journal-text text-white display-3"></i>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if(isset($canEdit) && $canEdit): ?>
                    <a href="<?php echo e(route('courses.edit', $course)); ?>" class="btn btn-outline-primary w-100 mb-2"><i class="bi bi-pencil me-1"></i> Edit course</a>
                    <?php endif; ?>
                    <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('courses.attendance', $course)); ?>" class="btn btn-outline-secondary w-100 mb-2"><i class="bi bi-person-lines-fill me-1"></i> Attendance register</a>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if($enrolled): ?>
                    <a href="<?php echo e(route('learn.show', ['course' => $course, 'start' => 1])); ?>" class="btn btn-primary w-100">Continue Learning</a>
                    <?php else: ?>
                    <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('courses.enroll', $course)); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-primary w-100">Enroll</button>
                    </form>
                    <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-primary w-100">Login to Enroll</a>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training\lms\resources\views/courses/show.blade.php ENDPATH**/ ?>