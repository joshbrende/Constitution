<?php $__env->startSection('title', 'Edit unit: ' . $unit->title); ?>

<?php $__env->startSection('content'); ?>
<div class="px-0 px-md-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('instructor.dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.instructor')); ?>">Instructing</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.show', $course)); ?>"><?php echo e(\Illuminate\Support\Str::limit($course->title, 35)); ?></a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.edit', $course)); ?>">Edit course</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit unit</li>
        </ol>
    </nav>

    <h1 class="h2">Edit unit</h1>
    <p class="text-muted"><?php echo e($unit->title); ?></p>

    <form action="<?php echo e(route('units.update', [$course, $unit])); ?>" method="post" class="mb-4">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="title" name="title"
                   value="<?php echo e(old('title', $unit->title)); ?>" required>
            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mb-3">
            <label for="unit_type" class="form-label">Type <span class="text-danger">*</span></label>
            <select class="form-select <?php $__errorArgs = ['unit_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="unit_type" name="unit_type" required>
                <option value="text" <?php echo e(old('unit_type', $unit->unit_type) === 'text' ? 'selected' : ''); ?>>Lesson (text)</option>
                <option value="video" <?php echo e(old('unit_type', $unit->unit_type) === 'video' ? 'selected' : ''); ?>>Video</option>
                <option value="audio" <?php echo e(old('unit_type', $unit->unit_type) === 'audio' ? 'selected' : ''); ?>>Audio</option>
                <option value="document" <?php echo e(old('unit_type', $unit->unit_type) === 'document' ? 'selected' : ''); ?>>Document</option>
                <option value="assignment" <?php echo e(old('unit_type', $unit->unit_type) === 'assignment' ? 'selected' : ''); ?>>Assignment</option>
                <option value="quiz" <?php echo e(old('unit_type', $unit->unit_type) === 'quiz' ? 'selected' : ''); ?>>Knowledge Check</option>
            </select>
            <?php $__errorArgs = ['unit_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="content" name="content"
                      rows="16" placeholder="Use the toolbar to format: headings, bold, lists, links. What you see is how learners will see it."><?php echo e(old('content', $unit->content)); ?></textarea>
            <small class="text-muted">Use the toolbar to format text like in Word or WordPress—no HTML needed. Use the <strong>Paragraph ▼</strong> dropdown for headings. <em>Code</em> shows the raw HTML only if you need it; avoid changing step markers in lessons with multiple steps.</small>
            <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <?php if($unit->title === 'Module 1: Introduction'): ?>
        <div class="mb-4 p-3 border rounded bg-light">
            <h6 class="mb-2"><i class="bi bi-file-earmark-arrow-down me-2"></i>Reload from template</h6>
            <p class="small text-muted mb-2">Replace the content above with the latest <code>01_introduction.md</code> (Module 1: Understanding SALGA 2026 Context). This overwrites your current content and sets duration to 6 minutes.</p>
            <form action="<?php echo e(route('units.refresh-from-file', [$course, $unit])); ?>" method="post" class="d-inline" onsubmit="return confirm('This will overwrite the current content. Continue?');">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-clockwise me-1"></i>Reload from 01_introduction.md</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="description" class="form-label">Short description</label>
            <input type="text" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="description" name="description"
                   value="<?php echo e(old('description', $unit->description)); ?>" maxlength="500" placeholder="Optional summary">
            <?php $__errorArgs = ['description'];
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
                <label for="duration" class="form-label">Duration (minutes)</label>
                <input type="number" class="form-control <?php $__errorArgs = ['duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="duration" name="duration"
                       value="<?php echo e(old('duration', $unit->duration)); ?>" min="0" max="999" placeholder="e.g. 15">
                <?php $__errorArgs = ['duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-4">
                <label for="order" class="form-label">Order</label>
                <input type="number" class="form-control <?php $__errorArgs = ['order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="order" name="order"
                       value="<?php echo e(old('order', $unit->order)); ?>" min="0" placeholder="0">
                <small class="text-muted">Lower = earlier in curriculum.</small>
                <?php $__errorArgs = ['order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label for="video_url" class="form-label">Video URL</label>
            <input type="url" class="form-control <?php $__errorArgs = ['video_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="video_url" name="video_url"
                   value="<?php echo e(old('video_url', $unit->video_url)); ?>" placeholder="YouTube, Vimeo, or direct URL">
            <?php $__errorArgs = ['video_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="mb-3">
            <label for="audio_url" class="form-label">Audio URL</label>
            <input type="url" class="form-control <?php $__errorArgs = ['audio_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="audio_url" name="audio_url"
                   value="<?php echo e(old('audio_url', $unit->audio_url)); ?>">
            <?php $__errorArgs = ['audio_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="mb-3">
            <label for="document_url" class="form-label">Document URL</label>
            <input type="url" class="form-control <?php $__errorArgs = ['document_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="document_url" name="document_url"
                   value="<?php echo e(old('document_url', $unit->document_url)); ?>">
            <?php $__errorArgs = ['document_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <?php if($unit->unit_type === 'quiz' && $unit->quiz): ?>
        <div class="alert alert-info">
            <i class="bi bi-question-circle me-2"></i>This unit is linked to Knowledge Check <strong><?php echo e($unit->quiz->title); ?></strong>.
            Questions are managed separately. Changing type above does not remove the link.
        </div>
        <?php endif; ?>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update unit</button>
            <a href="<?php echo e(route('courses.edit', $course)); ?>" class="btn btn-outline-secondary">Back to course</a>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.tiny.cloud/1/<?php echo e(config('services.tinymce.key')); ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#content',
        height: 480,
        plugins: 'lists link code',
        toolbar: 'undo redo | formatselect | bold italic | bullist numlist | link | code',
        block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3',
        extended_valid_elements: 'div[class|data-step-title]',
        promotion: false,
        branding: false,
        placeholder: 'Use the toolbar to add headings, lists, bold text, and links. Click "Source code" only if you need to edit HTML.'
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.facilitator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/units/edit.blade.php ENDPATH**/ ?>