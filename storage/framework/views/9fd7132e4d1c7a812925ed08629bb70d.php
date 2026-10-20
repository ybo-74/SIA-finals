<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Admin Dashboard</h1>
    <div>
        <a href="<?php echo e(route('subjects.create')); ?>" class="btn btn-primary">
           <i class="fas fa-plus"></i> Add New Subject
        </a>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<!-- Filter Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-filter"></i> Filter Sections & Students
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label for="academic_year" class="form-label">Academic Year</label>
                    <select name="academic_year" id="academic_year" class="form-control">
                        <option value="">All Academic Years</option>
                        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($year); ?>" <?php echo e(request('academic_year') == $year ? 'selected' : ''); ?>>
                                <?php echo e($year); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="semester" class="form-label">Semester</label>
                    <select name="semester" id="semester" class="form-control">
                        <option value="">All Semesters</option>
                        <option value="1st Semester" <?php echo e(request('semester') == '1st Semester' ? 'selected' : ''); ?>>1st Semester</option>
                        <option value="2nd Semester" <?php echo e(request('semester') == '2nd Semester' ? 'selected' : ''); ?>>2nd Semester</option>
                        <option value="Summer" <?php echo e(request('semester') == 'Summer' ? 'selected' : ''); ?>>Summer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year_level" class="form-label">Year Level</label>
                    <select name="year_level" id="year_level" class="form-control">
                        <option value="">All Year Levels</option>
                        <?php $__currentLoopData = $yearLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($level); ?>" <?php echo e(request('year_level') == $level ? 'selected' : ''); ?>>
                                <?php echo e($level); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Subjects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($subjects->count()); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sections <?php echo e(request('academic_year') || request('semester') || request('year_level') ? '(Filtered)' : ''); ?>

                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($sections->count()); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($students->count()); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Class Average</div>
                        <?php
                            $classAvg = $students->where('final_grade', '>', 0)->avg('final_grade');
                        ?>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo e($classAvg ? number_format($classAvg, 2) : '-'); ?>

                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Rankings -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-trophy"></i> Top Students by Final Grade
            <?php if(request('year_level')): ?>
                <span class="badge badge-info"><?php echo e(request('year_level')); ?></span>
            <?php endif; ?>
        </h6>
        <?php if(request('academic_year') || request('semester') || request('year_level')): ?>
            <span class="badge badge-info">Filtered Results</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if($students->where('final_grade', '>', 0)->isEmpty()): ?>
            <div class="alert alert-info">No student grades recorded yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%">Rank</th>
                            <th style="width: 30%">Student Name</th>
                            <th style="width: 25%">Email</th>
                            <th style="width: 10%">Year Level</th>
                            <th style="width: 10%">Sections</th>
                            <th style="width: 15%">Final Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $students->where('final_grade', '>', 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center">
                                    <?php if($index === 0): ?>
                                        <span class="badge bg-warning text-dark" style="font-size: 1.2em;">
                                            <i class="fas fa-trophy"></i> #1
                                        </span>
                                    <?php elseif($index === 1): ?>
                                        <span class="badge bg-secondary" style="font-size: 1.1em;">
                                            <i class="fas fa-medal"></i> #2
                                        </span>
                                    <?php elseif($index === 2): ?>
                                        <span class="badge bg-info" style="font-size: 1em;">
                                            <i class="fas fa-medal"></i> #3
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">#<?php echo e($index + 1); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo e($student->formatted_name); ?></strong></td>
                                <td><small class="text-muted"><?php echo e($student->email); ?></small></td>
                                <td class="text-center">
                                    <span class="badge badge-info"><?php echo e($student->year_level ?? 'N/A'); ?></span>
                                </td>
                                <td class="text-center"><?php echo e($student->sections->count()); ?></td>
                                <td class="text-center">
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
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Sections Overview -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">
            <i class="fas fa-users"></i> All Sections - Grade Admin Access
        </h6>
    </div>
    <div class="card-body">
        <?php if($sections->isEmpty()): ?>
            <div class="alert alert-info">No sections found with current filters.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Section Name</th>
                            <th>Subject</th>
                            <th>Professor</th>
                            <th>Year Level</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($section->name); ?></strong></td>
                                <td><?php echo e($section->subject->code); ?> - <?php echo e($section->subject->name); ?></td>
                                <td><?php echo e($section->professor->name); ?></td>
                                <td><span class="badge bg-info"><?php echo e($section->year_level); ?></span></td>
                                <td><span class="badge bg-primary"><?php echo e($section->academic_year); ?></span></td>
                                <td><span class="badge bg-success"><?php echo e($section->semester); ?></span></td>
                                <td class="text-center"><?php echo e($section->students->count()); ?></td>
                                <td>
                                    <a href="<?php echo e(route('grades.admin.input', $section)); ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-lock"></i> Input Exams
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Subjects Overview -->
<?php if($subjects->isNotEmpty()): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Subjects Overview</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Subject Name</th>
                            <th>Code</th>
                            <th>Professor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($subject->name); ?></td>
                                <td><span class="badge bg-info text-dark"><?php echo e($subject->code); ?></span></td>
                                <td><?php echo e($subject->professor->name ?? 'Unassigned'); ?></td>
                                <td>
                                    <a href="<?php echo e(route('subjects.edit', $subject)); ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Quick Link to Audit Trail -->
<div class="card shadow mb-4 border-left-primary">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="font-weight-bold text-primary mb-1">
                    <i class="fas fa-history"></i> Audit Trail
                </h6>
                <p class="text-muted mb-0">View all system activity and changes</p>
            </div>
            <a href="<?php echo e(route('audit.index')); ?>" class="btn btn-primary">
                <i class="fas fa-eye"></i> View Audit Logs
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\student-record-ver8\resources\views/dashboards/admin.blade.php ENDPATH**/ ?>