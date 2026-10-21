<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800">Grade Input: <?php echo e($section->name); ?></h1>
            <p class="text-muted mb-0">
                <?php echo e($section->subject->code); ?> - <?php echo e($section->subject->name); ?> | 
                <span class="badge badge-info"><?php echo e($section->academic_year); ?></span>
                <span class="badge badge-success"><?php echo e($section->semester); ?></span>
            </p>
        </div>
        <a href="<?php echo e(route('sections.show', $section)); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Section
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> <?php echo e(session('error')); ?>

            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Quick Guide:</strong> You can input grades for Lecture and Laboratory components. 
        <span class="badge badge-warning">Admin-Only</span> fields (like Term Exams) are displayed but cannot be edited by professors.
        Click on any editable cell to input grades. Changes are saved automatically.
        <br>
        <strong class="text-success">NEW:</strong> You can now add custom criteria (extra quizzes, assignments, etc.) using the 
        <button class="btn btn-xs btn-success" disabled><i class="fas fa-plus-circle"></i> Add Criteria</button> button below each term.
    </div>

    <!-- Add Custom Criteria Button (at top for easy access) -->
    <div class="card shadow mb-3 border-secondary">
        <div class="card-body py-2">
           <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#addCriteriaModal">
                <i class="fas fa-plus-circle"></i> Add Custom Grade Criteria
            </button>
            <small class="text-muted ml-2">Add extra quizzes, assignments, or lab activities during the semester</small>
        </div>
    </div>

    <?php if($students->isEmpty()): ?>
        <div class="alert alert-warning">
            No students enrolled in this section yet.
            <a href="<?php echo e(route('sections.enroll.form', $section)); ?>" class="alert-link">Click here to enroll students via CSV.</a>
        </div>
    <?php else: ?>
        <!-- Term Tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#prelim">Prelim</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#midterm">Midterm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#finals">Finals</a>
            </li>
        </ul>

        <div class="tab-content">
            <?php $__currentLoopData = ['Prelim', 'Midterm', 'Finals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="tab-pane fade <?php echo e($loop->first ? 'show active' : ''); ?>" id="<?php echo e(strtolower($term)); ?>">
                    <!-- Spreadsheet-style Grade Input -->
                    <div class="card shadow mb-4 mt-3">
                        <div class="card-header py-3 bg-secondary text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-table"></i> <?php echo e($term); ?> Term - Grade Input (<?php echo e($students->count()); ?> students)
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div style="overflow-x: auto;">
                                <table class="table table-bordered table-hover mb-0 grade-table">
                                    <thead class="thead-light" style="position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th style="min-width: 50px; position: sticky; left: 0; background: #f8f9fc; z-index: 11;" class="text-center">#</th>
                                            <th style="min-width: 200px; position: sticky; left: 50px; background: #f8f9fc; z-index: 11;">Student Name</th>
                                            
                                            <?php
                                                $lectureCriteria = $professorCriteria->get($term, collect())->get('lecture', collect());
                                                $labCriteria = $professorCriteria->get($term, collect())->get('laboratory', collect());
                                                $adminLectureCriteria = $adminOnlyCriteria->get($term, collect())->get('lecture', collect());
                                                $adminLabCriteria = $adminOnlyCriteria->get($term, collect())->get('laboratory', collect());
                                            ?>
                                            
                                            <!-- Lecture Columns -->
                                            <?php if($lectureCriteria->isNotEmpty() || $adminLectureCriteria->isNotEmpty()): ?>
                                                <th colspan="<?php echo e($lectureCriteria->count() + $adminLectureCriteria->count()); ?>" 
                                                    class="text-center bg-success text-white">
                                                    <i class="fas fa-chalkboard-teacher"></i> Lecture (70%)
                                                </th>
                                            <?php endif; ?>
                                            
                                            <!-- Lab Columns -->
                                            <?php if($section->subject->has_lab && ($labCriteria->isNotEmpty() || $adminLabCriteria->isNotEmpty())): ?>
                                                <th colspan="<?php echo e($labCriteria->count() + $adminLabCriteria->count()); ?>" 
                                                    class="text-center bg-info text-white">
                                                    <i class="fas fa-flask"></i> Laboratory (30%)
                                                </th>
                                            <?php endif; ?>
                                            
                                            <th style="min-width: 120px; background: #fff3cd;" class="text-center">
                                                <strong>Term Grade</strong>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="position: sticky; left: 0; background: #f8f9fc; z-index: 11;"></th>
                                            <th style="position: sticky; left: 50px; background: #f8f9fc; z-index: 11;"></th>
                                            
                                            <!-- Lecture Criteria Headers -->
                                            <?php $__currentLoopData = $lectureCriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <th class="text-center" style="min-width: 100px; font-size: 0.85rem; background: #d4edda;">
                                                    <?php echo e($criteria->name); ?><br>
                                                    <span class="badge badge-secondary editable-max" 
                                                          style="cursor: pointer; font-size: 0.7rem; margin: 2px 0;"
                                                          data-criteria-id="<?php echo e($criteria->id); ?>"
                                                          data-current-max="<?php echo e($criteria->max_score ?? 100); ?>"
                                                          title="Click to edit">
                                                        <?php echo e($criteria->max_score ?? 100); ?> items
                                                    </span><br>
                                                    <small class="text-muted">(<?php echo e($criteria->weight); ?>%)</small>
                                                </th>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            
                                            <!-- Admin Lecture Criteria (Read-Only) -->
                                            <?php $__currentLoopData = $adminLectureCriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <th class="text-center" style="min-width: 100px; font-size: 0.85rem; background: #fff3cd;">
                                                    <i class="fas fa-lock"></i> <?php echo e($criteria->name); ?><br>
                                                    <span class="badge badge-warning" style="font-size: 0.7rem; margin: 2px 0;">
                                                        <?php echo e($criteria->max_score ?? 100); ?> items
                                                    </span><br>
                                                    <small class="text-muted">(<?php echo e($criteria->weight); ?>%) - Admin Only</small>
                                                </th>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            
                                            <!-- Lab Criteria Headers -->
                                            <?php if($section->subject->has_lab): ?>
                                                <?php $__currentLoopData = $labCriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <th class="text-center" style="min-width: 100px; font-size: 0.85rem; background: #d1ecf1;">
                                                        <?php echo e($criteria->name); ?><br>
                                                        <span class="badge badge-secondary editable-max" 
                                                              style="cursor: pointer; font-size: 0.7rem; margin: 2px 0;"
                                                              data-criteria-id="<?php echo e($criteria->id); ?>"
                                                              data-current-max="<?php echo e($criteria->max_score ?? 100); ?>"
                                                              title="Click to edit">
                                                            <?php echo e($criteria->max_score ?? 100); ?> items
                                                        </span><br>
                                                        <small class="text-muted">(<?php echo e($criteria->weight); ?>%)</small>
                                                    </th>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                
                                                <!-- Admin Lab Criteria (Read-Only) -->
                                                <?php $__currentLoopData = $adminLabCriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <th class="text-center" style="min-width: 100px; font-size: 0.85rem; background: #fff3cd;">
                                                        <i class="fas fa-lock"></i> <?php echo e($criteria->name); ?><br>
                                                        <span class="badge badge-warning" style="font-size: 0.7rem; margin: 2px 0;">
                                                            <?php echo e($criteria->max_score ?? 100); ?> items
                                                        </span><br>
                                                        <small class="text-muted">(<?php echo e($criteria->weight); ?>%) - Admin Only</small>
                                                    </th>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                            
                                            <th style="background: #fff3cd;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr data-student-id="<?php echo e($student->id); ?>" data-term="<?php echo e($term); ?>">
                                                <td class="text-center font-weight-bold" style="position: sticky; left: 0; background: white;">
                                                    <?php echo e($index + 1); ?>

                                                </td>
                                                <td style="position: sticky; left: 50px; background: white;">
                                                    <strong><?php echo e($student->formatted_name); ?></strong><br>
                                                    <small class="text-muted"><?php echo e($student->email); ?></small>
                                                </td>
                                                
                                                <!-- Editable Lecture Grades -->
                                                <?php $__currentLoopData = $lectureCriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $existingGrade = $existingGrades->get($student->id)?->firstWhere('grade_criteria_id', $criteria->id);
                                                    ?>
                                                    <td class="text-center p-1 grade-cell editable" 
                                                        data-student-id="<?php echo e($student->id); ?>"
                                                        data-criteria-id="<?php echo e($criteria->id); ?>"
                                                        style="cursor: pointer; background: #f8fff9;">
                                                        <input type="number" 
                                                               class="form-control form-control-sm text-center grade-input" 
                                                               value="<?php echo e($existingGrade ? number_format($existingGrade->score, 2, '.', '') : ''); ?>"
                                                               min="0" 
                                                               max="<?php echo e($criteria->max_score ?? 100); ?>" 
                                                               step="0.01"
                                                               data-max="<?php echo e($criteria->max_score ?? 100); ?>"
                                                               placeholder="--">
                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                
                                                <!-- Read-Only Admin Lecture Grades -->
                                                <?php $__currentLoopData = $adminLectureCriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $existingGrade = $existingGrades->get($student->id)?->firstWhere('grade_criteria_id', $criteria->id);
                                                    ?>
                                                    <td class="text-center p-2" style="background: #fff9e6;">
                                                        <span class="badge badge-warning">
                                                            <?php echo e($existingGrade ? number_format($existingGrade->score, 2) : '--'); ?>

                                                        </span>
                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                
                                                <!-- Editable Lab Grades -->
                                                <?php if($section->subject->has_lab): ?>
                                                    <?php $__currentLoopData = $labCriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $existingGrade = $existingGrades->get($student->id)?->firstWhere('grade_criteria_id', $criteria->id);
                                                        ?>
                                                        <td class="text-center p-1 grade-cell editable" 
                                                            data-student-id="<?php echo e($student->id); ?>"
                                                            data-criteria-id="<?php echo e($criteria->id); ?>"
                                                            style="cursor: pointer; background: #f8fcfd;">
                                                            <input type="number" 
                                                                   class="form-control form-control-sm text-center grade-input" 
                                                                   value="<?php echo e($existingGrade ? number_format($existingGrade->score, 2, '.', '') : ''); ?>"
                                                                   min="0" 
                                                                   max="<?php echo e($criteria->max_score ?? 100); ?>" 
                                                                   step="0.01"
                                                                   data-max="<?php echo e($criteria->max_score ?? 100); ?>"
                                                                   placeholder="--">
                                                        </td>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    
                                                    <!-- Read-Only Admin Lab Grades -->
                                                    <?php $__currentLoopData = $adminLabCriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $existingGrade = $existingGrades->get($student->id)?->firstWhere('grade_criteria_id', $criteria->id);
                                                        ?>
                                                        <td class="text-center p-2" style="background: #fff9e6;">
                                                            <span class="badge badge-warning">
                                                                <?php echo e($existingGrade ? number_format($existingGrade->score, 2) : '--'); ?>

                                                            </span>
                                                        </td>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                                
                                                <!-- Term Grade (calculated server-side) -->
                                                <td class="text-center font-weight-bold term-grade-cell" style="background: #fff3cd;">
                                                    <span class="term-grade-value">
                                                        <?php echo e(number_format($student->{strtolower($term) . '_grade'} ?? 0, 2)); ?>

                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Legend & Instructions -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-palette"></i> Color Legend
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge" style="background: #d4edda; color: #155724;">Editable - Lecture</span>
                            <small class="text-muted">You can edit these grades</small>
                        </div>
                        <div class="mb-2">
                            <span class="badge" style="background: #d1ecf1; color: #0c5460;">Editable - Laboratory</span>
                            <small class="text-muted">You can edit these grades</small>
                        </div>
                        <div class="mb-2">
                            <span class="badge badge-warning">
                                <i class="fas fa-lock"></i> Read-Only - Admin Only
                            </span>
                            <small class="text-muted">Only administrators can edit these (Term Exams)</small>
                        </div>
                        <div>
                            <span class="badge" style="background: #fff3cd; color: #856404;">Term Grade</span>
                            <small class="text-muted">Auto-calculated</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-keyboard"></i> Keyboard Shortcuts
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><kbd>Tab</kbd> - Move to next cell</li>
                            <li><kbd>Shift + Tab</kbd> - Move to previous cell</li>
                            <li><kbd>Enter</kbd> - Save and move down</li>
                            <li><kbd>Esc</kbd> - Cancel editing</li>
                            <li><strong>Auto-save:</strong> Changes save when you leave a cell</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Add Custom Criteria Modal -->
<div class="modal fade" id="addCriteriaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Add Custom Grade Criteria
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addCriteriaForm">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="criteria_term">Term <span class="text-danger">*</span></label>
                        <select class="form-control" id="criteria_term" name="term" required>
                            <option value="">-- Select Term --</option>
                            <option value="Prelim">Prelim</option>
                            <option value="Midterm">Midterm</option>
                            <option value="Finals">Finals</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="criteria_category">Category <span class="text-danger">*</span></label>
                        <select class="form-control" id="criteria_category" name="category" required>
                            <option value="">-- Select Category --</option>
                            <option value="lecture">Lecture</option>
                            <?php if($section->subject->has_lab): ?>
                                <option value="laboratory">Laboratory</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="criteria_name">Component Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="criteria_name" name="name" 
                               placeholder="e.g., Quiz 4, Assignment 3, Lab Activity 5" required>
                        <small class="text-muted">Type the full name directly (e.g., Quiz 4, Assignment 3)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="criteria_max_score">Max Score/Items <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="criteria_max_score" name="max_score" 
                               placeholder="e.g., 15" min="1" step="0.01" required>
                        <small class="text-muted">Total items/points for this component (e.g., 15 for a 15-item quiz)</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-magic"></i>
                        <strong>Auto Weight Distribution:</strong> The system will automatically redistribute weights 
                        among similar components. For example, if you have 3 quizzes taking up 25% total and add Quiz 4, 
                        all 4 quizzes will automatically become 6.25% each (still 25% total).
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitAddCriteria()">
                    <i class="fas fa-plus"></i> Add Criteria
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Max Items Modal -->
<div class="modal fade" id="editMaxModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Max Items</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editMaxForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="edit_criteria_id">
                    <div class="form-group">
                        <label>Maximum Items <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_max_score" min="1" step="0.01" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.editable-max:hover { background: #495057 !important; transform: scale(1.05); }
.grade-input:focus {
    outline: 2px solid #007bff;
    background: white !important;
}

.grade-cell.editable:hover {
    background: #e3f2fd !important;
}

.saving {
    background: #fff3cd !important;
}

.saved {
    background: #d4edda !important;
    transition: background 0.3s;
}

.error {
    background: #f8d7da !important;
}

.grade-table {
    font-size: 0.9rem;
}

.grade-table th, .grade-table td {
    border: 1px solid #dee2e6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeInputs = document.querySelectorAll('.grade-input');
    
    // DON'T calculate on page load - use server-calculated values instead
    // Only recalculate when a grade is actually changed
    
    gradeInputs.forEach(input => {
        let originalValue = input.value;
        
        input.addEventListener('blur', function() {
            if (this.value !== originalValue) {
                saveGrade(this);
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (this.value !== originalValue) {
                    saveGrade(this);
                }
                moveToNextRow(this);
            } else if (e.key === 'Escape') {
                this.value = originalValue;
                this.blur();
            }
        });
        
        input.addEventListener('saved', function() {
            originalValue = this.value;
        });
        
        // Real-time validation on input
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (this.value !== '' && !isNaN(value)) {
                if (value < 0 || value > 100) {
                    this.style.borderColor = '#dc3545';
                    this.style.borderWidth = '2px';
                } else {
                    this.style.borderColor = '';
                    this.style.borderWidth = '';
                }
            } else {
                this.style.borderColor = '';
                this.style.borderWidth = '';
            }
        });
    });
    
    function saveGrade(input) {
        const cell = input.closest('.grade-cell');
        const studentId = cell.dataset.studentId;
        const criteriaId = cell.dataset.criteriaId;
        const score = input.value;
        
        // Validation with popup alerts
        if (score === '') {
            return;
        }
        
        if (score < 0) {
            alert('⚠️ Invalid Grade!\n\nGrade cannot be less than 0.\nPlease enter a value between 0 and 100.');
            input.value = '';
            input.focus();
            return;
        }
        
        if (score > 100) {
            alert('⚠️ Invalid Grade!\n\nGrade cannot be greater than 100.\nPlease enter a value between 0 and 100.');
            input.value = '';
            input.focus();
            return;
        }
        
        cell.classList.add('saving');
        
        fetch('<?php echo e(route("grades.professor.store", $section)); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                student_id: studentId,
                grade_criteria_id: criteriaId,
                score: score
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            cell.classList.remove('saving');
            cell.classList.add('saved');
            setTimeout(() => cell.classList.remove('saved'), 1000);
            
            // Format display to 2 decimals
            const numericScore = parseFloat(score);
            if (!isNaN(numericScore)) {
                input.value = numericScore.toFixed(2);
            }
            
            input.dispatchEvent(new Event('saved'));
            
            // Update term grade for the student's row in the current tab
            if (data.final_grade !== undefined) {
                const row = input.closest('tr');
                const term = row.dataset.term;
                updateTermGradeForStudentInTerm(studentId, term, data.final_grade);
            }
        })
        .catch(error => {
            cell.classList.remove('saving');
            cell.classList.add('error');
            setTimeout(() => cell.classList.remove('error'), 2000);
            console.error('Error saving grade:', error);
            alert('Failed to save grade. Please try again.');
        });
    }
    
    function moveToNextRow(currentInput) {
        const currentRow = currentInput.closest('tr');
        const currentCell = currentInput.closest('td');
        const cellIndex = Array.from(currentRow.children).indexOf(currentCell);
        
        const nextRow = currentRow.nextElementSibling;
        if (nextRow) {
            const nextCell = nextRow.children[cellIndex];
            if (nextCell) {
                const nextInput = nextCell.querySelector('.grade-input');
                if (nextInput) {
                    nextInput.focus();
                    nextInput.select();
                }
            }
        }
    }
    
    // Update term grade for a specific student in a specific term tab
    function updateTermGradeForStudentInTerm(studentId, term, termGrade) {
        const termTabId = term.toLowerCase();
        const termTab = document.getElementById(termTabId);
        
        if (!termTab) return;
        
        const row = termTab.querySelector(`tr[data-student-id="${studentId}"][data-term="${term}"]`);
        if (!row) return;
        
        const termGradeCell = row.querySelector('.term-grade-cell');
        const termGradeValue = termGradeCell ? termGradeCell.querySelector('.term-grade-value') : null;
        
        if (termGradeValue) {
            termGradeValue.textContent = termGrade > 0 ? termGrade.toFixed(2) : '--';
            
            termGradeCell.style.animation = 'pulse 0.5s';
            setTimeout(() => {
                termGradeCell.style.animation = '';
            }, 500);
        }
    }
    
    // Calculate and display term grades for all students in all terms on page load
    function calculateAllTermGrades() {
        ['Prelim', 'Midterm', 'Finals'].forEach(term => {
            const termTabId = term.toLowerCase();
            const termTab = document.getElementById(termTabId);
            
            if (!termTab) return;
            
            const rows = termTab.querySelectorAll('tr[data-student-id][data-term]');
            
            rows.forEach(row => {
                const termGrade = calculateTermGradeFromRow(row);
                
                const termGradeCell = row.querySelector('.term-grade-cell');
                const termGradeValue = termGradeCell ? termGradeCell.querySelector('.term-grade-value') : null;
                
                if (termGradeValue) {
                    termGradeValue.textContent = termGrade > 0 ? termGrade.toFixed(2) : '--';
                }
            });
        });
    }
    
    // FIXED: Calculate term grade using WEIGHTED AVERAGES from the current row's input values
    function calculateTermGradeFromRow(row) {
        const gradeInputs = row.querySelectorAll('.grade-input');
        
        if (gradeInputs.length === 0) return 0;
        
        let lectureData = [];
        let labData = [];
        
        // Get the table headers to extract weights
        const table = row.closest('table');
        const headerRow = table.querySelector('thead tr:last-child');
        const headerCells = headerRow.querySelectorAll('th');
        
        // Build maps of column index to weight and max_score
        const columnWeights = new Map();
        const columnMaxScores = new Map();
        headerCells.forEach((header, index) => {
            const weightMatch = header.textContent.match(/\((\d+\.?\d*)\%\)/);
            if (weightMatch) {
                columnWeights.set(index, parseFloat(weightMatch[1]));
            }
            
            const maxScoreMatch = header.textContent.match(/(\d+\.?\d*)\s*items/);
            if (maxScoreMatch) {
                columnMaxScores.set(index, parseFloat(maxScoreMatch[1]));
            }
        });
        
        // Get all grades from inputs with their weights and max scores
        gradeInputs.forEach(input => {
            const value = parseFloat(input.value);
            if (!isNaN(value) && value >= 0) {
                const cell = input.closest('td');
                const cellIndex = Array.from(row.children).indexOf(cell);
                const weight = columnWeights.get(cellIndex) || 0;
                const maxScore = columnMaxScores.get(cellIndex) || parseFloat(input.dataset.max) || 100;
                
                const cellBg = cell.style.background;
                const isLab = cellBg.includes('f8fcfd') || cellBg.includes('fff0f0') || cellBg.includes('ffd4d4');
                
                if (isLab) {
                    labData.push({ score: value, weight: weight, maxScore: maxScore });
                } else {
                    lectureData.push({ score: value, weight: weight, maxScore: maxScore });
                }
            }
        });
        
        // Also check for read-only admin grades (displayed as badges)
        const readOnlyBadges = row.querySelectorAll('.badge-warning');
        readOnlyBadges.forEach(badge => {
            const scoreText = badge.textContent.trim();
            if (scoreText !== '--') {
                const value = parseFloat(scoreText);
                if (!isNaN(value) && value >= 0) {
                    const cell = badge.closest('td');
                    const cellIndex = Array.from(row.children).indexOf(cell);
                    const weight = columnWeights.get(cellIndex) || 0;
                    const maxScore = columnMaxScores.get(cellIndex) || 100;
                    
                    const cellBg = cell.style.background;
                    const isLab = cellBg.includes('fff0f0') || cellBg.includes('ffd4d4');
                    
                    if (isLab) {
                        labData.push({ score: value, weight: weight, maxScore: maxScore });
                    } else {
                        lectureData.push({ score: value, weight: weight, maxScore: maxScore });
                    }
                }
            }
        });
        
        // Calculate weighted averages
        function calculateWeightedAverage(data) {
            if (data.length === 0) return 0;
            
            let totalWeight = 0;
            let weightedSum = 0;
            
            data.forEach(item => {
                // Convert raw score to percentage first
                const percentage = (item.score / item.maxScore) * 100;
                
                // Then apply weight
                const weightedScore = percentage * (item.weight / 100);
                
                weightedSum += weightedScore;
                totalWeight += item.weight;
            });
            
            return totalWeight > 0 ? weightedSum : 0;
        }
        
        const lectureAvg = calculateWeightedAverage(lectureData);
        const labAvg = calculateWeightedAverage(labData);
        
        const hasLab = labData.length > 0;
        
        if (hasLab) {
            if (lectureAvg > 0 && labAvg > 0) {
                return (lectureAvg * 0.7) + (labAvg * 0.3);
            } else if (lectureAvg > 0) {
                return lectureAvg * 0.7;
            } else if (labAvg > 0) {
                return labAvg * 0.3;
            }
        } else {
            return lectureAvg;
        }
        
        return 0;
    }
});

// CSS animation for pulse effect
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); background: #ffc107; }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);

// Add Custom Criteria Function
function submitAddCriteria() {
    const form = document.getElementById('addCriteriaForm');
    
    // Validate
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Get form values directly to ensure they're captured
    const data = {
        term: document.getElementById('criteria_term').value,
        category: document.getElementById('criteria_category').value,
        name: document.getElementById('criteria_name').value,
        max_score: document.getElementById('criteria_max_score').value,
        _token: '<?php echo e(csrf_token()); ?>'
    };
    
    // Validate required fields
    if (!data.term || !data.category || !data.name || !data.max_score) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Save the term so we can return to it after reload
    localStorage.setItem('activeGradeTerm', data.term.toLowerCase());
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    btn.disabled = true;
    
    fetch('<?php echo e(route("grades.add-criteria", $section)); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(result => {
        // Success
        alert(result.message || 'Criteria added successfully! The page will reload to show the new column.');
        $('#addCriteriaModal').modal('hide');
        form.reset();
        
        // Reload page to show new criteria
        window.location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Failed to add criteria. Please try again.');
        localStorage.removeItem('activeGradeTerm'); // Clear on error
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Restore active tab after page reload
document.addEventListener('DOMContentLoaded', function() {
    const savedTerm = localStorage.getItem('activeGradeTerm');
    if (savedTerm) {
        // Activate the saved tab
        const tabLink = document.querySelector(`a[href="#${savedTerm}"]`);
        if (tabLink) {
            $(tabLink).tab('show');
        }
        // Clear the saved term
        localStorage.removeItem('activeGradeTerm');
    }
    
    // Save current tab when switching manually
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        if (target) {
            const term = target.replace('#', '');
            localStorage.setItem('lastViewedGradeTerm', term);
        }
    });
});

// Edit max items
document.querySelectorAll('.editable-max').forEach(el => {
    el.onclick = function() {
        document.getElementById('edit_criteria_id').value = this.dataset.criteriaId;
        document.getElementById('edit_max_score').value = this.dataset.currentMax;
        $('#editMaxModal').modal('show');
    };
});

document.getElementById('editMaxForm').onsubmit = function(e) {
    e.preventDefault();
    const id = document.getElementById('edit_criteria_id').value;
    const max = document.getElementById('edit_max_score').value;
    
    // Save current active tab before reload
    const activeTab = document.querySelector('.nav-link.active[data-toggle="tab"]');
    if (activeTab) {
        const term = activeTab.getAttribute('href').replace('#', '');
        localStorage.setItem('activeGradeTerm', term);
    }
    
    fetch(`/criteria/${id}/update-max`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({max_score: max})
    })
    .then(r => r.json())
    .then(() => { alert('Updated!'); location.reload(); })
    .catch(e => { 
        alert('Error: ' + e); 
        localStorage.removeItem('activeGradeTerm'); // Clear on error
    });
};
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.professor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/grades/professor-input.blade.php ENDPATH**/ ?>