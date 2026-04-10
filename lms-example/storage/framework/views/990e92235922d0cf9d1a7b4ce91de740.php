<?php $__env->startSection('title', 'Learners – ' . $course->title); ?>

<?php $__env->startSection('content'); ?>
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Learners – <?php echo e($course->title); ?></h1>
    <p class="text-muted mb-3">
        Enrollment, progress and recent activity for this course.
    </p>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <a href="<?php echo e(route('courses.instructor')); ?>" class="small text-decoration-none">
                &larr; Back to instructing courses
            </a>
        </div>
        <div class="btn-group btn-group-sm" role="group" aria-label="Filter learners">
            <a href="<?php echo e(route('instructor.course-learners', [$course])); ?>"
               class="btn btn-outline-secondary <?php echo e($filter === '' ? 'active' : ''); ?>">
                All learners
            </a>
            <a href="<?php echo e(route('instructor.course-learners', [$course, 'filter' => 'at-risk'])); ?>"
               class="btn btn-outline-secondary <?php echo e($filter === 'at-risk' ? 'active' : ''); ?>">
                At risk
                <?php if($atRiskCount > 0): ?>
                    <span class="badge bg-danger ms-1"><?php echo e($atRiskCount); ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <?php if($rows->isEmpty()): ?>
    <div class="alert alert-info">
        No enrollments yet for this course.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Learner</th>
                    <th class="text-center">Progress</th>
                    <th class="text-center">Units completed</th>
                    <th class="text-center">Quizzes completed</th>
                    <th class="text-center">Last activity</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $e = $row['enrollment'];
                    $cp = $row['progress'];
                ?>
                <tr <?php if($row['at_risk']): ?> class="table-warning" <?php endif; ?>>
                    <td>
                        <strong><?php echo e($e->user->name ?? 'User #' . $e->user_id); ?></strong><br>
                        <small class="text-muted"><?php echo e($e->user->email ?? ''); ?></small>
                    </td>
                    <td class="text-center">
                        <?php echo e($row['percentage']); ?>%
                    </td>
                    <td class="text-center">
                        <?php echo e($cp->units_completed ?? '–'); ?>/<?php echo e($cp->total_units ?? '–'); ?>

                    </td>
                    <td class="text-center">
                        <?php echo e($cp->quizzes_completed ?? '–'); ?>/<?php echo e($cp->total_quizzes ?? '–'); ?>

                    </td>
                    <td class="text-center">
                        <small class="text-muted">
                            <?php echo e($row['last_activity_at'] ? $row['last_activity_at']->diffForHumans() : 'No activity yet'); ?>

                        </small>
                    </td>
                    <td class="text-center">
                        <?php if($row['at_risk']): ?>
                        <span class="badge bg-warning text-dark">At risk</span>
                        <?php elseif($row['percentage'] >= 100): ?>
                        <span class="badge bg-success">Completed</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">In progress</span>
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


<?php echo $__env->make('layouts.facilitator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/facilitator/learners.blade.php ENDPATH**/ ?>