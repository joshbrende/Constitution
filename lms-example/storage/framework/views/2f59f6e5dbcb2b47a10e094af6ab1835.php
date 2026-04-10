<?php $__env->startSection('title', 'Certificate templates'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Certificate templates</li>
        </ol>
    </nav>

    <h1 class="h2 mb-1">Certificate templates</h1>
    <p class="text-muted mb-4">Add PDF templates for certificates (upload or select a file already on the server). Each course can use one template; the certificate title comes from the course. Assign a template to a course from the course edit page.</p>

    <?php if(session('message')): ?>
    <div class="alert alert-success"><?php echo e(session('message')); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add template</h5>
        </div>
        <div class="card-body">
            <p class="small text-muted mb-3">Upload a PDF via the form, or upload via FTP to <code>public/asset/</code> and select it below (good for large files, e.g. 11 MB+).</p>

            <form action="<?php echo e(route('admin.certificate-templates.store')); ?>" method="post" enctype="multipart/form-data" id="form-upload">
                <?php echo csrf_field(); ?>
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Template name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" placeholder="e.g. Performance training" value="<?php echo e(old('name')); ?>" required>
                        <small class="text-muted">Used when assigning to a course.</small>
                    </div>
                    <div class="col-md-4">
                        <label for="template" class="form-label">PDF file (upload)</label>
                        <input type="file" name="template" id="template" class="form-control form-control-sm" accept=".pdf">
                        <small class="text-muted">PDF, max 100 MB. Leave empty if selecting from server below.</small>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                    </div>
                </div>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['template'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </form>

            <?php if(!empty($existingPdfs)): ?>
            <hr class="my-3">
            <p class="small fw-semibold mb-2">Or select a file already on the server (e.g. uploaded via FTP to <code>public/asset/</code>):</p>
            <form action="<?php echo e(route('admin.certificate-templates.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="name_existing" class="form-label">Template name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name_existing" class="form-control form-control-sm" placeholder="e.g. Performance training" required>
                    </div>
                    <div class="col-md-4">
                        <label for="existing_file_path" class="form-label">PDF file</label>
                        <select name="existing_file" id="existing_file_path" class="form-select form-select-sm" required>
                            <option value="">— Choose a file —</option>
                            <?php $__currentLoopData = $existingPdfs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $basename => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($path); ?>"><?php echo e($basename); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary btn-sm">Use this file</button>
                    </div>
                </div>
                <?php $__errorArgs = ['existing_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </form>
            <?php else: ?>
            <p class="small text-muted mb-0">No PDF files found in <code>public/asset/</code>. Upload PDFs there via FTP to see them here.</p>
            <?php endif; ?>
        </div>
    </div>

    <h5 class="mb-2">Templates</h5>
    <?php if($templates->isEmpty()): ?>
    <p class="text-muted">No certificate templates yet. Upload one above.</p>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>File</th>
                    <th>Courses using</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($t->name); ?></td>
                    <td>
                        <?php if($t->fileExists()): ?>
                        <span class="text-success">PDF present</span>
                        <?php if($t->isPublicPath()): ?>
                        <span class="badge bg-secondary ms-1">On server</span>
                        <?php endif; ?>
                        <?php else: ?>
                        <span class="text-danger">File missing</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($t->courses()->count()); ?></td>
                    <td>
                        <form action="<?php echo e(route('admin.certificate-templates.destroy', $t)); ?>" method="post" class="d-inline" onsubmit="return confirm('Remove this template? Courses using it will fall back to the default certificate.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/admin/certificate-templates.blade.php ENDPATH**/ ?>