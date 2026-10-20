<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Add New Subject</h1>
        <a href="<?php echo e(route('subjects.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form action="<?php echo e(route('subjects.store')); ?>" method="POST" id="subjectForm">
        <?php echo csrf_field(); ?>

        <!-- Basic Subject Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-book"></i> Basic Subject Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Subject Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo e(old('name')); ?>" 
                                   required
                                   placeholder="e.g., Data Structures and Algorithms">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">
                                Subject Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="code" 
                                   name="code" 
                                   value="<?php echo e(old('code')); ?>" 
                                   required
                                   placeholder="e.g., CS201">
                            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="professor_id" class="form-label">Assigned Professor</label>
                            <select class="form-control <?php $__errorArgs = ['professor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="professor_id" 
                                    name="professor_id">
                                <option value="">-- No Professor Assigned Yet --</option>
                                <?php $__currentLoopData = $professors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($professor->id); ?>"><?php echo e($professor->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['professor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="has_lab" class="form-label">
                                Subject Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-control <?php $__errorArgs = ['has_lab'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="has_lab" 
                                    name="has_lab" 
                                    required>
                                <option value="1">📚 Lecture + Laboratory</option>
                                <option value="0">📖 Lecture Only</option>
                            </select>
                            <?php $__errorArgs = ['has_lab'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grading Criteria Configuration -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-calculator"></i> Grading Criteria Configuration
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Configure the grading criteria for each term. Each term (Prelim/Midterm/Finals) will use these criteria.
                    <strong>Weights must sum to 100% for each category.</strong>
                </div>

                <!-- Tabs for each term -->
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

                <div class="tab-content mt-3">
                    <?php $__currentLoopData = ['Prelim', 'Midterm', 'Finals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="tab-pane fade <?php echo e($loop->first ? 'show active' : ''); ?>" id="<?php echo e(strtolower($term)); ?>">
                            <!-- Lecture Criteria -->
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-chalkboard-teacher"></i> Lecture Component 
                                <span class="badge badge-primary"><?php echo e('70%'); ?></span>
                            </h5>
                            <div id="<?php echo e(strtolower($term)); ?>-lecture-criteria">
                                <!-- Criteria will be added here dynamically -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mb-3" 
                                    onclick="addCriteria('<?php echo e(strtolower($term)); ?>', 'lecture')">
                                <i class="fas fa-plus"></i> Add Lecture Criteria
                            </button>
                            <div class="alert alert-light">
                                <strong>Total Lecture Weight:</strong> 
                                <span id="<?php echo e(strtolower($term)); ?>-lecture-total" class="font-weight-bold">0%</span>
                            </div>

                            <hr>

                            <!-- Lab Criteria (only if has_lab) -->
                            <div class="lab-section" style="display: none;">
                                <h5 class="text-info mb-3">
                                    <i class="fas fa-flask"></i> Laboratory Component 
                                    <span class="badge badge-info">30%</span>
                                </h5>
                                <div id="<?php echo e(strtolower($term)); ?>-laboratory-criteria">
                                    <!-- Criteria will be added here dynamically -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-info mb-3" 
                                        onclick="addCriteria('<?php echo e(strtolower($term)); ?>', 'laboratory')">
                                    <i class="fas fa-plus"></i> Add Lab Criteria
                                </button>
                                <div class="alert alert-light">
                                    <strong>Total Lab Weight:</strong> 
                                    <span id="<?php echo e(strtolower($term)); ?>-laboratory-total" class="font-weight-bold">0%</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Load Default Templates Button -->
                <div class="mt-4">
                    <button type="button" class="btn btn-info" onclick="loadDefaultCriteria()">
                        <i class="fas fa-magic"></i> Load Default Criteria
                    </button>
                    <small class="text-muted ml-2">
                        This will populate with standard grading criteria based on subject type
                    </small>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Create Subject
                </button>
                <a href="<?php echo e(route('subjects.index')); ?>" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<script>
let criteriaCounter = 0;

// Show/hide lab section based on subject type
document.getElementById('has_lab').addEventListener('change', function() {
    const hasLab = this.value === '1';
    document.querySelectorAll('.lab-section').forEach(section => {
        section.style.display = hasLab ? 'block' : 'none';
    });
});

// Add criteria row
function addCriteria(term, category) {
    criteriaCounter++;
    const container = document.getElementById(`${term}-${category}-criteria`);
    
    const row = document.createElement('div');
    row.className = 'row mb-2 criteria-row';
    row.id = `criteria-${criteriaCounter}`;
    
    row.innerHTML = `
        <div class="col-md-5">
            <input type="text" 
                   name="criteria[${term}][${category}][${criteriaCounter}][name]" 
                   class="form-control form-control-sm" 
                   placeholder="e.g., Quiz 1" 
                   required>
        </div>
        <div class="col-md-3">
            <input type="number" 
                   name="criteria[${term}][${category}][${criteriaCounter}][weight]" 
                   class="form-control form-control-sm weight-input" 
                   placeholder="Weight %" 
                   min="0" 
                   max="100" 
                   step="0.0001"
                   data-term="${term}"
                   data-category="${category}"
                   onchange="updateTotal('${term}', '${category}')"
                   required>
        </div>
        <div class="col-md-3">
            <select name="criteria[${term}][${category}][${criteriaCounter}][admin_only]" 
                    class="form-control form-control-sm">
                <option value="0">Professor Can Edit</option>
                <option value="1">Admin Only</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeCriteria(${criteriaCounter}, '${term}', '${category}')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(row);
}

// Remove criteria row
function removeCriteria(id, term, category) {
    document.getElementById(`criteria-${id}`).remove();
    updateTotal(term, category);
}

// Update total weight
function updateTotal(term, category) {
    const inputs = document.querySelectorAll(`input[data-term="${term}"][data-category="${category}"]`);
    let total = 0;
    inputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    const totalSpan = document.getElementById(`${term}-${category}-total`);
    totalSpan.textContent = total.toFixed(2) + '%';
    
    // Highlight if not 100%
    if (Math.abs(total - 100) < 0.01) {
        totalSpan.className = 'font-weight-bold text-success';
    } else {
        totalSpan.className = 'font-weight-bold text-danger';
    }
}

// Load default criteria based on subject type
function loadDefaultCriteria() {
    const hasLab = document.getElementById('has_lab').value === '1';
    
    if (!confirm('This will clear any existing criteria. Continue?')) {
        return;
    }
    
    // Clear all existing criteria
    document.querySelectorAll('.criteria-row').forEach(row => row.remove());
    
    const terms = ['prelim', 'midterm', 'finals'];
    
    terms.forEach(term => {
        // Add lecture criteria
        if (hasLab) {
            // LECTURE WITH LAB: Total must be 100%
            // 3 Quizzes: 25% total (8.33% each)
            addCriteriaWithValues(term, 'lecture', 'Quiz 1', 8.33, false);
            addCriteriaWithValues(term, 'lecture', 'Quiz 2', 8.33, false);
            addCriteriaWithValues(term, 'lecture', 'Quiz 3', 8.34, false); // .34 to round to 100%
            
            // 2 Assignments: 10% total (5% each)
            addCriteriaWithValues(term, 'lecture', 'Assignment 1', 5.00, false);
            addCriteriaWithValues(term, 'lecture', 'Assignment 2', 5.00, false);
            
            // 1 Class Participation: 25%
            addCriteriaWithValues(term, 'lecture', 'Class Participation', 25.00, false);
            
            // 1 Term Exam: 40% (Admin Only)
            addCriteriaWithValues(term, 'lecture', 'Term Exam', 40.00, true);
            
            // LAB CRITERIA: Total must be 100%
            // 2 Hands-on Exams: 30% total (15% each)
            addCriteriaWithValues(term, 'laboratory', 'Hands-on Exam 1', 15.00, false);
            addCriteriaWithValues(term, 'laboratory', 'Hands-on Exam 2', 15.00, false);
            
            // 4 Lab Activities: 70% total (17.5% each)
            addCriteriaWithValues(term, 'laboratory', 'Lab Activity 1', 17.50, false);
            addCriteriaWithValues(term, 'laboratory', 'Lab Activity 2', 17.50, false);
            addCriteriaWithValues(term, 'laboratory', 'Lab Activity 3', 17.50, false);
            addCriteriaWithValues(term, 'laboratory', 'Lab Activity 4', 17.50, false);
        } else {
            // LECTURE ONLY: Total must be 100%
            // 3 Quizzes: 30% total (10% each)
            addCriteriaWithValues(term, 'lecture', 'Quiz 1', 10.00, false);
            addCriteriaWithValues(term, 'lecture', 'Quiz 2', 10.00, false);
            addCriteriaWithValues(term, 'lecture', 'Quiz 3', 10.00, false);
            
            // 2 Assignments: 15% total (7.5% each)
            addCriteriaWithValues(term, 'lecture', 'Assignment 1', 7.50, false);
            addCriteriaWithValues(term, 'lecture', 'Assignment 2', 7.50, false);
            
            // 1 Class Participation: 25%
            addCriteriaWithValues(term, 'lecture', 'Class Participation', 25.00, false);
            
            // 1 Term Exam: 30% (Admin Only)
            addCriteriaWithValues(term, 'lecture', 'Term Exam', 30.00, true);
        }
        
        updateTotal(term, 'lecture');
        if (hasLab) {
            updateTotal(term, 'laboratory');
        }
    });
    
    alert('Default criteria loaded successfully!\n\n' + 
          'Lecture: 3 Quizzes, 2 Assignments, 1 Class Participation, 1 Term Exam\n' +
          (hasLab ? 'Laboratory: 2 Hands-on Exams, 4 Lab Activities' : ''));
}

// Helper function to add criteria with specific values
function addCriteriaWithValues(term, category, name, weight, adminOnly) {
    criteriaCounter++;
    const container = document.getElementById(`${term}-${category}-criteria`);
    
    const row = document.createElement('div');
    row.className = 'row mb-2 criteria-row';
    row.id = `criteria-${criteriaCounter}`;
    
    row.innerHTML = `
        <div class="col-md-5">
            <input type="text" 
                   name="criteria[${term}][${category}][${criteriaCounter}][name]" 
                   class="form-control form-control-sm" 
                   value="${name}"
                   required>
        </div>
        <div class="col-md-3">
            <input type="number" 
                   name="criteria[${term}][${category}][${criteriaCounter}][weight]" 
                   class="form-control form-control-sm weight-input" 
                   value="${weight}"
                   min="0" 
                   max="100" 
                   step="0.0001"
                   data-term="${term}"
                   data-category="${category}"
                   onchange="updateTotal('${term}', '${category}')"
                   required>
        </div>
        <div class="col-md-3">
            <select name="criteria[${term}][${category}][${criteriaCounter}][admin_only]" 
                    class="form-control form-control-sm">
                <option value="0" ${!adminOnly ? 'selected' : ''}>Professor Can Edit</option>
                <option value="1" ${adminOnly ? 'selected' : ''}>Admin Only</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeCriteria(${criteriaCounter}, '${term}', '${category}')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(row);
}

// Initialize
document.getElementById('has_lab').dispatchEvent(new Event('change'));
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\student-record-ver8\resources\views/subjects/create.blade.php ENDPATH**/ ?>