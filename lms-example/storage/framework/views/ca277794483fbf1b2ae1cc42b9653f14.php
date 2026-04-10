<?php $__env->startSection('title', 'Knowledge Check results'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">Knowledge Check results</h1>
    <p class="text-muted mb-4">Recent Knowledge Check attempts across your courses.</p>

    <?php if($attempts->isEmpty()): ?>
    <div class="alert alert-info">No Knowledge Check attempts yet. Results appear here when learners complete module Knowledge Checks.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Learner</th>
                    <th>Course</th>
                    <th>Knowledge Check</th>
                    <th class="text-center">Score</th>
                    <th class="text-center">Status</th>
                    <th>Completed</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($a->user->name ?? 'User #' . $a->user_id); ?></td>
                    <td>
                        <a href="<?php echo e(route('courses.show', $a->course)); ?>" class="text-decoration-none"><?php echo e(\Illuminate\Support\Str::limit($a->course?->title ?? '—', 30)); ?></a>
                    </td>
                    <td><?php echo e($a->quiz?->title ?? 'Knowledge Check #' . $a->quiz_id); ?></td>
                    <td class="text-center"><?php echo e($a->percentage); ?>%</td>
                    <td class="text-center">
                        <?php if($a->status === 'passed'): ?>
                        <span class="badge bg-success">Passed</span>
                        <?php else: ?>
                        <span class="badge bg-warning text-dark">Failed</span>
                        <?php endif; ?>
                    </td>
                    <td><small class="text-muted"><?php echo e($a->completed_at?->format('d M Y H:i') ?? '—'); ?></small></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <p class="small text-muted">Showing latest 50 attempts.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.facilitator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/facilitator/results.blade.php ENDPATH**/ ?>