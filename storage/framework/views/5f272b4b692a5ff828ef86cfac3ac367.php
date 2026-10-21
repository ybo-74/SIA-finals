

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Subjects</h1>
    <a href="<?php echo e(route('subjects.create')); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Subject
    </a>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php if($subjects->isEmpty()): ?>
    <div class="alert alert-warning">No subjects found.</div>
<?php else: ?>
    <div class="card shadow">
        <div class="card-body table-responsive">
            <div class="mb-3">
                <input type="text" id="subjectSearchInput" class="form-control" placeholder="Search subjects...">
            </div>

            <table class="table table-bordered table-hover" id="subjectsTable">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Professor</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($subject->name); ?></td>
                            <td><?php echo e($subject->code); ?></td>
                            <td><?php echo e($subject->professor->name ?? 'Unassigned'); ?></td>
                            <td>
                                <a href="<?php echo e(route('subjects.edit', $subject)); ?>" class="btn btn-sm btn-warning">Edit</a>
                                <form action="<?php echo e(route('subjects.destroy', $subject)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this subject?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <p id="subjectNoResults" class="text-muted mt-2" style="display: none;">No matching subjects found.</p>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('subjectSearchInput');
    const tableBody = document.querySelector('#subjectsTable tbody');

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('keyup', function () {
        const query = this.value.toLowerCase();
        const rows = tableBody.querySelectorAll('tr');
        let anyVisible = false;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const match = text.includes(query);
            row.style.display = match ? '' : 'none';
            if (match) anyVisible = true;
        });

        const noResults = document.getElementById('subjectNoResults');
        if (noResults) {
            noResults.style.display = anyVisible ? 'none' : '';
        }
    });
});
</script>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/subjects/index.blade.php ENDPATH**/ ?>