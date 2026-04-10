<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Learn'); ?> – <?php echo e(config('app.name')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --lms-accent: #dc3545; --lms-sidebar-width: 320px; }
        body { overflow: hidden; height: 100vh; }
        .learn-topbar { background: #2c2c2c; height: 48px; }
        .learn-sidebar { width: var(--lms-sidebar-width); background: #fff; border-right: 1px solid #dee2e6; overflow-y: auto; }
        .learn-course-hero { background: linear-gradient(180deg, rgba(0,0,0,.5) 0%, transparent 100%), #4a4a4a; background-size: cover; background-position: center; color: #fff; padding: 1.5rem; min-height: 140px; }
        .learn-course-hero h2 { font-size: 1.25rem; font-weight: 700; margin: 0 0 .5rem; }
        .learn-progress-pct { font-size: .75rem; opacity: .9; }
        .learn-progress-bar { height: 4px; background: rgba(255,255,255,.3); border-radius: 2px; overflow: hidden; }
        .learn-progress-bar .fill { height: 100%; background: #fff; border-radius: 2px; }
        .learn-standalones { border-bottom: 1px solid #eee; }
        .learn-day { border-bottom: 1px solid #eee; }
        .learn-day-header { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #495057; padding: .65rem 1rem; background: #f0f2f5; }
        .learn-trailing { border-top: 1px solid #eee; }
        .learn-module { border-bottom: 1px solid #eee; }
        .learn-module-header { display: flex; align-items: center; gap: .35rem; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; padding: .65rem 1rem; cursor: pointer; user-select: none; transition: background .15s; }
        .learn-module-header:hover { background: #f8f9fa; color: #495057; }
        .learn-module-header .learn-module-caret { font-size: .65rem; transition: transform .2s; }
        .learn-module.collapsed .learn-module-caret { transform: rotate(-90deg); }
        .learn-module-lessons { overflow: hidden; }
        .learn-module.collapsed .learn-module-lessons { display: none; }
        .learn-nav-item { display: flex; align-items: center; gap: .5rem; padding: .5rem 1rem; color: #212529; text-decoration: none; border-left: 3px solid transparent; transition: background .15s, border-color .15s; }
        .learn-nav-item:hover { background: #f8f9fa; color: #212529; }
        .learn-nav-item.active { background: rgba(220,53,69,.08); border-left-color: var(--lms-accent); color: var(--lms-accent); font-weight: 600; }
        .learn-nav-item .icon { width: 20px; text-align: center; color: #6c757d; }
        .learn-nav-item.active .icon { color: var(--lms-accent); }
        .learn-nav-item .flex-grow-1 { min-width: 0; }
        .learn-nav-item .circle { width: 18px; height: 18px; border-radius: 50%; border: 2px solid #dee2e6; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .learn-nav-item.done .circle { border-color: var(--lms-accent); background: var(--lms-accent); color: #fff; }
        .learn-nav-item.active .circle { border-color: var(--lms-accent); background: rgba(220,53,69,.2); }
        .learn-nav-item .circle i { font-size: .6rem; }
        .learn-nav-item-locked { cursor: not-allowed; color: #adb5bd; }
        .learn-nav-item-locked:hover { background: #f8f9fa; }
        .learn-quiz-q .form-check { margin-bottom: .35rem; }
        .learn-quiz-q .form-check:last-of-type { margin-bottom: 0; }
        .learn-main { flex: 1; overflow-y: auto; background: #fff; }
        .learn-content-wrap { max-width: 720px; margin: 0 auto; padding: 2rem 1.5rem 5rem; }
        .learn-meta { font-size: .85rem; color: #6c757d; margin-bottom: .25rem; }
        .learn-title { font-size: 1.75rem; font-weight: 700; margin-bottom: .5rem; }
        .learn-accent { width: 60px; height: 4px; background: var(--lms-accent); margin-bottom: 1.5rem; }
        .learn-body { line-height: 1.7; }
        .learn-attendance { border: 1px solid #dee2e6; border-radius: 8px; padding: 1.25rem; background: #f8f9fa; }
        .learn-attendance-form .form-label { margin-bottom: 0.2rem; }
        .learn-media-wrap { margin: 1.5rem 0; border-radius: 8px; overflow: hidden; background: #000; }
        .learn-media-wrap video, .learn-media-wrap iframe { width: 100%; display: block; }
        .learn-bottom-bar { position: fixed; bottom: 0; left: var(--lms-sidebar-width); right: 0; height: 56px; background: #f0f0f0; border-top: 1px solid #dee2e6; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; }
        .learn-bar-next { display: flex; align-items: center; gap: .5rem; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .learn-bar-next:hover { color: #0a58ca; }
        .learn-bar-prev { color: #6c757d; text-decoration: none; }
        .learn-bar-prev:hover { color: #212529; }
        .learn-btn-complete { background: var(--lms-accent); color: #fff; border: none; padding: .5rem 1.25rem; border-radius: 6px; font-weight: 500; }
        .learn-btn-complete:hover { background: #bb2d3b; color: #fff; }
        @media (max-width: 991.98px) {
            .learn-sidebar { position: fixed; left: 0; top: 48px; height: calc(100vh - 48px); z-index: 1030; transform: translateX(-100%); transition: transform .2s; }
            .learn-sidebar.show { transform: translateX(0); }
            .learn-main { margin-left: 0 !important; }
            .learn-bottom-bar { left: 0; }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="d-flex flex-column">
    <header class="learn-topbar d-flex align-items-center px-3">
        <button type="button" class="btn btn-link text-light me-2 d-lg-none" id="learn-sidebar-toggle" aria-label="Toggle menu">
            <i class="bi bi-list fs-5"></i>
        </button>
        <a href="<?php echo e(route('courses.index')); ?>" class="text-light text-decoration-none small me-3">Courses</a>
        <?php if (! empty(trim($__env->yieldContent('learn-back')))): ?>
        <?php echo $__env->yieldContent('learn-back'); ?>
        <?php endif; ?>
        <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('profile.edit')); ?>" class="text-light-50 text-decoration-none small ms-auto"><?php echo e(auth()->user()->name); ?></a>
        <a href="<?php echo e(route('courses.my')); ?>" class="text-light ms-2 small">Exit to my courses</a>
        <?php endif; ?>
    </header>
    <div class="d-flex flex-grow-1 overflow-hidden">
        <?php echo $__env->yieldContent('sidebar'); ?>
        <main class="learn-main flex-grow-1 ms-0">
            <?php if(session('message')): ?>
            <div class="alert alert-info mb-0 rounded-0"><?php echo e(session('message')); ?></div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    <?php if (! empty(trim($__env->yieldContent('bottom-bar')))): ?>
    <?php echo $__env->yieldContent('bottom-bar'); ?>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (! empty(trim($__env->yieldContent('chat')))): ?>
    <?php echo $__env->yieldContent('chat'); ?>
    <?php endif; ?>
    <script>
        document.getElementById('learn-sidebar-toggle')?.addEventListener('click', function() {
            document.querySelector('.learn-sidebar')?.classList.toggle('show');
        });
        document.querySelectorAll('.learn-module-header').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var mod = this.closest('.learn-module');
                if (!mod) return;
                mod.classList.toggle('collapsed');
                this.setAttribute('aria-expanded', mod.classList.contains('collapsed') ? 'false' : 'true');
            });
            btn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/layouts/learn.blade.php ENDPATH**/ ?>