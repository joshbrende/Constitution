<?php $__env->startSection('title', 'Leaderboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1 class="h2 mb-1">Leaderboard</h1>
    <p class="text-muted mb-4">Top learners by points. Earn points by enrolling, completing units, passing Knowledge Checks, and finishing courses.</p>

    <?php if($users->isEmpty()): ?>
    <div class="alert alert-info">No users yet.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">#</th>
                    <th>Name</th>
                    <th class="text-center">Points</th>
                    <th>Badges</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-muted"><?php echo e($users->firstItem() + $loop->index); ?></td>
                    <td><?php echo e($u->name ?? 'User'); ?></td>
                    <td class="text-center"><strong><?php echo e((int) ($u->points ?? 0)); ?></strong></td>
                    <td>
                        <?php $ubadges = $u->badges ?? collect(); ?>
                        <?php $__empty_1 = true; $__currentLoopData = $ubadges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <span class="badge bg-secondary me-1" title="<?php echo e($b->description ?? ''); ?>"><i class="<?php echo e($b->icon ?? 'bi bi-award'); ?> me-1"></i><?php echo e($b->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        <?php echo e($users->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/leaderboard/index.blade.php ENDPATH**/ ?>