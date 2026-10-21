<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h2 class="mb-4 text-gray-800">My Grades</h2>

    <?php if($sections->isEmpty()): ?>
        <div class="alert alert-info">
            You are not enrolled in any sections yet. Please enroll in sections to view your grades.
        </div>
    <?php else: ?>
        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <?php echo e($section->name); ?> - <?php echo e($section->subject->code); ?>

                    </h6>
                    <small><?php echo e($section->subject->name); ?> | Professor: <?php echo e($section->professor->name ?? 'Unassigned'); ?></small>
                </div>
                <div class="card-body">
                    <!-- Display grades by term -->
                    <div class="row">
                        <?php $__currentLoopData = ['Prelim', 'Midterm', 'Finals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4">
                                <h6 class="
                                    <?php if($term === 'Prelim'): ?> text-success
                                    <?php elseif($term === 'Midterm'): ?> text-info
                                    <?php else: ?> text-danger
                                    <?php endif; ?> border-bottom pb-2">
                                    <i class="fas fa-clipboard-list"></i> <?php echo e($term); ?>

                                </h6>
                                
                                <?php
                                    $termGrades = $section->studentGrades->filter(function($g) use ($term) {
                                        return $g->gradeCriteria && $g->gradeCriteria->term === $term;
                                    });
                                    
                                    $lectureGrades = $termGrades->filter(fn($g) => $g->gradeCriteria->category === 'lecture');
                                    $labGrades = $termGrades->filter(fn($g) => $g->gradeCriteria->category === 'laboratory');
                                ?>
                                
                                <?php if($termGrades->isEmpty()): ?>
                                    <p class="text-muted small">No grades yet.</p>
                                <?php else: ?>
                                    <!-- Lecture Grades -->
                                    <?php if($lectureGrades->isNotEmpty()): ?>
                                        <div class="mb-2">
                                            <strong class="text-success">Lecture:</strong>
                                            <ul class="list-unstyled ml-3">
                                                <?php $__currentLoopData = $lectureGrades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li class="small">
                                                        <?php echo e($grade->gradeCriteria->name); ?>

                                                        <span class="text-muted">(<?php echo e($grade->gradeCriteria->weight); ?>%)</span>:
                                                        <span class="badge badge-success"><?php echo e(number_format($grade->score, 2)); ?></span>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Lab Grades -->
                                    <?php if($section->subject->has_lab && $labGrades->isNotEmpty()): ?>
                                        <div class="mb-2">
                                            <strong class="text-info">Laboratory:</strong>
                                            <ul class="list-unstyled ml-3">
                                                <?php $__currentLoopData = $labGrades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li class="small">
                                                        <?php echo e($grade->gradeCriteria->name); ?>

                                                        <span class="text-muted">(<?php echo e($grade->gradeCriteria->weight); ?>%)</span>:
                                                        <span class="badge badge-info"><?php echo e(number_format($grade->score, 2)); ?></span>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <hr class="my-2">
                                    
                                    <!-- Term Grade -->
                                    <?php
                                        // Calculate term grade
                                        $lectureAvg = null;
                                        $labAvg = null;
                                        
                                        if ($lectureGrades->isNotEmpty()) {
                                            $totalWeight = 0;
                                            $weightedSum = 0;
                                            foreach($lectureGrades as $grade) {
                                                $criteria = $grade->gradeCriteria;
                                                $weight = $criteria->weight;
                                                
                                                // Convert raw score to percentage first
                                                $percentage = $criteria->calculatePercentage($grade->score);
                                                
                                                // Then apply weight
                                                $weightedScore = $percentage * ($weight / 100);
                                                
                                                $weightedSum += $weightedScore;
                                                $totalWeight += $weight;
                                            }
                                            $lectureAvg = $totalWeight > 0 ? $weightedSum : null;
                                        }
                                        
                                        if ($labGrades->isNotEmpty()) {
                                            $totalWeight = 0;
                                            $weightedSum = 0;
                                            foreach($labGrades as $grade) {
                                                $criteria = $grade->gradeCriteria;
                                                $weight = $criteria->weight;
                                                
                                                // Convert raw score to percentage first
                                                $percentage = $criteria->calculatePercentage($grade->score);
                                                
                                                // Then apply weight
                                                $weightedScore = $percentage * ($weight / 100);
                                                
                                                $weightedSum += $weightedScore;
                                                $totalWeight += $weight;
                                            }
                                            $labAvg = $totalWeight > 0 ? $weightedSum : null;
                                        }
                                        
                                        // Calculate final term grade
                                        $termGrade = 0;
                                        if ($section->subject->has_lab) {
                                            if ($lectureAvg !== null && $labAvg !== null) {
                                                $termGrade = ($lectureAvg * 0.7) + ($labAvg * 0.3);
                                            } elseif ($lectureAvg !== null) {
                                                $termGrade = $lectureAvg * 0.7;
                                            } elseif ($labAvg !== null) {
                                                $termGrade = $labAvg * 0.3;
                                            }
                                        } else {
                                            $termGrade = $lectureAvg ?? 0;
                                        }
                                    ?>
                                    
                                    <p class="mb-0">
                                        <strong><?php echo e($term); ?> Grade:</strong>
                                        <span class="badge badge-primary" style="font-size: 1rem;">
                                            <?php echo e($termGrade > 0 ? number_format($termGrade, 2) : '--'); ?>

                                        </span>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <!-- Final Weighted Grade -->
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-primary mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">
                                            <i class="fas fa-graduation-cap"></i> Final Grade for this Subject
                                        </h5>
                                        <small class="text-muted">
                                            Formula: (Prelim + Midterm + Finals) ÷ 3
                                            <?php if($section->subject->has_lab): ?>
                                                | Each Term = (Lecture × 70%) + (Lab × 30%)
                                            <?php else: ?>
                                                | Each Term = Lecture (100%)
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div class="text-right">
                                        <?php if($section->final_grade > 0): ?>
                                            <h2 class="mb-0 
                                                <?php if($section->final_grade >= 90): ?> text-success
                                                <?php elseif($section->final_grade >= 80): ?> text-primary
                                                <?php elseif($section->final_grade >= 70): ?> text-info
                                                <?php elseif($section->final_grade >= 60): ?> text-warning
                                                <?php else: ?> text-danger
                                                <?php endif; ?>">
                                                <?php echo e(number_format($section->final_grade, 2)); ?>

                                            </h2>
                                            <small class="text-muted">
                                                <?php if($section->final_grade >= 90): ?> Excellent
                                                <?php elseif($section->final_grade >= 80): ?> Very Good
                                                <?php elseif($section->final_grade >= 70): ?> Good
                                                <?php elseif($section->final_grade >= 60): ?> Fair
                                                <?php else: ?> Needs Improvement
                                                <?php endif; ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">Not enough data yet</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <!-- Overall Average -->
        <?php
            $overallAvg = $sections->where('final_grade', '>', 0)->avg('final_grade');
        ?>
        <?php if($overallAvg): ?>
            <div class="card shadow mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-line"></i> Overall Academic Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted mb-2">Overall Average Across All Subjects</h6>
                            <h1 class="display-3 
                                <?php if($overallAvg >= 90): ?> text-success
                                <?php elseif($overallAvg >= 80): ?> text-primary
                                <?php elseif($overallAvg >= 70): ?> text-info
                                <?php elseif($overallAvg >= 60): ?> text-warning
                                <?php else: ?> text-danger
                                <?php endif; ?> mb-0">
                                <?php echo e(number_format($overallAvg, 2)); ?>

                            </h1>
                            <p class="text-muted">
                                <?php if($overallAvg >= 90): ?> Excellent Performance
                                <?php elseif($overallAvg >= 80): ?> Very Good Performance
                                <?php elseif($overallAvg >= 70): ?> Good Performance
                                <?php elseif($overallAvg >= 60): ?> Fair Performance
                                <?php else: ?> Needs Improvement
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-primary mb-3">Performance Breakdown</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-success pl-3">
                                        <small class="text-muted">Subjects with A (90+)</small>
                                        <h4 class="mb-0 text-success">
                                            <?php echo e($sections->filter(fn($s) => $s->final_grade >= 90)->count()); ?>

                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-primary pl-3">
                                        <small class="text-muted">Subjects with B (80-89)</small>
                                        <h4 class="mb-0 text-primary">
                                            <?php echo e($sections->filter(fn($s) => $s->final_grade >= 80 && $s->final_grade < 90)->count()); ?>

                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-info pl-3">
                                        <small class="text-muted">Subjects with C (70-79)</small>
                                        <h4 class="mb-0 text-info">
                                            <?php echo e($sections->filter(fn($s) => $s->final_grade >= 70 && $s->final_grade < 80)->count()); ?>

                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-warning pl-3">
                                        <small class="text-muted">Total Subjects Enrolled</small>
                                        <h4 class="mb-0 text-dark">
                                            <?php echo e($sections->count()); ?>

                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/grades/student-view-new.blade.php ENDPATH**/ ?>