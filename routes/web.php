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
use App\Http\Controllers\Catalog\CartController as CatalogCartController;
use App\Http\Controllers\Admin\GiftCardController as AdminGiftCardController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SeoSettingController as AdminSeoSettingController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\ImportExportController as AdminImportExportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('robots.txt', function () {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Disallow: /admin/\n";
    $content .= "Disallow: /cart/\n";
    $content .= "Disallow: /checkout/\n";
    $content .= "Disallow: /gift-cards/redeem\n";
    $content .= "\nSitemap: " . url('sitemap.xml');
    return response($content, 200, ['Content-Type' => 'text/plain']);
});

// Static Pages Routes
Route::get('/', [StaticPageController::class, 'home'])->name('static.home');
Route::get('/chi-sono', [StaticPageController::class, 'about'])->name('static.about');
Route::get('/contatti', [StaticPageController::class, 'contact'])->name('static.contact');
Route::post('/contatti', [StaticPageController::class, 'submitContact'])->name('static.contact.submit');
Route::get('/workout-online', [StaticPageController::class, 'workoutOnline'])->name('static.workout-online');
Route::get('/workout-in-studio', [StaticPageController::class, 'workoutInStudio'])->name('static.workout-in-studio');
Route::get('/prenota-una-consulenza', [StaticPageController::class, 'bookAConsultation'])->name('static.book-a-consultation');
Route::post('/prenota-una-consulenza', [StaticPageController::class, 'submitBookAConsultation'])->name('static.book-a-consultation.submit');

// Blog (public)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Newsletter Subscription
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

// Public Catalog Routes
Route::get('/catalog', [CatalogCourseController::class, 'index'])->name('catalog.index');
Route::get('/catalog/courses/{course}', [CatalogCourseController::class, 'show'])->name('catalog.show');
// Privacy & Cookie Policy (public)
Route::view('/privacy-policy', 'static.privacy-policy')->name('privacy-policy');
Route::view('/cookie-policy', 'static.cookie-policy')->name('cookie-policy');
Route::middleware('auth')->group(function () {
    Route::get('/catalog/courses/{course}/checkout', [CatalogCourseController::class, 'purchase'])->name('catalog.checkout');
    Route::get('/catalog/checkout/success', [CatalogCourseController::class, 'success'])->name('catalog.checkout.success');
    Route::get('/catalog/checkout/cancel', [CatalogCourseController::class, 'cancel'])->name('catalog.checkout.cancel');
});

// Gift Cards Routes
Route::get('/gift-cards', [CatalogGiftCardController::class, 'index'])->name('giftcards.index');
// Public redeem form so recipients can access the page directly from email, will prompt login on submit
Route::get('/gift-cards/redeem', [CatalogGiftCardController::class, 'redeemForm'])->name('giftcards.redeem');
// Checkout route handles both GET (after login/registration) and POST (form submission)
Route::match(['GET','POST'], '/gift-cards/{course}/checkout', [CatalogGiftCardController::class, 'checkout'])->name('giftcards.checkout');
Route::middleware('auth')->group(function () {
    Route::post('/gift-cards/redeem', [CatalogGiftCardController::class, 'redeem'])->name('giftcards.redeem.submit');
    Route::get('/gift-cards/checkout/success', [CatalogGiftCardController::class, 'success'])->name('giftcards.checkout.success');
    Route::get('/gift-cards/checkout/cancel', [CatalogGiftCardController::class, 'cancel'])->name('giftcards.checkout.cancel');
});
// Keep parameterized route last to avoid conflicts with above paths
Route::get('/gift-cards/{course}', [CatalogGiftCardController::class, 'show'])->name('giftcards.show');

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CatalogCartController::class, 'index'])->name('index');
    Route::get('/state', [CatalogCartController::class, 'state'])->name('state');
    Route::post('/add-course/{course}', [CatalogCartController::class, 'addCourse'])->name('add-course');
    Route::post('/add-gift-card/{course}', [CatalogCartController::class, 'addGiftCard'])->name('add-gift-card');
    Route::delete('/item/{id}', [CatalogCartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CatalogCartController::class, 'clear'])->name('clear');
    Route::get('/checkout', [CatalogCartController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [CatalogCartController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CatalogCartController::class, 'cancel'])->name('checkout.cancel');
});

// Student Routes (using default auth)
Route::middleware(['auth', 'verified', 'student.auth'])->group(function () {
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

        // Media Library
        Route::get('/media', [AdminMediaController::class, 'index'])->name('media.index');
        Route::get('/media/list', [AdminMediaController::class, 'list'])->name('media.list'); // JSON
        Route::post('/media', [AdminMediaController::class, 'store'])->name('media.store');
        Route::delete('/media/{media}', [AdminMediaController::class, 'destroy'])->name('media.destroy');
        
        // Student Management
        Route::resource('students', AdminStudentController::class);
        
        // Student Enrollment Management
        Route::get('/students/{student}/enrollments', [AdminStudentController::class, 'enrollments'])->name('students.enrollments');
        Route::post('/students/{student}/enrollments', [AdminStudentController::class, 'storeEnrollment'])->name('students.enrollments.store');
        Route::patch('/students/{student}/enrollments/{enrollment}/toggle', [AdminStudentController::class, 'toggleEnrollment'])->name('students.enrollments.toggle');
        Route::patch('/students/{student}/enrollments/{enrollment}/expires', [AdminStudentController::class, 'updateEnrollmentExpiration'])->name('students.enrollments.expires');
        Route::delete('/students/{student}/enrollments/{enrollment}', [AdminStudentController::class, 'destroyEnrollment'])->name('students.enrollments.destroy');
        
        // Progress Management
        Route::get('/progress', [AdminProgressController::class, 'index'])->name('progress.index');
        Route::get('/progress/course/{course}', [AdminProgressController::class, 'course'])->name('progress.course');
        Route::get('/progress/student/{student}', [AdminProgressController::class, 'student'])->name('progress.student');
        Route::get('/progress/lesson/{lesson}', [AdminProgressController::class, 'lesson'])->name('progress.lesson');
        Route::patch('/progress/student/{student}/lesson/{lesson}', [AdminProgressController::class, 'updateLessonProgress'])->name('progress.update');
        Route::delete('/progress/student/{student}/course/{course}/reset', [AdminProgressController::class, 'resetCourseProgress'])->name('progress.reset');
        Route::get('/progress/export', [AdminProgressController::class, 'export'])->name('progress.export');
        
        // Workout Cards Management (restricted by permission via Gate)
        Route::middleware('can:workout-cards.manage')->group(function () {
            Route::resource('workout-cards', AdminWorkoutCardController::class);
            Route::get('/workout-cards/builder/{course?}', [AdminWorkoutCardController::class, 'builder'])->name('workout-cards.builder');
            Route::post('/workout-cards/builder', [AdminWorkoutCardController::class, 'storeFromBuilder'])->name('workout-cards.store-builder');
        });

        // Blog Posts Management
        Route::resource('blog-posts', AdminBlogPostController::class);

        // Settings - Contatti (mappa e destinatario)
        Route::get('/settings/contact', [AdminSettingController::class, 'editContact'])->name('settings.contact.edit');
        Route::post('/settings/contact', [AdminSettingController::class, 'updateContact'])->name('settings.contact.update');

        // Settings - SEO
        Route::get('/settings/seo', [AdminSeoSettingController::class, 'edit'])->name('settings.seo.edit');
        Route::post('/settings/seo', [AdminSeoSettingController::class, 'update'])->name('settings.seo.update');
        
        // Enrollment Management
        Route::resource('enrollments', AdminEnrollmentController::class);
        Route::post('/enrollments/bulk-action', [AdminEnrollmentController::class, 'bulkAction'])->name('enrollments.bulk-action');
        Route::get('/enrollments-export', [AdminEnrollmentController::class, 'export'])->name('enrollments.export');

        // Payments Management
        Route::resource('payments', AdminPaymentController::class)->only(['index', 'show']);

        // Gift Cards Management
        Route::resource('giftcards', AdminGiftCardController::class)->only(['index', 'show']);
        Route::post('/giftcards/{giftcard}/resend', [AdminGiftCardController::class, 'resend'])->name('giftcards.resend');

        // Newsletter Management
        Route::get('/newsletters', [AdminNewsletterController::class, 'index'])->name('newsletters.index');

        // Import/Export Data Management
        Route::prefix('data')->name('data.')->group(function () {
            Route::get('/', [AdminImportExportController::class, 'index'])->name('index');
            // Export
            Route::get('/export/courses', [AdminImportExportController::class, 'exportCourses'])->name('export.courses');
            Route::get('/export/students', [AdminImportExportController::class, 'exportStudents'])->name('export.students');
            Route::get('/export/enrollments', [AdminImportExportController::class, 'exportEnrollments'])->name('export.enrollments');
            Route::get('/export/payments', [AdminImportExportController::class, 'exportPayments'])->name('export.payments');
            // Import
            Route::post('/import/courses', [AdminImportExportController::class, 'importCourses'])->name('import.courses');
            Route::post('/import/students', [AdminImportExportController::class, 'importStudents'])->name('import.students');
            Route::post('/import/enrollments', [AdminImportExportController::class, 'importEnrollments'])->name('import.enrollments');
            Route::post('/import/payments', [AdminImportExportController::class, 'importPayments'])->name('import.payments');
        });
    });
});

require __DIR__.'/auth.php';
