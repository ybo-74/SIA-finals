<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Management
    </div>

    <!-- Nav Item - Subjects -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSubjects"
            aria-expanded="true" aria-controls="collapseSubjects">
            <i class="fas fa-fw fa-book"></i>
            <span>Subjects</span>
        </a>
        <div id="collapseSubjects" class="collapse" aria-labelledby="headingSubjects" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Subject Management:</h6>
                <a class="collapse-item" href="{{ route('subjects.index') }}">View All Subjects</a>
                <a class="collapse-item" href="{{ route('subjects.create') }}">Add New Subject</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Professors -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProfessors"
            aria-expanded="true" aria-controls="collapseProfessors">
            <i class="fas fa-fw fa-chalkboard-teacher"></i>
            <span>Professors</span>
        </a>
        <div id="collapseProfessors" class="collapse" aria-labelledby="headingProfessors" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Professor Management:</h6>
                <a class="collapse-item" href="{{ route('professors.index') }}">View All Professors</a>
                <a class="collapse-item" href="{{ route('professors.create') }}">Register New Professor</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Students -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('students.index') }}">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span>Students</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        System
    </div>

    <!-- Nav Item - Audit Trail -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('audit.index') }}">
            <i class="fas fa-fw fa-history"></i>
            <span>Audit Trail</span>
        </a>
    </li>

    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Developers -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('developers') }}">
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
