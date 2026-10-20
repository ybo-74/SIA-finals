

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">My Sections</h1>
        <a href="<?php echo e(route('sections.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Section
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($sections->isEmpty()): ?>
        <div class="alert alert-info">
            You haven't created any sections yet. Click "Create New Section" to get started.
        </div>
    <?php else: ?>
        <div class="row">
            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow h-100">
                       <div class="card-header bg-secondary text-white">
                            <h6 class="m-0 font-weight-bold"><?php echo e($section->name); ?></h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Subject:</strong> <?php echo e($section->subject->code); ?> - <?php echo e($section->subject->name); ?>

                            </p>
                            <p class="mb-2">
                                <strong>Year Level:</strong> 
                                <span class="badge bg-info"><?php echo e($section->year_level); ?></span>
                            </p>
                            <p class="mb-0">
                                <strong>Students Enrolled:</strong> <?php echo e($section->students->count()); ?>

                            </p>
                        </div>
                        <div class="card-footer">
                        <a href="<?php echo e(route('sections.show', $section)); ?>" class="btn btn-sm btn-success btn-block">
                                <i class="fas fa-eye"></i> View Students & Grades
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.professor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\student-record-ver8\resources\views/sections/index.blade.php ENDPATH**/ ?>