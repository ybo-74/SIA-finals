<ul class="navbar-nav bg-gradient-secondary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('professor.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Faculty</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('professor.dashboard') }}">
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
    <!-- Nav Item - Students -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStudents"
            aria-expanded="true" aria-controls="collapseStudents">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span>Students</span>
        </a>
        <div id="collapseStudents" class="collapse" aria-labelledby="headingStudents" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Student Management:</h6>
                <a class="collapse-item" href="{{ route('students.index') }}">View All Students</a>
                <a class="collapse-item" href="{{ route('students.create') }}">Register Student</a>
                <a class="collapse-item" href="{{ route('students.import.form') }}">Import CSV</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Sections -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSections"
            aria-expanded="true" aria-controls="collapseSections">
            <i class="fas fa-fw fa-users"></i>
            <span>Sections</span>
        </a>
        <div id="collapseSections" class="collapse" aria-labelledby="headingSections" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Section Management:</h6>
                <a class="collapse-item" href="{{ route('sections.index') }}">My Sections</a>
                <a class="collapse-item" href="{{ route('sections.create') }}">Create New Section</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - My Subjects -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('professor.dashboard') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>My Subjects</span>
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
