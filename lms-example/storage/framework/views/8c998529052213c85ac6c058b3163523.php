<?php $__env->startSection('title', 'Edit Knowledge Check: ' . ($quiz->title ?? $unit->title)); ?>

<?php $__env->startSection('content'); ?>
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('instructor.dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.instructor')); ?>">Instructing</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.show', $course)); ?>"><?php echo e(\Illuminate\Support\Str::limit($course->title, 35)); ?></a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.edit', $course)); ?>">Edit course</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('units.edit', [$course, $unit])); ?>">Edit unit</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Knowledge Check</li>
        </ol>
    </nav>

    <h1 class="h2">Edit Knowledge Check</h1>
    <p class="text-muted"><?php echo e($unit->title); ?></p>

    <form action="<?php echo e(route('units.quiz.update', [$course, $unit])); ?>" method="post" class="mb-4" id="quiz-edit-form">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="card mb-4">
            <div class="card-header">Settings</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="quiz_title" class="form-label">Knowledge Check title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php $__errorArgs = ['quiz_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="quiz_title" name="quiz_title"
                           value="<?php echo e(old('quiz_title', $quiz->title)); ?>" required>
                    <?php $__errorArgs = ['quiz_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-3">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control <?php $__errorArgs = ['instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="instructions" name="instructions" rows="2"><?php echo e(old('instructions', $quiz->instructions)); ?></textarea>
                    <?php $__errorArgs = ['instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="pass_percentage" class="form-label">Pass mark (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?php $__errorArgs = ['pass_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="pass_percentage" name="pass_percentage"
                               value="<?php echo e(old('pass_percentage', $quiz->pass_percentage ?? 70)); ?>" min="1" max="100" required>
                        <?php $__errorArgs = ['pass_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4 d-flex align-items-end pb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="randomize_questions" id="randomize_questions" value="1"
                                   <?php echo e(old('randomize_questions', $quiz->randomize_questions) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="randomize_questions">Randomize question order</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Questions</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-question"><i class="bi bi-plus-lg me-1"></i>Add question</button>
            </div>
            <div class="card-body">
                <div id="questions-container">
                    <?php
                        $qList = old('questions', $quiz->questions->map(function ($q) {
                            $idx = collect($q->options ?? [])->search(fn ($o) => in_array($o['value'] ?? null, (array)($q->correct_answers ?? []), true));
                            return [
                                'question' => $q->question,
                                'options' => array_map(fn ($o) => $o['text'] ?? '', $q->options ?? []),
                                'correct_index' => $idx !== false ? min(3, max(0, (int)$idx)) : 0,
                                'points' => $q->points ?? 1,
                            ];
                        })->values()->all());
                    ?>
                    <?php $__currentLoopData = $qList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qi => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $opts = $q['options'] ?? [];
                        if (!is_array($opts)) { $opts = []; }
                        $opts = array_pad($opts, 4, '');
                        $correctIdx = (int)($q['correct_index'] ?? 0);
                        if ($correctIdx < 0 || $correctIdx >= 4) { $correctIdx = 0; }
                    ?>
                    <div class="question-block border rounded p-3 mb-3" data-index="<?php echo e($qi); ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong class="text-muted">Question <?php echo e($qi + 1); ?></strong>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-question" aria-label="Remove">×</button>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">Question text <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="questions[<?php echo e($qi); ?>][question]" value="<?php echo e($q['question'] ?? ''); ?>" required placeholder="e.g. What is the main purpose of...">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">Options (at least 2; select the correct one)</label>
                            <?php $__currentLoopData = [0,1,2,3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="input-group input-group-sm mb-1">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="questions[<?php echo e($qi); ?>][correct_index]" value="<?php echo e($oi); ?>" <?php echo e($correctIdx === $oi ? 'checked' : ''); ?> required>
                                </div>
                                <input type="text" class="form-control" name="questions[<?php echo e($qi); ?>][options][<?php echo e($oi); ?>]" value="<?php echo e($opts[$oi] ?? ''); ?>" placeholder="Option <?php echo e($oi + 1); ?>">
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label small mb-1">Points</label>
                                <input type="number" class="form-control form-control-sm" name="questions[<?php echo e($qi); ?>][points]" value="<?php echo e($q['points'] ?? 1); ?>" min="1" max="100">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if(empty($qList)): ?>
                <p class="text-muted small mb-0" id="no-questions-msg">No questions yet. Click <strong>Add question</strong> to create one.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update Knowledge Check</button>
            <a href="<?php echo e(route('units.edit', [$course, $unit])); ?>" class="btn btn-outline-secondary">Back to unit</a>
            <a href="<?php echo e(route('courses.edit', $course)); ?>" class="btn btn-outline-secondary">Back to course</a>
        </div>
    </form>
</div>

<template id="question-tpl">

    <div class="question-block border rounded p-3 mb-3" data-index="{{ idx }}">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <strong class="text-muted">Question {{ idx1 }}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger remove-question" aria-label="Remove">×</button>
        </div>
        <div class="mb-2">
            <label class="form-label small mb-1">Question text <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" name="questions[{{ idx }}][question]" value="" required placeholder="e.g. What is the main purpose of...">
        </div>
        <div class="mb-2">
            <label class="form-label small mb-1">Options (at least 2; select the correct one)</label>
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-text"><input class="form-check-input mt-0" type="radio" name="questions[{{ idx }}][correct_index]" value="0" required></div>
                <input type="text" class="form-control" name="questions[{{ idx }}][options][0]" value="" placeholder="Option 1">
            </div>
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-text"><input class="form-check-input mt-0" type="radio" name="questions[{{ idx }}][correct_index]" value="1"></div>
                <input type="text" class="form-control" name="questions[{{ idx }}][options][1]" value="" placeholder="Option 2">
            </div>
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-text"><input class="form-check-input mt-0" type="radio" name="questions[{{ idx }}][correct_index]" value="2"></div>
                <input type="text" class="form-control" name="questions[{{ idx }}][options][2]" value="" placeholder="Option 3">
            </div>
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-text"><input class="form-check-input mt-0" type="radio" name="questions[{{ idx }}][correct_index]" value="3"></div>
                <input type="text" class="form-control" name="questions[{{ idx }}][options][3]" value="" placeholder="Option 4">
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <label class="form-label small mb-1">Points</label>
                <input type="number" class="form-control form-control-sm" name="questions[{{ idx }}][points]" value="1" min="1" max="100">
            </div>
        </div>
    </div>

</template>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('questions-container');
    var tpl = document.getElementById('question-tpl');
    if (!container || !tpl) return;

    document.getElementById('add-question').addEventListener('click', function() {
        document.getElementById('no-questions-msg')?.remove();
        var idx = container.querySelectorAll('.question-block').length;
        var html = tpl.innerHTML
            .replace(/\{\{\s*idx\s*\}\}/g, String(idx))
            .replace(/\{\{\s*idx1\s*\}\}/g, String(idx + 1));
        var div = document.createElement('div');
        div.innerHTML = html;
        container.appendChild(div.firstElementChild);
        reindex();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-question')) {
            var block = e.target.closest('.question-block');
            if (block && container.querySelectorAll('.question-block').length > 1) {
                block.remove();
                reindex();
            }
        }
    });

    function reindex() {
        container.querySelectorAll('.question-block').forEach(function(block, i) {
            block.dataset.index = i;
            block.querySelector('.text-muted').textContent = 'Question ' + (i + 1);
            block.querySelectorAll('[name^="questions["]').forEach(function(el) {
                el.name = el.name.replace(/questions\[\d+\]/, 'questions[' + i + ']');
            });
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.facilitator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/units/quiz-edit.blade.php ENDPATH**/ ?>