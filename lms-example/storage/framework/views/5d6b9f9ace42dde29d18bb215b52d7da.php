<?php $__env->startSection('title', $course->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('courses.index')); ?>">Courses</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($course->title); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <h1 class="h2"><?php echo e($course->title); ?></h1>
            <?php if($course->short_description): ?>
            <p class="lead text-muted"><?php echo e($course->short_description); ?></p>
            <?php endif; ?>
            <p class="text-muted small">
                <i class="bi bi-people"></i> <?php echo e($course->enrollment_count); ?> students enrolled
                · <i class="bi bi-person"></i> <?php echo e($course->instructor?->name ?? 'Facilitator to be assigned'); ?>

                <?php if($course->rating_count > 0): ?>
                · <span class="text-warning"><?php echo e(str_repeat('★', (int) round($course->rating))); ?><?php echo e(str_repeat('☆', 5 - (int) round($course->rating))); ?></span> <?php echo e(number_format($course->rating, 1)); ?> (<?php echo e($course->rating_count); ?>)
                <?php endif; ?>
            </p>

            <div class="mt-4">
                <h3 class="h5">Course Description</h3>
                <div class="course-description">
                    <?php echo nl2br(e($course->description ?? 'No description.')); ?>

                </div>
            </div>

            <h3 class="h5 mt-4">Course Curriculum</h3>
            <div class="course-curriculum table-responsive">
                <table class="table table-sm">
                    <tbody>
                    <?php $__currentLoopData = $course->curriculum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-muted" style="width:40px;"><i class="<?php echo e($item['icon'] ?? 'bi bi-file-text'); ?>"></i></td>
                        <td><?php echo e($item['title']); ?></td>
                        <td class="text-muted small"><?php echo e($item['duration'] ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php if($course->curriculum->isEmpty()): ?>
            <p class="text-muted">No curriculum yet.</p>
            <?php endif; ?>

            <h3 class="h5 mt-4">Reviews</h3>
            <?php if($course->rating_count > 0): ?>
            <p class="text-muted small">
                <span class="text-warning"><?php echo e(str_repeat('★', (int) round($course->rating))); ?><?php echo e(str_repeat('☆', 5 - (int) round($course->rating))); ?></span>
                <?php echo e(number_format($course->rating, 1)); ?> · <?php echo e($course->rating_count); ?> rating<?php echo e($course->rating_count !== 1 ? 's' : ''); ?>

            </p>
            <?php endif; ?>
            <?php if(auth()->guard()->check()): ?>
            <?php if($enrolled && !$myReview): ?>
            <form action="<?php echo e(route('courses.reviews.store', $course)); ?>" method="post" class="mb-4">
                <?php echo csrf_field(); ?>
                <div class="mb-2">
                    <label class="form-label">Your rating</label>
                    <div>
                        <?php $__currentLoopData = [1,2,3,4,5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="rating" id="r<?php echo e($s); ?>" value="<?php echo e($s); ?>" <?php echo e((int) old('rating') === $s ? 'checked' : ''); ?> required>
                            <label class="form-check-label" for="r<?php echo e($s); ?>"><?php echo e($s); ?> ★</label>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="review_text" class="form-label">Review (optional)</label>
                    <textarea name="review" id="review_text" class="form-control" rows="3" maxlength="2000"><?php echo e(old('review')); ?></textarea>
                </div>
                <button type="submit" class="btn btn-outline-primary btn-sm">Submit review</button>
            </form>
            <?php elseif($myReview): ?>
            <p class="text-muted small mb-2">Your rating: <span class="text-warning"><?php echo e(str_repeat('★', $myReview->rating)); ?><?php echo e(str_repeat('☆', 5 - $myReview->rating)); ?></span></p>
            <?php if($myReview->review): ?><p class="small"><?php echo e($myReview->review); ?></p><?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>
            <?php if($reviews->isNotEmpty()): ?>
            <ul class="list-unstyled">
                <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="mb-3 pb-3 border-bottom">
                    <span class="text-warning"><?php echo e(str_repeat('★', $rev->rating)); ?><?php echo e(str_repeat('☆', 5 - $rev->rating)); ?></span>
                    <strong><?php echo e($rev->user->name ?? 'User'); ?></strong>
                    <span class="text-muted small"><?php echo e($rev->created_at?->format('d M Y')); ?></span>
                    <?php if($rev->review): ?><p class="mb-0 small mt-1"><?php echo e($rev->review); ?></p><?php endif; ?>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <p class="text-muted small">No reviews yet.</p>
            <?php endif; ?>
        </div>
        <div class="col-lg-4">
            <?php if($course->instructor): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Your facilitator</h6>
                </div>
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
                        <i class="bi bi-person fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <strong><?php echo e($course->instructor->name); ?></strong>
                        <p class="small mb-0 mt-1">This course is facilitated by <strong><?php echo e($course->instructor->name); ?></strong>. Delegates will know who is leading the course.</p>
                    </div>
                </div>
                <?php if($enrolled && $enrollment && (int) $enrollment->progress_percentage >= 100): ?>
                <div class="card-footer bg-transparent border-top pt-2">
                    <?php if($hasRated && $facilitatorRating): ?>
                    <p class="small mb-0 text-muted"><span class="text-warning"><?php echo e(str_repeat('★', $facilitatorRating->rating)); ?><?php echo e(str_repeat('☆', 5 - $facilitatorRating->rating)); ?></span> You rated your facilitator. <?php if($facilitatorRating->review): ?><br><em><?php echo e($facilitatorRating->review); ?></em><?php endif; ?></p>
                    <?php else: ?>
                    <form action="<?php echo e(route('courses.rate-facilitator', $course)); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <label class="form-label small mb-1">Rate how your facilitator taught this course</label>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <?php $__currentLoopData = [1,2,3,4,5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="rating" id="fr<?php echo e($s); ?>" value="<?php echo e($s); ?>" <?php echo e((int) old('rating') === $s ? 'checked' : ''); ?> required>
                                <label class="form-check-label small" for="fr<?php echo e($s); ?>"><?php echo e($s); ?> ★</label>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <textarea name="review" class="form-control form-control-sm mt-2" rows="2" placeholder="Optional feedback" maxlength="2000"><?php echo e(old('review')); ?></textarea>
                        <button type="submit" class="btn btn-sm btn-outline-primary mt-2">Submit rating</button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="card shadow-sm sticky-top" style="top: 1rem;">
                <?php if($course->featured_image): ?>
                <img src="<?php echo e(asset('storage/' . $course->featured_image)); ?>" class="card-img-top" alt="">
                <?php else: ?>
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height:180px;">
                    <i class="bi bi-journal-text text-white display-3"></i>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if(isset($canEditThisCourse) && $canEditThisCourse): ?>
                    <a href="<?php echo e(route('courses.edit', $course)); ?>" class="btn btn-outline-primary w-100 mb-2"><i class="bi bi-pencil me-1"></i> Edit course</a>
                    <?php endif; ?>
                    <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('courses.attendance', $course)); ?>" class="btn btn-outline-secondary w-100 mb-2"><i class="bi bi-person-lines-fill me-1"></i> Attendance register</a>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if($enrolled): ?>
                    <a href="<?php echo e(route('learn.show', ['course' => $course, 'start' => 1])); ?>" class="btn btn-primary w-100 <?php echo e(!empty($certificate) ? 'mb-2' : ''); ?>">Continue Learning</a>
                    <?php if(!empty($certificate)): ?>
                    <a href="<?php echo e(route('certificates.show', $certificate)); ?>" class="btn btn-outline-success w-100"><i class="bi bi-award me-1"></i> View certificate</a>
                    <?php endif; ?>
                    <?php else: ?>
                    <p class="text-muted small mb-2"><i class="bi bi-lock me-1"></i> This course is locked. Enroll to unlock.</p>
                    <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('courses.enroll', $course)); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-primary w-100">Enroll to unlock</button>
                    </form>
                    <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-primary w-100">Login to Enroll</a>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_1\lms\resources\views/courses/show.blade.php ENDPATH**/ ?>