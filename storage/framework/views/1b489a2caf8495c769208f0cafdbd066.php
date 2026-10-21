<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Bulk Import Students via CSV</h1>
        <div>
            <a href="<?php echo e(route('students.create')); ?>" class="btn btn-success mr-2">
                <i class="fas fa-user-plus"></i> Manual Registration
            </a>
            <a href="<?php echo e(route('students.index')); ?>" class="btn btn-secondary">
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
                <li>CSV must have a header row with columns: <code>first_name</code>, <code>last_name</code>, <code>middle_name</code> (optional), <code>email</code>, <code>password</code>, <code>year_level</code> (optional)</li>
                <li>Each row should contain a student's information</li>
                <li>Email addresses must be unique (not already in the system)</li>
                <li><strong>Required fields:</strong> first_name, last_name, email, password</li>
                <li><strong>Optional fields:</strong> middle_name (leave empty if none), year_level (defaults to "1st Year" if not provided)</li>
            </ul>

            <h6 class="text-success mt-3">Example CSV Format:</h6>
            <div class="bg-light p-3 border rounded">
                <code>
                    first_name,last_name,middle_name,email,password,year_level<br>
                    Juan,Dela Cruz,Santos,juan.delacruz@philsca.com,student123,1st Year<br>
                    Maria,Reyes,,maria.reyes@philsca.com,student456,2nd Year<br>
                    Pedro,Garcia,Lopez,pedro.garcia@philsca.com,student789,1st Year
                </code>
            </div>

            <div class="alert alert-light mt-3">
                <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> 
                Notice how Maria Reyes has no middle name - just leave that column empty in the CSV.
            </div>

            <div class="mt-3">
                <button class="btn btn-sm btn-outline-success" onclick="downloadSampleCSV()">
                    <i class="fas fa-download"></i> Download Sample CSV
                </button>
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
            <form action="<?php echo e(route('students.import.csv')); ?>" 
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

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Important:</strong> Make sure all email addresses are unique and valid. 
                    Students will use these credentials to login.
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-upload"></i> Upload and Register Students
                    </button>
                    <a href="<?php echo e(route('students.index')); ?>" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- CSV Format Reference Card -->
    <div class="card shadow mb-4 border-success">
        <div class="card-header bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-table"></i> CSV Column Reference
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Column Name</th>
                            <th>Required</th>
                            <th>Description</th>
                            <th>Example</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>first_name</code></td>
                            <td><span class="badge badge-danger">Required</span></td>
                            <td>Student's first name</td>
                            <td>Juan</td>
                        </tr>
                        <tr>
                            <td><code>last_name</code></td>
                            <td><span class="badge badge-danger">Required</span></td>
                            <td>Student's last name</td>
                            <td>Dela Cruz</td>
                        </tr>
                        <tr>
                            <td><code>middle_name</code></td>
                            <td><span class="badge badge-secondary">Optional</span></td>
                            <td>Student's middle name (leave empty if none)</td>
                            <td>Santos</td>
                        </tr>
                        <tr>
                            <td><code>email</code></td>
                            <td><span class="badge badge-danger">Required</span></td>
                            <td>Student's email address (must be unique)</td>
                            <td>juan.delacruz@philsca.com</td>
                        </tr>
                        <tr>
                            <td><code>password</code></td>
                            <td><span class="badge badge-danger">Required</span></td>
                            <td>Initial password (minimum 8 characters)</td>
                            <td>student123</td>
                        </tr>
                        <tr>
                            <td><code>year_level</code></td>
                            <td><span class="badge badge-secondary">Optional</span></td>
                            <td>Student's year level (defaults to "1st Year")</td>
                            <td>1st Year, 2nd Year, 3rd Year, 4th Year</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function downloadSampleCSV() {
    // Create sample CSV content with new format
    const csvContent = "first_name,last_name,middle_name,email,password,year_level\n" +
                       "Juan,Dela Cruz,Santos,juan.delacruz@philsca.com,student123,1st Year\n" +
                       "Maria,Reyes,,maria.reyes@philsca.com,student456,2nd Year\n" +
                       "Pedro,Garcia,Lopez,pedro.garcia@philsca.com,student789,1st Year";
    
    // Create blob and download
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'sample_student_registration.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.professor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/students/import.blade.php ENDPATH**/ ?>