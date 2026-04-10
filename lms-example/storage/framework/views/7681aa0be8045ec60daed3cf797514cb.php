<?php $__env->startSection('title', $course->title . ' – Learn'); ?>

<?php $__env->startSection('learn-back'); ?>
<a href="<?php echo e(route('courses.show', $course)); ?>" class="text-light text-decoration-none small me-3">Back to course</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('sidebar'); ?>
<aside class="learn-sidebar" id="learn-sidebar">
    <div class="learn-course-hero" <?php if($course->featured_image): ?> style="background-image: url('<?php echo e(asset('storage/' . $course->featured_image)); ?>')" <?php endif; ?>>
        <h2><?php echo e($course->title); ?></h2>
        <div class="learn-progress-pct"><?php echo e($enrollment->progress_percentage ?? 0); ?>% COMPLETE</div>
        <div class="learn-progress-bar mt-1">
            <div class="fill" style="width: <?php echo e($enrollment->progress_percentage ?? 0); ?>%"></div>
        </div>
    </div>
    <nav class="py-2">
        <?php
            $days = $structuredCurriculum['days'] ?? [];
            $trailing = $structuredCurriculum['trailing'] ?? collect();
            $currentId = $current ? (int) $current->id : null;
            $unlocked = $unlockedUnitIds ?? collect();
        ?>
        <?php $__currentLoopData = [1, 2, 3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $day = $days[$d] ?? null;
            if (!$day) continue;
            $standalones = $day['standalones'] ?? collect();
            $modules = $day['modules'] ?? [];
            ksort($modules);
        ?>
        <div class="learn-day" data-day="<?php echo e($d); ?>">
            <div class="learn-day-header">DAY <?php echo e($d); ?></div>
            <?php $__currentLoopData = $standalones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $active = $current && (int) $current->id === (int) $item['id'];
                $done = isset($progress[$item['id']]) && $progress[$item['id']]->completed_at;
                $locked = !$unlocked->contains($item['id']);
            ?>
            <?php if($locked): ?>
            <div class="learn-nav-item learn-nav-item-locked" title="Pass the previous module's Knowledge Check to unlock">
                <span class="icon"><i class="bi bi-lock"></i></span>
                <span class="flex-grow-1 text-truncate"><?php echo e($item['title']); ?></span>
                <span class="circle"></span>
            </div>
            <?php else: ?>
            <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $item['id']])); ?>"
               class="learn-nav-item <?php echo e($active ? 'active' : ''); ?> <?php echo e($done ? 'done' : ''); ?>">
                <span class="icon"><i class="bi <?php echo e($item['icon'] ?? 'bi-file-text'); ?>"></i></span>
                <span class="flex-grow-1 text-truncate"><?php echo e($item['title']); ?></span>
                <span class="circle"><?php echo $done ? '<i class="bi bi-check"></i>' : ''; ?></span>
            </a>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modIndex => $moduleItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $hasActive = $currentId && $moduleItems->contains(fn ($i) => (int) $i['id'] === $currentId);
                $expandFirst = !$currentId && $d === 1 && $modIndex === array_key_first($modules);
                $isExpanded = $hasActive || $expandFirst;
            ?>
            <div class="learn-module <?php echo e($isExpanded ? '' : 'collapsed'); ?>" data-module="<?php echo e($modIndex); ?>">
                <div class="learn-module-header" role="button" tabindex="0" aria-expanded="<?php echo e($isExpanded ? 'true' : 'false'); ?>">
                    <i class="bi bi-chevron-down learn-module-caret"></i>
                    <span>MODULE <?php echo e($modIndex); ?></span>
                </div>
                <div class="learn-module-lessons">
                    <?php $__currentLoopData = $moduleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $active = $current && (int) $current->id === (int) $item['id'];
                        $done = isset($progress[$item['id']]) && $progress[$item['id']]->completed_at;
                        $locked = !$unlocked->contains($item['id']);
                    ?>
                    <?php if($locked): ?>
                    <div class="learn-nav-item learn-nav-item-locked" title="Pass the previous module's Knowledge Check to unlock">
                        <span class="icon"><i class="bi bi-lock"></i></span>
                        <span class="flex-grow-1 text-truncate"><?php echo e($item['title']); ?></span>
                        <span class="circle"></span>
                    </div>
                    <?php else: ?>
                    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $item['id']])); ?>"
                       class="learn-nav-item <?php echo e($active ? 'active' : ''); ?> <?php echo e($done ? 'done' : ''); ?>">
                        <span class="icon"><i class="bi <?php echo e($item['icon'] ?? 'bi-file-text'); ?>"></i></span>
                        <span class="flex-grow-1 text-truncate"><?php echo e($item['title']); ?></span>
                        <span class="circle"><?php echo $done ? '<i class="bi bi-check"></i>' : ''; ?></span>
                    </a>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($trailing->isNotEmpty()): ?>
        <div class="learn-trailing">
            <?php $__currentLoopData = $trailing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $active = $current && (int) $current->id === (int) $item['id'];
                $done = isset($progress[$item['id']]) && $progress[$item['id']]->completed_at;
                $locked = !$unlocked->contains($item['id']);
            ?>
            <?php if($locked): ?>
            <div class="learn-nav-item learn-nav-item-locked" title="Pass the previous module's Knowledge Check to unlock">
                <span class="icon"><i class="bi bi-lock"></i></span>
                <span class="flex-grow-1 text-truncate"><?php echo e($item['title']); ?></span>
                <span class="circle"></span>
            </div>
            <?php else: ?>
            <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $item['id']])); ?>"
               class="learn-nav-item <?php echo e($active ? 'active' : ''); ?> <?php echo e($done ? 'done' : ''); ?>">
                <span class="icon"><i class="bi <?php echo e($item['icon'] ?? 'bi-file-text'); ?>"></i></span>
                <span class="flex-grow-1 text-truncate"><?php echo e($item['title']); ?></span>
                <span class="circle"><?php echo $done ? '<i class="bi bi-check"></i>' : ''; ?></span>
            </a>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </nav>
</aside>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="learn-content-wrap">
    <?php if(!$current): ?>
    <div class="text-center py-5">
        <p class="text-muted mb-3">Select a lesson from the sidebar to begin.</p>
        <?php if($totalUnits > 0): ?>
        <a href="<?php echo e(route('learn.show', ['course' => $course, 'start' => 1])); ?>" class="btn btn-danger">Start course</a>
        <?php else: ?>
        <p class="text-muted small">No lessons in this course yet.</p>
        <?php endif; ?>
    </div>
    <?php elseif($current->unit_type === 'quiz' || $current->unit_type === 'assignment'): ?>
    <p class="learn-meta">Unit <?php echo e($currentIndex); ?> of <?php echo e($totalUnits); ?></p>
    <h1 class="learn-title"><?php echo e($current->title); ?></h1>
    <div class="learn-accent"></div>
    <?php if(!empty($currentLocked)): ?>
    <div class="alert alert-warning"><i class="bi bi-lock me-2"></i>This module is locked. Pass the previous module's Knowledge Check to unlock it.</div>
    <?php elseif($current->unit_type === 'assignment'): ?>
    <?php if($assignmentSubmission): ?>
    <div class="alert alert-<?php echo e($assignmentSubmission->isGraded() ? 'success' : 'info'); ?> mb-4">
        <?php if($assignmentSubmission->isGraded()): ?>
        <i class="bi bi-check-circle me-2"></i>Graded: <?php echo e($assignmentSubmission->score); ?>/<?php echo e($assignmentSubmission->max_points); ?>.
        <?php if($assignmentSubmission->instructor_feedback): ?>
        <div class="mt-2"><strong>Feedback:</strong> <?php echo e($assignmentSubmission->instructor_feedback); ?></div>
        <?php endif; ?>
        <?php else: ?>
        <i class="bi bi-send me-2"></i>Your assignment has been submitted. Your instructor will grade it.
        <?php endif; ?>
    </div>
    <?php if(!empty($assignmentSubmission->attachments)): ?>
    <p class="text-muted small mb-2">Attachments:</p>
    <ul class="list-unstyled mb-3">
        <?php $__currentLoopData = $assignmentSubmission->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><a href="<?php echo e(asset('storage/' . ($att['path'] ?? ''))); ?>" target="_blank" rel="noopener"><?php echo e($att['name'] ?? 'File'); ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <?php endif; ?>
    <?php if($nextUnit && !$nextLocked): ?>
    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $next])); ?>" class="btn btn-outline-primary">Continue to next &rarr;</a>
    <?php elseif($currentIndex >= $totalUnits): ?>
    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $current->id, 'finished' => 1])); ?>" class="btn btn-success me-2">View course complete</a>
    <a href="<?php echo e(route('courses.my')); ?>" class="btn btn-outline-secondary">My courses</a>
    <?php endif; ?>
    <?php elseif($assignment): ?>
    <div class="learn-body mb-4">
        <?php if($assignment->instructions): ?><?php echo nl2br(e($assignment->instructions)); ?><?php endif; ?>
        <p class="text-muted small mt-2">Max <?php echo e($assignment->max_points); ?> points. <?php if($assignment->allow_file_upload): ?> You may upload up to 5 files (PDF, DOC, DOCX, TXT, JPG, PNG; max 5 MB each). <?php endif; ?></p>
    </div>
    <form action="<?php echo e(route('learn.assignment.submit', [$course, $current])); ?>" method="post" enctype="multipart/form-data" class="learn-assignment-form">
        <?php echo csrf_field(); ?>
        <?php if($errors->any()): ?>
        <div class="alert alert-warning mb-3"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>
        <div class="mb-3">
            <label for="assign_content" class="form-label">Your response <span class="text-danger">*</span></label>
            <textarea name="content" id="assign_content" class="form-control" rows="8" required><?php echo e(old('content')); ?></textarea>
        </div>
        <?php if($assignment->allow_file_upload): ?>
        <div class="mb-3">
            <label for="assign_files" class="form-label">Attachments (optional)</label>
            <input type="file" name="attachments[]" id="assign_files" class="form-control" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-danger">Submit assignment</button>
    </form>
    <?php else: ?>
    <div class="alert alert-info">This assignment is not configured yet.</div>
    <?php endif; ?>
    <?php elseif(!empty($showQuizResults) && !empty($quizResultsAttempt)): ?>
    <?php
        $attempt = $quizResultsAttempt;
        $passed = $attempt->status === 'passed';
        $ans = $attempt->answers ?? [];
    ?>
    <div class="alert <?php echo e($passed ? 'alert-success' : 'alert-warning'); ?> mb-4">
        <i class="bi <?php echo e($passed ? 'bi-check-circle' : 'bi-exclamation-circle'); ?> me-2"></i>
        <?php echo e($passed ? 'Knowledge Check passed!' : 'Knowledge Check not passed.'); ?> You scored <?php echo e($attempt->score); ?>/<?php echo e($attempt->total_points); ?> (<?php echo e(number_format($attempt->percentage, 1)); ?>%).
        <?php if($nextUnit && !$nextLocked && $passed): ?> The next module is unlocked. <?php elseif($passed && !$nextUnit): ?> This was the final Knowledge Check—you've completed the course! <?php endif; ?>
    </div>
    <h3 class="h6 mb-2">Your answers</h3>
    <div class="mb-4">
        <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $a = $ans[$q->id] ?? null;
            $correct = $a['correct'] ?? false;
            $val = $a['value'] ?? null;
        ?>
        <div class="card mb-2">
            <div class="card-body py-2 <?php echo e($correct ? 'border-start border-4 border-success' : 'border-start border-4 border-danger'); ?>">
                <div class="d-flex align-items-start">
                    <span class="me-2"><?php echo e($correct ? '✓' : '✗'); ?></span>
                    <div>
                        <strong><?php echo e($i + 1); ?>. <?php echo e($q->question); ?></strong>
                        <?php
                            $display = $val ?? '—';
                            if ($q->type === 'true_false') {
                                $display = $val === '1' ? 'True' : ($val === '0' ? 'False' : $display);
                            } elseif ($val !== null && $val !== '' && !empty($q->options) && is_array($q->options)) {
                                foreach ($q->options as $o) {
                                    if (($o['value'] ?? '') === $val) { $display = $o['text'] ?? $val; break; }
                                }
                            }
                        ?>
                        <p class="mb-0 small text-muted">Your answer: <?php echo e($display); ?></p>
                        <?php if(!$correct && $q->explanation): ?><p class="mb-0 small mt-1"><?php echo e($q->explanation); ?></p><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php if($passed): ?>
    <?php if($nextUnit && !$nextLocked): ?>
    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $next])); ?>" class="btn btn-primary">Continue to next &rarr;</a>
    <?php else: ?>
    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $current->id, 'finished' => 1])); ?>" class="btn btn-success me-2">View course complete</a>
    <a href="<?php echo e(route('courses.show', $course)); ?>" class="btn btn-outline-secondary me-2">Back to course</a>
    <a href="<?php echo e(route('courses.my')); ?>" class="btn btn-outline-secondary">My courses</a>
    <?php endif; ?>
    <?php else: ?>
    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $current->id])); ?>" class="btn btn-outline-primary">Try again</a>
    <?php endif; ?>
    <?php elseif(!empty($quizPassed)): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>You have passed this Knowledge Check. <?php if($nextUnit && !$nextLocked): ?> The next module is unlocked. <?php else: ?> This was the final Knowledge Check—you've completed the course! <?php endif; ?></div>
    <?php if($nextUnit && !$nextLocked): ?>
    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $next])); ?>" class="btn btn-outline-primary mt-2">Continue to next &rarr;</a>
    <?php else: ?>
    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $current->id, 'finished' => 1])); ?>" class="btn btn-success mt-2 me-2">View course complete</a>
    <a href="<?php echo e(route('courses.show', $course)); ?>" class="btn btn-outline-secondary mt-2 me-2">Back to course</a>
    <a href="<?php echo e(route('courses.my')); ?>" class="btn btn-outline-secondary mt-2">My courses</a>
    <?php endif; ?>
    <?php elseif($quiz && $quiz->questions->isNotEmpty()): ?>
    <div class="learn-body mb-4">
        <?php if($quiz->instructions): ?><p><?php echo e($quiz->instructions); ?></p><?php endif; ?>
        <p class="text-muted small mt-2">Pass mark: <?php echo e($quiz->pass_percentage); ?>%. You may retake the Knowledge Check if you do not pass.</p>
    </div>
    <form action="<?php echo e(route('learn.quiz.submit', [$course, $current])); ?>" method="post" class="learn-quiz-form">
        <?php echo csrf_field(); ?>
        <?php if($errors->any()): ?>
        <div class="alert alert-warning mb-3">Please answer all questions.</div>
        <?php endif; ?>
        <div class="learn-quiz-questions">
            <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card mb-3 learn-quiz-q">
                <div class="card-body">
                    <p class="fw-semibold mb-3"><?php echo e($i + 1); ?>. <?php echo e($q->question); ?></p>
                    <?php if($q->type === 'true_false'): ?>
                    <div class="learn-quiz-opts">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[<?php echo e($q->id); ?>]" id="q<?php echo e($q->id); ?>_t" value="1" required>
                            <label class="form-check-label" for="q<?php echo e($q->id); ?>_t">True</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[<?php echo e($q->id); ?>]" id="q<?php echo e($q->id); ?>_f" value="0">
                            <label class="form-check-label" for="q<?php echo e($q->id); ?>_f">False</label>
                        </div>
                    </div>
                    <?php elseif($q->type === 'multiple_choice' && !empty($q->options)): ?>
                    <div class="learn-quiz-opts">
                        <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[<?php echo e($q->id); ?>]" id="q<?php echo e($q->id); ?>_v<?php echo e($loop->index); ?>" value="<?php echo e($opt['value'] ?? ''); ?>" required>
                            <label class="form-check-label" for="q<?php echo e($q->id); ?>_v<?php echo e($loop->index); ?>"><?php echo e($opt['text'] ?? ''); ?></label>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php elseif($q->type === 'short_answer'): ?>
                    <div class="learn-quiz-opts">
                        <input type="text" class="form-control" name="answers[<?php echo e($q->id); ?>]" id="q<?php echo e($q->id); ?>_sa" value="<?php echo e(old('answers.'.$q->id)); ?>" required placeholder="Your answer" maxlength="500">
                    </div>
                    <?php endif; ?>
                    <small class="text-muted"><?php echo e($q->points); ?> point<?php echo e($q->points !== 1 ? 's' : ''); ?></small>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <button type="submit" class="btn btn-danger learn-btn-complete"><i class="bi bi-send me-1"></i> Submit Knowledge Check</button>
    </form>
    <?php else: ?>
    <div class="alert alert-info">No questions in this Knowledge Check yet.</div>
    <?php endif; ?>
    <?php else: ?>
    <p class="learn-meta">Lesson <?php echo e($currentIndex); ?> of <?php echo e($totalUnits); ?></p>
    <h1 class="learn-title"><?php echo e($current->title); ?></h1>
    <div class="learn-accent"></div>
    <?php if($current->duration): ?>
    <p class="text-muted small mb-3"><i class="bi bi-clock me-1"></i><?php echo e((int) $current->duration); ?> min</p>
    <?php endif; ?>
    <div class="learn-body">
        <?php
            $body = $current->content ?: '<p class="text-muted">No content.</p>';
            $body = str_replace('**Time Slot:**', 'Time Slot:', $body);
        ?>
        <?php if(!empty($steps) && count($steps) > 0): ?>
        <div class="learn-stepper" data-total="<?php echo e(count($steps)); ?>">
            <div class="learn-stepper-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <span class="text-muted small">Step <span class="learn-step-num">1</span> of <?php echo e(count($steps)); ?></span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm learn-step-prev" disabled><i class="bi bi-chevron-left me-1"></i> Previous</button>
                    <button type="button" class="btn btn-primary btn-sm learn-step-next"><span class="learn-step-next-text">Next</span> <i class="bi bi-chevron-right ms-1"></i></button>
                </div>
            </div>
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="learn-step-panel" data-step="<?php echo e($i + 1); ?>" style="display:<?php echo e($i === 0 ? 'block' : 'none'); ?>;">
                <?php echo $s['content']; ?>

            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <p class="learn-step-done-hint text-muted small mt-3" id="learn-step-done-hint" style="display:none;">You've seen all steps. Mark as finished and we proceed to the next lesson.</p>
        </div>
        <?php $__env->startPush('scripts'); ?>
        <script>
        (function(){
            var st = document.querySelector('.learn-stepper');
            if (!st) return;
            var panels = st.querySelectorAll('.learn-step-panel');
            var num = st.querySelector('.learn-step-num');
            var prev = st.querySelector('.learn-step-prev');
            var next = st.querySelector('.learn-step-next');
            var txt = st.querySelector('.learn-step-next-text');
            var hint = st.querySelector('.learn-step-done-hint');
            var markWrap = document.getElementById('learn-mark-finished-wrap');
            var total = panels.length;
            var cur = 1;
            function go(n) {
                if (n < 1 || n > total) return;
                cur = n;
                panels.forEach(function(p, i){ p.style.display = (i+1) === cur ? 'block' : 'none'; });
                if (num) num.textContent = cur;
                if (prev) prev.disabled = cur <= 1;
                if (next && txt) {
                    if (cur >= total) { next.disabled = true; next.classList.add('btn-outline-secondary'); next.classList.remove('btn-primary'); txt.textContent = 'Finish'; } else { next.disabled = false; next.classList.remove('btn-outline-secondary'); next.classList.add('btn-primary'); txt.textContent = 'Next'; }
                }
                if (hint) hint.style.display = (cur >= total) ? 'block' : 'none';
                if (markWrap) markWrap.style.display = (cur >= total) ? 'block' : 'none';
            }
            if (prev) prev.addEventListener('click', function(){ go(cur-1); });
            if (next) next.addEventListener('click', function(){ if (cur < total) go(cur+1); });
            go(1);
        })();
        </script>
        <?php $__env->stopPush(); ?>
        <?php else: ?>
        <?php echo $body; ?>

        <?php endif; ?>
    </div>

    <?php if($current): ?>
    <div class="card mt-4">
        <div class="card-header">
            <i class="bi bi-pencil-square me-2"></i>My notes
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('learn.notes.store', [$course, $current])); ?>" method="post" id="learn-notes-form">
                <?php echo csrf_field(); ?>
                <div class="mb-2">
                    <textarea
                        name="body"
                        rows="5"
                        class="form-control"
                        placeholder="Write your personal notes for this module (only you can see these)..."
                        maxlength="20000"
                    ><?php echo e(old('body', $currentNote->body ?? '')); ?></textarea>
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-save me-1"></i>Save notes
                </button>
                <?php if(!empty($currentNote)): ?>
                <small class="text-muted ms-2" id="learn-notes-status">
                    Last updated <?php echo e($currentNote->updated_at?->diffForHumans()); ?>

                </small>
                <?php else: ?>
                <small class="text-muted ms-2" id="learn-notes-status"></small>
                <?php endif; ?>
            </form>
            <p class="text-muted small mb-0 mt-2">
                Notes are private and saved per unit; clear the text and save to remove them.
            </p>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!empty($showAttendanceRegister) && $current): ?>
    <div class="learn-attendance mt-4 mb-4">
        <?php if(!empty($attendanceSubmitted)): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Your attendance has been recorded.</div>
        <?php else: ?>
        <h3 class="h5 mb-3">Attendance register</h3>
        <p class="text-muted small mb-3">Please mark your attendance for this session.</p>
        <form action="<?php echo e(route('learn.attendance.store', $course)); ?>" method="post" class="learn-attendance-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="unit_id" value="<?php echo e($current->id); ?>">
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-6">
                    <label for="att_title" class="form-label">Title</label>
                    <input type="text" class="form-control form-control-sm" id="att_title" name="title" value="<?php echo e(old('title')); ?>" placeholder="e.g. Mr, Ms, Dr">
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="att_name" name="name" value="<?php echo e(old('name', auth()->user()->name ?? '')); ?>" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_surname" class="form-label">Surname <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm <?php $__errorArgs = ['surname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="att_surname" name="surname" value="<?php echo e(old('surname', auth()->user()->surname ?? '')); ?>" required>
                    <?php $__errorArgs = ['surname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_designation" class="form-label">Designation</label>
                    <input type="text" class="form-control form-control-sm" id="att_designation" name="designation" value="<?php echo e(old('designation')); ?>" placeholder="e.g. Manager, Director">
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_organisation" class="form-label">Organisation</label>
                    <input type="text" class="form-control form-control-sm" id="att_organisation" name="organisation" value="<?php echo e(old('organisation')); ?>">
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_contact" class="form-label">Contact number</label>
                    <input type="text" class="form-control form-control-sm" id="att_contact" name="contact_number" value="<?php echo e(old('contact_number')); ?>">
                </div>
                <div class="col-12 col-md-6">
                    <label for="att_email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-sm <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="att_email" name="email" value="<?php echo e(old('email', auth()->user()->email ?? '')); ?>" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm mt-3"><i class="bi bi-person-check me-1"></i> Mark attendance</button>
        </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if(!empty($courseFinished)): ?>
    <div class="alert alert-success mb-4">
        <h2 class="h5 mb-2"><i class="bi bi-trophy me-2"></i>You've completed the course!</h2>
        <p class="mb-0">Congratulations on finishing all lessons. You can revisit any lesson from the sidebar or return to your courses.</p>
        <?php if(!empty($certificate)): ?>
        <a href="<?php echo e(route('certificates.show', $certificate)); ?>" class="btn btn-success mt-3 me-2"><i class="bi bi-award me-1"></i> View certificate</a>
        <?php endif; ?>
        <a href="<?php echo e(route('courses.show', $course)); ?>" class="btn <?php echo e($certificate ?? null ? 'btn-outline-success' : 'btn-success'); ?> mt-3 me-2">Back to course</a>
        <a href="<?php echo e(route('courses.my')); ?>" class="btn btn-outline-success mt-3">My courses</a>
    </div>
    <?php endif; ?>

    <?php if($current->video_url): ?>
    <div class="learn-media-wrap">
        <?php
            $v = $current->video_url;
            if (preg_match('/youtube\.com\/watch\?v=([\w-]+)/', $v, $m)) { $embed = 'https://www.youtube.com/embed/' . $m[1]; }
            elseif (preg_match('/youtu\.be\/([\w-]+)/', $v, $m)) { $embed = 'https://www.youtube.com/embed/' . $m[1]; }
            elseif (preg_match('/vimeo\.com\/(\d+)/', $v, $m)) { $embed = 'https://player.vimeo.com/video/' . $m[1]; }
            else { $embed = null; }
        ?>
        <?php if($embed): ?>
        <iframe src="<?php echo e($embed); ?>" height="400" allowfullscreen></iframe>
        <?php else: ?>
        <video controls src="<?php echo e($current->video_url); ?>" class="w-100"></video>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if($current->audio_url): ?>
    <div class="learn-media-wrap p-3">
        <audio controls src="<?php echo e($current->audio_url); ?>" class="w-100"></audio>
    </div>
    <?php endif; ?>
    <?php $currentDone = isset($progress[$current->id]) && $progress[$current->id]->completed_at; ?>
    <?php if(!$currentDone): ?>
    <div id="learn-mark-finished-wrap" class="mt-4" <?php if(!empty($steps) && count($steps) > 0): ?> style="display:none" <?php endif; ?>>
        <form action="<?php echo e(route('learn.unit.complete', [$course, $current])); ?>" method="post">
            <?php echo csrf_field(); ?>
            <button type="submit" class="learn-btn-complete"><i class="bi bi-check-lg me-1"></i> Mark as finished</button>
        </form>
    </div>
    <?php elseif(empty($courseFinished)): ?>
    <p class="text-muted mt-4 mb-0"><i class="bi bi-check-circle me-1"></i> You've marked this as finished. Proceed to the next lesson below or via the sidebar.</p>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('bottom-bar'); ?>
<?php if($current): ?>
<div class="learn-bottom-bar">
    <?php if($prevUnit): ?>
    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $prev])); ?>" class="learn-bar-prev"><i class="bi bi-chevron-left me-1"></i> Previous</a>
    <?php else: ?>
    <span></span>
    <?php endif; ?>
    <?php if($nextUnit): ?>
    <?php if($nextLocked ?? false): ?>
    <span class="text-muted" title="Pass the current module's Knowledge Check to unlock">Next: <?php echo e($nextUnit->title); ?> <i class="bi bi-lock"></i></span>
    <?php else: ?>
    <a href="<?php echo e(route('learn.show', ['course' => $course, 'unit' => $next])); ?>" class="learn-bar-next">
        <?php echo e($current->unit_type === 'quiz' ? 'Next' : 'Lesson ' . ($currentIndex + 1)); ?> – <?php echo e($nextUnit->title); ?> <i class="bi bi-chevron-right"></i>
    </a>
    <?php endif; ?>
    <?php else: ?>
    <span></span>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('chat'); ?>
<?php echo $__env->make('courses._facilitator_chat_panel', [
    'course' => $course,
    'unitId' => $current->id ?? null,
    'isFacilitator' => $isFacilitator ?? false,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const form = document.getElementById('learn-notes-form');
    if (!form) return;

    const textarea = form.querySelector('textarea[name="body"]');
    const statusEl = document.getElementById('learn-notes-status');
    if (!textarea) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let timeoutId = null;
    let lastSentValue = textarea.value;

    function setStatus(text) {
        if (statusEl) {
            statusEl.textContent = text || '';
        }
    }

    async function autosave() {
        const body = textarea.value;
        if (body === lastSentValue) {
            return;
        }
        lastSentValue = body;
        setStatus('Saving…');
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                },
                body: new URLSearchParams({ body }),
            });
            if (!res.ok) {
                throw new Error('Failed');
            }
            const data = await res.json().catch(() => ({}));
            if (data.saved_at) {
                const d = new Date(data.saved_at);
                setStatus('Saved at ' + d.toLocaleTimeString());
            } else if (body.trim() === '') {
                setStatus('Notes cleared');
            } else {
                setStatus('Saved');
            }
        } catch (e) {
            setStatus('Could not autosave (check connection).');
        }
    }

    textarea.addEventListener('input', function () {
        setStatus('Saving…');
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        timeoutId = setTimeout(autosave, 1500);
    });

    textarea.addEventListener('blur', function () {
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        autosave();
    });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.learn', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Training_2\lms\resources\views/courses/learn.blade.php ENDPATH**/ ?>