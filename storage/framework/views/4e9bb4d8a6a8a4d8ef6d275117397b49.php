<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">About the Developers</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-users"></i> Development Team
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-left-primary h-100">
                        <div class="card-body">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user-tie"></i> Project Manager
                            </h5>
                            <p class="mb-0"><strong>Asst. Prof. Aron J. Alojado</strong></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-left-success h-100">
                        <div class="card-body">
                            <h5 class="text-success mb-3">
                                <i class="fas fa-code"></i> Developers
                            </h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check-circle text-success"></i> <strong>Ymmanuela Bo E. Cabalquinto</strong></li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success"></i> <strong>Chloe Isobelle Palma</strong></li>
                                <li class="mb-0"><i class="fas fa-check-circle text-success"></i> <strong>Alicia Maye Sharllotte Quinto</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <h5 class="text-info mb-3">
                                <i class="fas fa-heart"></i> Special Thanks
                            </h5>
                            <p class="mb-0">
                                <strong>Earl Justin Paje</strong> for participating in every testing stage.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 border-secondary">
        <div class="card-body text-center">
            <p class="text-muted mb-0">
                <i class="fas fa-graduation-cap"></i> 
                <strong>ICS Student Record System</strong>
            </p>
            <small class="text-muted">National Aviation Academy of the Philippines &copy; <?php echo e(date('Y')); ?></small>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->user()->hasRole('admin') ? 'layouts.admin' : (auth()->user()->hasRole('professor') ? 'layouts.professor' : 'layouts.student'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/dashboards/developers.blade.php ENDPATH**/ ?>