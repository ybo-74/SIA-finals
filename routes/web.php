<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\Auth\PasswordController;

// Root redirect
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Role-aware dashboard redirect
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('professor')) {
        return redirect()->route('professor.dashboard');
    }

    if ($user->hasRole('student')) {
        return redirect()->route('student.dashboard');
    }

    abort(403);
})->middleware(['auth', 'verified'])->name('dashboard');

// Role-specific dashboards
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::get('/professor/dashboard', [DashboardController::class, 'professor'])
        ->middleware('role:professor')
        ->name('professor.dashboard');

    Route::get('/student/dashboard', [DashboardController::class, 'student'])
        ->middleware('role:student')
        ->name('student.dashboard');

        Route::get('/developers', function () {return view('dashboards.developers');})
        ->name('developers');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    // -------------------------
    // Admin-only routes
    // -------------------------
    Route::middleware(['role:admin'])->group(function () {
        // Subjects
        Route::resource('subjects', SubjectController::class)->except(['show']);
        
        // Professors - Manual Registration (NOT CSV)
        Route::get('/professors', [ProfessorController::class, 'index'])->name('professors.index');
        Route::get('/professors/create', [ProfessorController::class, 'create'])->name('professors.create');
        Route::post('/professors', [ProfessorController::class, 'store'])->name('professors.store');
        Route::get('/professors/{professor}/edit', [ProfessorController::class, 'edit'])->name('professors.edit');
        Route::put('/professors/{professor}', [ProfessorController::class, 'update'])->name('professors.update');
        Route::delete('/professors/{professor}', [ProfessorController::class, 'destroy'])->name('professors.destroy');
        
        // Student Registration - Admin & Professor

        // Admin Grade Input (Term Exam + All Exams)
        Route::get('/sections/{section}/admin-grades', [GradeController::class, 'adminGradeInput'])->name('grades.admin.input');
        Route::post('/sections/{section}/admin-grades', [GradeController::class, 'storeGrade'])->name('grades.admin.store');
        
        // Audit Trail - Admin Only
        Route::get('/audit-trail', [App\Http\Controllers\AuditTrailController::class, 'index'])->name('audit.index');
        Route::get('/audit-trail/{audit}', [App\Http\Controllers\AuditTrailController::class, 'show'])->name('audit.show');
    });

    // -------------------------
    // Professor routes
    // -------------------------
    Route::middleware(['role:professor'])->group(function () {
        // Student Registration - Professors can also register students
        
        // Sections
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
        Route::get('/sections/create', [SectionController::class, 'create'])->name('sections.create');
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
        Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show');
        
        // CSV Student Enrollment for Section
        Route::get('/sections/{section}/enroll-students', [SectionController::class, 'enrollStudentsForm'])->name('sections.enroll.form');
        Route::post('/sections/{section}/enroll-students', [SectionController::class, 'enrollStudentsCsv'])->name('sections.enroll.csv');
        
        // Professor Grade Input (ALL Lecture + ALL Laboratory - EXCEPT Term Exam)
        Route::get('/sections/{section}/professor-grades', [GradeController::class, 'professorGradeInput'])->name('grades.professor.input');
        Route::post('/sections/{section}/professor-grades', [GradeController::class, 'storeGrade'])->name('grades.professor.store');
        
        // Delete grades
        Route::delete('/grades/delete', [GradeController::class, 'deleteGrade'])->name('grades.delete');

        // Dynamic Grade Criteria Management
        Route::post('/sections/{section}/add-criteria', [GradeController::class, 'addCustomCriteria'])->name('grades.add-criteria');
        Route::delete('/criteria/{criteria}', [GradeController::class, 'deleteCriteria'])->name('grades.delete-criteria');
        
        // Export Class Record to Excel
        Route::get('/sections/{section}/export-excel', [App\Http\Controllers\ExportController::class, 'exportClassRecord'])->name('sections.export.excel');
    });

    Route::middleware(['role:admin|professor'])->group(function () {
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');

        // Student Registration - Admin & Professor (registered ONCE; duplicates made professors get 403)
        Route::get('/students/register', [App\Http\Controllers\StudentRegistrationController::class, 'create'])->name('students.create');
        Route::post('/students/register', [App\Http\Controllers\StudentRegistrationController::class, 'store'])->name('students.store');
        Route::get('/students/import', [App\Http\Controllers\StudentRegistrationController::class, 'importForm'])->name('students.import.form');
        Route::post('/students/import', [App\Http\Controllers\StudentRegistrationController::class, 'importCsv'])->name('students.import.csv');
        Route::post('/criteria/{criteria}/update-max', [GradeController::class, 'updateMaxScore']) ->middleware('auth');
    });

    // -------------------------
    // Student routes - FIXED: NO SELF-ENROLLMENT
    // -------------------------
    Route::middleware(['role:student'])->group(function () {
        // View own grades ONLY
        Route::get('/my-grades', [GradeController::class, 'studentView'])->name('grades.student.view');
    });
});

require __DIR__.'/auth.php';
