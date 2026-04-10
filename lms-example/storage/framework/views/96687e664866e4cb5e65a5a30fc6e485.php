<?php $__env->startSection('title', 'Course analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Admin Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Course analytics</li>
        </ol>
    </nav>

    <h1 class="h2 mb-1">Course analytics</h1>
    <p class="text-muted mb-4">
        Enrollment, completion and quiz performance per course.
    </p>

    <?php if($items->isEmpty()): ?>
    <div class="alert alert-info">
        No courses found. Create a course and enroll learners to see analytics.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Course</th>
                    <th class="text-center">Enrolled</th>
                    <th class="text-center">Completed</th>
                    <th class="text-center">Completion rate</th>
                    <th class="text-center">Quiz attempts</th>
                    <th class="text-center">Avg quiz %</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $c = $row['course']; ?>
                <tr>
                    <td>
                        <a href="<?php echo e(route('courses.show', $c)); ?>" class="text-decoration-none fw-medium">
                            <?php echo e($c->title); ?>

                        </a>
                        <?php if($c->instructor): ?>
                        <br>
                        <small class="text-muted"><?php echo e($c->instructor->name); ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($row['enrolled']); ?></td>
                    <td class="text-center"><?php echo e($row['completed']); ?></td>
                    <td class="text-center">
                        <?php if($row['completion_rate'] !== null): ?>
                            <?php echo e($row['completion_rate']); ?>%
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($row['quiz_attempts']); ?></td>
                    <td class="text-center">
                        <?php if($row['avg_quiz_pct']): ?>
                            <?php echo e(number_format($row['avg_quiz_pct'], 1)); ?>%
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/admin/analytics.blade.php ENDPATH**/ ?>