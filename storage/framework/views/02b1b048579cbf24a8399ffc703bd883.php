

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h2 class="mb-4 text-gray-800">Student Dashboard</h2>

    <div class="row mb-4">
        <div class="col-md-12">
            <a href="<?php echo e(route('grades.student.view')); ?>" class="btn btn-primary btn-block mb-2">
                <i class="fas fa-chart-bar"></i> View My Detailed Grades
            </a>
        </div>
    </div>

    <?php if($sections->isEmpty()): ?>
        <div class="alert alert-info">
            You are not enrolled in any sections yet. Your professor will enroll you in their sections.
        </div>
    <?php else: ?>
        <!-- My Enrolled Sections Overview -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-book"></i> My Enrolled Section and Subjects
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">Section</th>
                                <th style="width: 25%">Subject</th>
                                <th style="width: 20%">Professor</th>
                                <th style="width: 25%" class="text-center">Current Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($section->name); ?></strong><br>
                                        <small class="text-muted"><?php echo e($section->year_level); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo e($section->subject->code); ?></strong><br>
                                        <small class="text-muted"><?php echo e($section->subject->name); ?></small>
                                    </td>
                                    <td>
                                        <?php echo e($section->professor->name ?? 'Unassigned'); ?>

                                    </td>
                                    <td class="text-center">
                                        <?php if($section->final_grade > 0): ?>
                                            <h4 class="mb-0 
                                                <?php if($section->final_grade >= 90): ?> text-success
                                                <?php elseif($section->final_grade >= 80): ?> text-primary
                                                <?php elseif($section->final_grade >= 70): ?> text-info
                                                <?php elseif($section->final_grade >= 60): ?> text-warning
                                                <?php else: ?> text-danger
                                                <?php endif; ?>">
                                                <?php echo e(number_format($section->final_grade, 2)); ?>

                                            </h4>
                                            <small class="text-muted">
                                                <?php if($section->final_grade >= 90): ?> Excellent
                                                <?php elseif($section->final_grade >= 80): ?> Very Good
                                                <?php elseif($section->final_grade >= 70): ?> Good
                                                <?php elseif($section->final_grade >= 60): ?> Fair
                                                <?php else: ?> Needs Improvement
                                                <?php endif; ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">No grades yet</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Overall Performance Card -->
        <?php
            $sectionsWithGrades = $sections->filter(fn($s) => $s->final_grade > 0);
            $overallAvg = $sectionsWithGrades->avg('final_grade');
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
                            <h6 class="text-muted mb-2">Overall Average</h6>
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
                                        <small class="text-muted">Sections with A (90+)</small>
                                        <h4 class="mb-0 text-success">
                                            <?php echo e($sectionsWithGrades->filter(fn($s) => $s->final_grade >= 90)->count()); ?>

                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-primary pl-3">
                                        <small class="text-muted">Sections with B (80-89)</small>
                                        <h4 class="mb-0 text-primary">
                                            <?php echo e($sectionsWithGrades->filter(fn($s) => $s->final_grade >= 80 && $s->final_grade < 90)->count()); ?>

                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-info pl-3">
                                        <small class="text-muted">Sections with C (70-79)</small>
                                        <h4 class="mb-0 text-info">
                                            <?php echo e($sectionsWithGrades->filter(fn($s) => $s->final_grade >= 70 && $s->final_grade < 80)->count()); ?>

                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border-left-warning pl-3">
                                        <small class="text-muted">Total Sections Enrolled</small>
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

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-info-circle"></i> Information
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <i class="fas fa-check-circle text-success"></i> 
                    You are currently enrolled in <strong><?php echo e($sections->count()); ?></strong> section(s).
                </p>
                <p class="mb-2">
                    <i class="fas fa-graduation-cap text-primary"></i> 
                    Your grades are updated by your professors and administrators.
                </p>
                <p class="mb-0">
                    <i class="fas fa-chart-bar text-info"></i> 
                    Click "View My Detailed Grades" to see complete grade breakdowns.
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/dashboards/student.blade.php ENDPATH**/ ?>