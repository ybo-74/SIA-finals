

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800">Enroll Students via CSV</h1>
            <p class="text-muted mb-0">
                Section: <strong><?php echo e($section->name); ?></strong> | 
                Subject: <strong><?php echo e($section->subject->code); ?> - <?php echo e($section->subject->name); ?></strong>
            </p>
        </div>
        <a href="<?php echo e(route('sections.show', $section)); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Section
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?>

            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if(session('import_errors') && count(session('import_errors')) > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <strong>Some rows had issues:</strong>
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Instructions Card -->
    <div class="card shadow mb-4 border-info">
        <div class="card-header bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-info-circle"></i> CSV Format Instructions
            </h6>
        </div>
        <div class="card-body">
            <h6 class="text-primary">CSV File Requirements:</h6>
            <ul>
                <li>CSV must have a header row with column: <code>email</code></li>
                <li>Each row should contain a student's email address</li>
                <li>The email must belong to an existing student account</li>
                <li>Students already enrolled will be skipped</li>
            </ul>

            <h6 class="text-success mt-3">Example CSV Format:</h6>
            <div class="bg-light p-3 border rounded">
                <code>
                    email<br>
                    student1@philsca.com<br>
                    student2@philsca.com<br>
                    student3@philsca.com
                </code>
            </div>

            <div class="mt-3">
                <a href="#" class="btn btn-sm btn-outline-success" onclick="downloadSampleCSV()">
                    <i class="fas fa-download"></i> Download Sample CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Upload Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-upload"></i> Upload CSV File
            </h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('sections.enroll.csv', $section)); ?>" 
                  method="POST" 
                  enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label for="csv_file" class="form-label">
                        Select CSV File <span class="text-danger">*</span>
                    </label>
                    <input type="file" 
                           class="form-control <?php $__errorArgs = ['csv_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           id="csv_file" 
                           name="csv_file" 
                           accept=".csv,.txt" 
                           required>
                    <?php $__errorArgs = ['csv_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <small class="form-text text-muted">
                        Accepted formats: .csv, .txt (CSV format)
                    </small>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload and Enroll Students
                    </button>
                    <a href="<?php echo e(route('sections.show', $section)); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Current Students Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-users"></i> Currently Enrolled Students (<?php echo e($section->students->count()); ?>)
            </h6>
        </div>
        <div class="card-body">
            <?php if($section->students->isEmpty()): ?>
                <p class="text-muted mb-0">No students enrolled yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Enrolled Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $section->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($student->name); ?></td>
                                    <td><?php echo e($student->email); ?></td>
                                    <td><?php echo e($student->pivot->created_at->format('M d, Y')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function downloadSampleCSV() {
    // Create sample CSV content
    const csvContent = "email\nstudent1@philsca.com\nstudent2@philsca.com\nstudent3@philsca.com";
    
    // Create blob and download
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'sample_student_enrollment.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.professor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/sections/enroll-students.blade.php ENDPATH**/ ?>