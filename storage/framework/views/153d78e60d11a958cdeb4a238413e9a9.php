<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800"><?php echo e($section->name); ?></h1>
            <p class="text-muted mb-0">
                <?php echo e($section->subject->code); ?> - <?php echo e($section->subject->name); ?> | 
                <span class="badge badge-info"><?php echo e($section->year_level); ?></span>
                <span class="badge badge-primary"><?php echo e($section->academic_year); ?></span>
                <span class="badge badge-success"><?php echo e($section->semester); ?></span>
            </p>
        </div>
        <div>
            <a href="<?php echo e(route('sections.export.excel', $section)); ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
            <a href="<?php echo e(route('sections.enroll.form', $section)); ?>" class="btn btn-info">
                <i class="fas fa-file-csv"></i> Enroll Students (CSV)
            </a>
            <a href="<?php echo e(route('grades.professor.input', $section)); ?>" class="btn btn-success">
                <i class="fas fa-edit"></i> Input Grades
            </a>
            <?php if(auth()->user()->hasRole('admin')): ?>
                <a href="<?php echo e(route('grades.admin.input', $section)); ?>" class="btn btn-warning">
                    <i class="fas fa-lock"></i> Admin Grades
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('sections.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?>

            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if($students->isEmpty()): ?>
        <div class="alert alert-info">
            No students enrolled in this section yet.
            <a href="<?php echo e(route('sections.enroll.form', $section)); ?>" class="alert-link">Click here to enroll students via CSV.</a>
        </div>
    <?php else: ?>
        <!-- Spreadsheet-style Grade Input -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-secondary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-table"></i> Students Overview (<?php echo e($students->count()); ?> students)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 25%">Student Name</th>
                                <th style="width: 15%" class="text-center">Prelim</th>
                                <th style="width: 15%" class="text-center">Midterm</th>
                                <th style="width: 15%" class="text-center">Finals</th>
                                <th style="width: 15%" class="text-center">Final Grade</th>
                                <th style="width: 10%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($index + 1); ?></td>
                                    <td>
                                        <strong><?php echo e($student->name); ?></strong><br>
                                        <small class="text-muted"><?php echo e($student->email); ?></small>
                                    </td>
                                    
                                    <!-- Prelim Grade -->
                                    <td class="text-center">
                                        <?php if(isset($student->prelim_grade) && $student->prelim_grade > 0): ?>
                                            <span class="badge badge-success" style="font-size: 1em;">
                                                <?php echo e(number_format($student->prelim_grade, 2)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Midterm Grade -->
                                    <td class="text-center">
                                        <?php if(isset($student->midterm_grade) && $student->midterm_grade > 0): ?>
                                            <span class="badge badge-info" style="font-size: 1em;">
                                                <?php echo e(number_format($student->midterm_grade, 2)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Finals Grade -->
                                    <td class="text-center">
                                        <?php if(isset($student->finals_grade) && $student->finals_grade > 0): ?>
                                            <span class="badge badge-danger" style="font-size: 1em;">
                                                <?php echo e(number_format($student->finals_grade, 2)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Final Grade -->
                                    <td class="text-center">
                                        <?php if($student->final_grade > 0): ?>
                                            <h5 class="mb-0 
                                                <?php if($student->final_grade >= 90): ?> text-success
                                                <?php elseif($student->final_grade >= 80): ?> text-primary
                                                <?php elseif($student->final_grade >= 70): ?> text-info
                                                <?php elseif($student->final_grade >= 60): ?> text-warning
                                                <?php else: ?> text-danger
                                                <?php endif; ?>">
                                                <?php echo e(number_format($student->final_grade, 2)); ?>

                                            </h5>
                                            <small class="text-muted">
                                                <?php if($student->final_grade >= 90): ?> Excellent
                                                <?php elseif($student->final_grade >= 80): ?> Very Good
                                                <?php elseif($student->final_grade >= 70): ?> Good
                                                <?php elseif($student->final_grade >= 60): ?> Fair
                                                <?php else: ?> Needs Improvement
                                                <?php endif; ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Actions -->
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info" 
                                                data-toggle="modal" 
                                                data-target="#studentDetailModal<?php echo e($student->id); ?>"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Statistics Card -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="font-weight-bold mb-3">Section Statistics</h6>
                                <div class="row">
                                    <?php
                                        $studentsWithGrades = $students->filter(fn($s) => $s->final_grade > 0);
                                        $avgFinal = $studentsWithGrades->avg('final_grade');
                                        $avgPrelim = $students->filter(fn($s) => isset($s->prelim_grade) && $s->prelim_grade > 0)->avg('prelim_grade');
                                        $avgMidterm = $students->filter(fn($s) => isset($s->midterm_grade) && $s->midterm_grade > 0)->avg('midterm_grade');
                                        $avgFinals = $students->filter(fn($s) => isset($s->finals_grade) && $s->finals_grade > 0)->avg('finals_grade');
                                    ?>
                                    
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <small class="text-muted">Class Average (Final)</small>
                                            <h4 class="mb-0 text-primary">
                                                <?php echo e($avgFinal ? number_format($avgFinal, 2) : '-'); ?>

                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <small class="text-muted">Prelim Average</small>
                                            <h4 class="mb-0 text-success">
                                                <?php echo e($avgPrelim ? number_format($avgPrelim, 2) : '-'); ?>

                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <small class="text-muted">Midterm Average</small>
                                            <h4 class="mb-0 text-info">
                                                <?php echo e($avgMidterm ? number_format($avgMidterm, 2) : '-'); ?>

                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <small class="text-muted">Finals Average</small>
                                            <h4 class="mb-0 text-danger">
                                                <?php echo e($avgFinals ? number_format($avgFinals, 2) : '-'); ?>

                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Detail Modals -->
        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="modal fade" id="studentDetailModal<?php echo e($student->id); ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo e($student->name); ?> - Grade Breakdown</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <?php $__currentLoopData = ['Prelim', 'Midterm', 'Finals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-4">
                                        <h6 class="text-primary border-bottom pb-2">
                                            <i class="fas fa-clipboard-list"></i> <?php echo e($term); ?>

                                        </h6>
                                        <?php
                                            $termGrades = $student->studentGrades->filter(fn($g) => $g->gradeCriteria && $g->gradeCriteria->term === $term);
                                        ?>
                                        <?php if($termGrades->isNotEmpty()): ?>
                                            <ul class="list-unstyled">
                                                <?php $__currentLoopData = $termGrades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li class="mb-2">
                                                        <strong><?php echo e($grade->gradeCriteria->name); ?></strong> 
                                                        <small class="text-muted">(<?php echo e($grade->gradeCriteria->weight); ?>%)</small><br>
                                                        <span class="badge badge-info"><?php echo e(number_format($grade->score, 2)); ?></span>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                            <hr>
                                            <strong><?php echo e($term); ?> Grade: 
                                                <?php
                                                    $termGradeProp = strtolower($term) . '_grade';
                                                ?>
                                                <?php echo e(isset($student->$termGradeProp) ? number_format($student->$termGradeProp, 2) : '-'); ?>

                                            </strong>
                                        <?php else: ?>
                                            <p class="text-muted">No grades yet</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <!-- Final Grade Display -->
                            <div class="alert alert-primary mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Final Grade</h6>
                                        <small class="text-muted">Average of Prelim, Midterm, Finals</small>
                                    </div>
                                    <h3 class="mb-0"><?php echo e(number_format($student->final_grade, 2)); ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.professor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/sections/show.blade.php ENDPATH**/ ?>