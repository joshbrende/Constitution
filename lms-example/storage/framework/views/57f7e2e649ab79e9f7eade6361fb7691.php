<?php $__env->startSection('title', 'Attendance register – ' . $course->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.index')); ?>">Courses</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.show', $course)); ?>"><?php echo e($course->title); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Attendance register</li>
        </ol>
    </nav>

    <h1 class="h2">Attendance register</h1>
    <p class="text-muted"><?php echo e($course->title); ?></p>

    <?php if($rows->isEmpty()): ?>
    <div class="alert alert-info">No attendance records yet.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Designation</th>
                    <th>Organisation</th>
                    <th>Contact number</th>
                    <th>Email</th>
                    <th>Registered at</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($r->title ?? '—'); ?></td>
                    <td><?php echo e($r->name); ?></td>
                    <td><?php echo e($r->surname); ?></td>
                    <td><?php echo e($r->designation ?? '—'); ?></td>
                    <td><?php echo e($r->organisation ?? '—'); ?></td>
                    <td><?php echo e($r->contact_number ?? '—'); ?></td>
                    <td><?php echo e($r->email); ?></td>
                    <td><?php echo e($r->created_at?->format('d M Y H:i') ?? '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <a href="<?php echo e(route('courses.show', $course)); ?>" class="btn btn-outline-secondary mt-3">Back to course</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training\lms\resources\views/courses/attendance.blade.php ENDPATH**/ ?>