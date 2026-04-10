<?php $__env->startSection('title', 'My notes'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h1 class="h2 mb-1">My notes</h1>
    <p class="text-muted mb-4">Your personal notes across courses and modules.</p>

    <form method="get" class="row g-2 align-items-center mb-3">
        <div class="col-md-6">
            <label for="q" class="visually-hidden">Search notes</label>
            <input
                type="search"
                name="q"
                id="q"
                class="form-control"
                value="<?php echo e($search); ?>"
                placeholder="Search by course, module, or note text..."
            >
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-search me-1"></i>Search
            </button>
        </div>
    </form>

    <?php if($notes->isEmpty()): ?>
    <div class="alert alert-info mb-0">
        <i class="bi bi-journal-text me-1"></i>
        You don't have any notes yet. Open a course and use the <strong>My notes</strong> box in the learn view to start capturing your thoughts.
    </div>
    <?php else: ?>
    <div class="list-group mb-3">
        <?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a
            href="<?php echo e(route('learn.show', ['course' => $note->course, 'unit' => $note->unit_id])); ?>"
            class="list-group-item list-group-item-action"
        >
            <div class="d-flex w-100 justify-content-between">
                <div class="me-3">
                    <h5 class="mb-1 text-truncate"><?php echo e($note->course->title ?? 'Course'); ?></h5>
                    <p class="mb-1 text-truncate">
                        <span class="badge bg-light text-secondary border me-1">
                            <?php echo e($note->unit->title ?? 'Module'); ?>

                        </span>
                        <span class="text-muted small">
                            <?php echo e(\Illuminate\Support\Str::limit(strip_tags($note->body), 120)); ?>

                        </span>
                    </p>
                </div>
                <small class="text-muted text-nowrap ms-auto">
                    <?php echo e($note->updated_at?->diffForHumans()); ?>

                </small>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php echo e($notes->links()); ?>

    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/notes/index.blade.php ENDPATH**/ ?>