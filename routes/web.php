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
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;
use App\Http\Controllers\Catalog\GiftCardController as CatalogGiftCardController;
use App\Http\Controllers\Admin\GiftCardController as AdminGiftCardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Home: show catalog to guests, dashboard to authenticated users
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('catalog.index');
});

// Public Catalog Routes
Route::get('/catalog', [CatalogCourseController::class, 'index'])->name('catalog.index');
Route::get('/catalog/courses/{course}', [CatalogCourseController::class, 'show'])->name('catalog.show');
Route::middleware('auth')->group(function () {
    Route::get('/catalog/courses/{course}/checkout', [CatalogCourseController::class, 'purchase'])->name('catalog.checkout');
    Route::get('/catalog/checkout/success', [CatalogCourseController::class, 'success'])->name('catalog.checkout.success');
    Route::get('/catalog/checkout/cancel', [CatalogCourseController::class, 'cancel'])->name('catalog.checkout.cancel');
});

// Gift Cards Routes
Route::get('/gift-cards', [CatalogGiftCardController::class, 'index'])->name('giftcards.index');
// Public redeem form so recipients can access the page directly from email, will prompt login on submit
Route::get('/gift-cards/redeem', [CatalogGiftCardController::class, 'redeemForm'])->name('giftcards.redeem');
Route::middleware('auth')->group(function () {
    Route::post('/gift-cards/redeem', [CatalogGiftCardController::class, 'redeem'])->name('giftcards.redeem.submit');
    Route::post('/gift-cards/{course}/checkout', [CatalogGiftCardController::class, 'checkout'])->name('giftcards.checkout');
    Route::get('/gift-cards/checkout/success', [CatalogGiftCardController::class, 'success'])->name('giftcards.checkout.success');
    Route::get('/gift-cards/checkout/cancel', [CatalogGiftCardController::class, 'cancel'])->name('giftcards.checkout.cancel');
});
// Keep parameterized route last to avoid conflicts with above paths
Route::get('/gift-cards/{course}', [CatalogGiftCardController::class, 'show'])->name('giftcards.show');

// Student Routes (using default auth)
Route::middleware(['auth', 'student.auth'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // Course routes for students
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/{course}', [StudentCourseController::class, 'show'])->name('show');
        Route::get('/{course}/workout', [StudentCourseController::class, 'workout'])->name('workout');
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
        Route::get('/workout-cards/builder/{course?}', [AdminWorkoutCardController::class, 'builder'])->name('workout-cards.builder');
        Route::post('/workout-cards/builder', [AdminWorkoutCardController::class, 'storeFromBuilder'])->name('workout-cards.store-builder');
        
        // Enrollment Management
        Route::resource('enrollments', AdminEnrollmentController::class);
        Route::post('/enrollments/bulk-action', [AdminEnrollmentController::class, 'bulkAction'])->name('enrollments.bulk-action');
        Route::get('/enrollments-export', [AdminEnrollmentController::class, 'export'])->name('enrollments.export');

        // Payments Management
        Route::resource('payments', AdminPaymentController::class)->only(['index', 'show']);

        // Gift Cards Management
        Route::resource('giftcards', AdminGiftCardController::class)->only(['index', 'show']);
        Route::post('/giftcards/{giftcard}/resend', [AdminGiftCardController::class, 'resend'])->name('giftcards.resend');
    });
});

require __DIR__.'/auth.php';
