<?php $__env->startSection('title', 'Notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.index')); ?>">Courses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifications</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="h2 mb-0">Notifications</h1>
        <?php if(auth()->user()->unreadNotifications()->exists()): ?>
        <form action="<?php echo e(route('notifications.mark-all-read')); ?>" method="post" class="d-inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline-secondary btn-sm">Mark all as read</button>
        </form>
        <?php endif; ?>
    </div>

    <?php if($notifications->isEmpty()): ?>
    <div class="alert alert-info">No notifications yet.</div>
    <?php else: ?>
    <ul class="list-group">
        <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $d = is_array($n->data) ? $n->data : [];
            $msg = $d['message'] ?? 'Notification';
            $url = $d['action_url'] ?? route('notifications.index');
            $unread = $n->read_at === null;
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-start <?php echo e($unread ? 'list-group-item-primary list-group-item-light' : ''); ?>">
            <div class="ms-2 me-auto">
                <a href="<?php echo e(route('notifications.read-and-go', $n->id)); ?>" class="<?php echo e($unread ? 'fw-semibold' : ''); ?> text-decoration-none text-dark"><?php echo e($msg); ?></a>
                <div class="small text-muted mt-1"><?php echo e($n->created_at->format('d M Y H:i')); ?></div>
            </div>
            <a href="<?php echo e(route('notifications.read-and-go', $n->id)); ?>" class="btn btn-sm btn-outline-primary">View</a>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <div class="mt-3"><?php echo e($notifications->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/notifications/index.blade.php ENDPATH**/ ?>