<?php
    $structured = $course->structured_curriculum;
    $days = $structured['days'] ?? [];
    $trailing = $structured['trailing'] ?? collect();
?>

<?php if($course->units->isEmpty()): ?>
<div class="alert alert-info">No units yet. Add units via seeders (e.g. Module1SectionsSeeder, Day2Day3Seeder, ModuleQuizzesSeeder) or implement &quot;Add unit&quot; to create them here.</div>
<?php else: ?>
<div class="card mb-4">
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            <?php $__currentLoopData = [1, 2, 3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $day = $days[$d] ?? null;
                if (!$day) continue;
                $standalones = $day['standalones'] ?? collect();
                $modules = $day['modules'] ?? [];
                ksort($modules);
                $hasAny = $standalones->isNotEmpty() || !empty($modules);
                if (!$hasAny) continue;
            ?>
            <li class="list-group-item bg-light fw-semibold">DAY <?php echo e($d); ?></li>
            <?php $__currentLoopData = $standalones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item d-flex align-items-center justify-content-between">
                <span><i class="bi <?php echo e($item['icon'] ?? 'bi-file-text'); ?> text-muted me-2"></i><?php echo e($item['title']); ?></span>
                <div class="d-flex gap-1">
                    <?php if(($item['type'] ?? '') === 'quiz'): ?>
                    <a href="<?php echo e(route('units.quiz.edit', [$course, $item['id']])); ?>" class="btn btn-sm btn-outline-secondary" title="Edit Knowledge Check">Knowledge Check</a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('units.edit', [$course, $item['id']])); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                </div>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modNum => $moduleItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item bg-light fw-semibold pt-2 pb-1">MODULE <?php echo e($modNum); ?></li>
            <?php $__currentLoopData = $moduleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item d-flex align-items-center justify-content-between">
                <span><i class="bi <?php echo e($item['icon'] ?? 'bi-file-text'); ?> text-muted me-2"></i><?php echo e($item['title']); ?></span>
                <div class="d-flex gap-1">
                    <?php if(($item['type'] ?? '') === 'quiz'): ?>
                    <a href="<?php echo e(route('units.quiz.edit', [$course, $item['id']])); ?>" class="btn btn-sm btn-outline-secondary" title="Edit Knowledge Check">Knowledge Check</a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('units.edit', [$course, $item['id']])); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                </div>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($trailing->isNotEmpty()): ?>
            <li class="list-group-item bg-light fw-semibold">END</li>
            <?php $__currentLoopData = $trailing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item d-flex align-items-center justify-content-between">
                <span><i class="bi <?php echo e($item['icon'] ?? 'bi-file-text'); ?> text-muted me-2"></i><?php echo e($item['title']); ?></span>
                <div class="d-flex gap-1">
                    <?php if(($item['type'] ?? '') === 'quiz'): ?>
                    <a href="<?php echo e(route('units.quiz.edit', [$course, $item['id']])); ?>" class="btn btn-sm btn-outline-secondary" title="Edit Knowledge Check">Knowledge Check</a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('units.edit', [$course, $item['id']])); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                </div>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/courses/_curriculum_edit.blade.php ENDPATH**/ ?>