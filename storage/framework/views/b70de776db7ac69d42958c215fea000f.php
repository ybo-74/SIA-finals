<ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo e(route('student.dashboard')); ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Student</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="<?php echo e(route('student.dashboard')); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        My Classes
    </div>

    <!-- Nav Item - My Sections -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('student.dashboard')); ?>">
            <i class="fas fa-fw fa-book"></i>
            <span>My Subjects</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Grades
    </div>

    <!-- Nav Item - My Grades -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('grades.student.view')); ?>">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>View My Grades</span>
        </a>
    </li>

    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Developers -->
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('developers')); ?>">
            <i class="fas fa-fw fa-users"></i>
            <span>Developers</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<?php /**PATH C:\Users\User\Downloads\student-record-ver9\student-record-ver9\resources\views/components/sidebar-student.blade.php ENDPATH**/ ?>