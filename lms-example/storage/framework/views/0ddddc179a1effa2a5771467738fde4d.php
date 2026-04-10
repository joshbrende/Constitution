<?php $__env->startSection('title', 'My facilitator ratings'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-0 px-md-2">
    <h1 class="h2 mb-1">My facilitator ratings</h1>
    <p class="text-muted mb-4">See how delegates rated your teaching. Ratings are submitted by learners who complete a course you facilitate.</p>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3">
                        <i class="bi bi-star-fill display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e(number_format($avg, 1)); ?></div>
                        <small class="text-muted">Average rating</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                        <i class="bi bi-chat-quote display-6"></i>
                    </div>
                    <div>
                        <div class="h4 mb-0"><?php echo e($count); ?></div>
                        <small class="text-muted">Total ratings</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($ratings->isEmpty()): ?>
    <div class="alert alert-info">No ratings yet. Ratings appear when delegates complete a course you facilitate and rate you.</div>
    <?php else: ?>
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Recent ratings</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Delegate</th>
                            <th>Course</th>
                            <th class="text-center">Rating</th>
                            <th>Feedback</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $ratings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($r->enrollment->user->name ?? 'User #' . $r->enrollment->user_id); ?></td>
                            <td><a href="<?php echo e(route('courses.show', $r->enrollment->course)); ?>" class="text-decoration-none"><?php echo e(\Illuminate\Support\Str::limit($r->enrollment->course->title ?? '—', 35)); ?></a></td>
                            <td class="text-center"><span class="text-warning"><?php echo e(str_repeat('★', $r->rating)); ?><?php echo e(str_repeat('☆', 5 - $r->rating)); ?></span> <?php echo e($r->rating); ?>/5</td>
                            <td><?php echo e($r->review ? \Illuminate\Support\Str::limit($r->review, 80) : '—'); ?></td>
                            <td><small class="text-muted"><?php echo e($r->created_at?->format('d M Y')); ?></small></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <?php echo e($ratings->links()); ?>

        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.facilitator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/facilitator/ratings.blade.php ENDPATH**/ ?>