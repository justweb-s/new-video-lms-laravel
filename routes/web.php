<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Catalog\CourseController as CatalogCourseController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\ProgressController as AdminProgressController;
use App\Http\Controllers\Admin\WorkoutCardController as AdminWorkoutCardController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;
use Illuminate\Support\Facades\Route;

// Redirect root to student login
Route::get('/', function () {
    return redirect()->route('login');
});

// Public Catalog Routes
Route::get('/catalog', [CatalogCourseController::class, 'index'])->name('catalog.index');
Route::get('/catalog/courses/{course}', [CatalogCourseController::class, 'show'])->name('catalog.show');
Route::middleware('auth')->group(function () {
    Route::get('/catalog/courses/{course}/purchase', [CatalogCourseController::class, 'purchase'])->name('catalog.purchase');
});

// Student Routes (using default auth)
Route::middleware(['auth', 'student.auth'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // Course routes for students
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/{course}', [StudentCourseController::class, 'show'])->name('show');
        Route::get('/{course}/lessons/{lesson}', [App\Http\Controllers\Student\LessonController::class, 'show'])->name('lesson');
    });
    
    // Progress tracking routes
    Route::prefix('progress')->name('progress.')->group(function () {
        Route::post('/update', [StudentProgressController::class, 'updateProgress'])->name('update');
        Route::post('/complete', [StudentProgressController::class, 'markAsCompleted'])->name('complete');
    });
});

// Student Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Protected Admin Routes
    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Course Management
        Route::resource('courses', AdminCourseController::class);
        
        // Section Management (nested under courses)
        Route::resource('courses.sections', AdminSectionController::class)->except(['index']);
        Route::get('/courses/{course}/sections', [AdminSectionController::class, 'index'])->name('courses.sections.index');
        
        // Lesson Management (nested under courses and sections)
        Route::resource('courses.sections.lessons', AdminLessonController::class)->except(['index']);
        Route::get('/courses/{course}/sections/{section}/lessons', [AdminLessonController::class, 'index'])->name('courses.sections.lessons.index');
        
        // Student Management
        Route::resource('students', AdminStudentController::class);
        
        // Student Enrollment Management
        Route::get('/students/{student}/enrollments', [AdminStudentController::class, 'enrollments'])->name('students.enrollments');
        Route::post('/students/{student}/enrollments', [AdminStudentController::class, 'storeEnrollment'])->name('students.enrollments.store');
        Route::patch('/students/{student}/enrollments/{enrollment}/toggle', [AdminStudentController::class, 'toggleEnrollment'])->name('students.enrollments.toggle');
        Route::delete('/students/{student}/enrollments/{enrollment}', [AdminStudentController::class, 'destroyEnrollment'])->name('students.enrollments.destroy');
        
        // Progress Management
        Route::get('/progress', [AdminProgressController::class, 'index'])->name('progress.index');
        Route::get('/progress/course/{course}', [AdminProgressController::class, 'course'])->name('progress.course');
        Route::get('/progress/student/{student}', [AdminProgressController::class, 'student'])->name('progress.student');
        Route::get('/progress/lesson/{lesson}', [AdminProgressController::class, 'lesson'])->name('progress.lesson');
        Route::patch('/progress/student/{student}/lesson/{lesson}', [AdminProgressController::class, 'updateLessonProgress'])->name('progress.update');
        Route::delete('/progress/student/{student}/course/{course}/reset', [AdminProgressController::class, 'resetCourseProgress'])->name('progress.reset');
        Route::get('/progress/export', [AdminProgressController::class, 'export'])->name('progress.export');
        
        // Workout Cards Management
        Route::resource('workout-cards', AdminWorkoutCardController::class);
        
        // Enrollment Management
        Route::resource('enrollments', AdminEnrollmentController::class);
        Route::post('/enrollments/bulk-action', [AdminEnrollmentController::class, 'bulkAction'])->name('enrollments.bulk-action');
        Route::get('/enrollments-export', [AdminEnrollmentController::class, 'export'])->name('enrollments.export');
    });
});

require __DIR__.'/auth.php';
