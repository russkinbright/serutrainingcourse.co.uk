<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\CreateCourseController;
use App\Http\Controllers\MainCourseController;
use App\Http\Controllers\EditCourseController;
use App\Http\Controllers\EditSectionController;
use App\Http\Controllers\CreateSectionController;
use App\Http\Controllers\ConnectSectionController;
use App\Http\Controllers\RemoveSectionController;
use App\Http\Controllers\CreatePracticeController;
use App\Http\Controllers\PracticeQuestionController;
use App\Http\Controllers\EditpracticeQuestionController;
use App\Http\Controllers\CreateMockController;
use App\Http\Controllers\MockOneQuestionController;
use App\Http\Controllers\MockSecondQuestionController;
use App\Http\Controllers\EditMockOneQuestionController;
use App\Http\Controllers\EditMockSecondQuestionController;
use App\Http\Controllers\CourseDetailsController;
use App\Http\Controllers\PaymentFormController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ClearController;
use App\Http\Controllers\LearnerCertificateController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\AssignCourseController;
use App\Http\Controllers\LearnerAssignController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\CreateBlogController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LearnerLoginController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\LearnerRegistrationController;
use App\Http\Controllers\LearnerCartController;
use App\Http\Controllers\LearnerPaymentController;
use App\Http\Controllers\LearnerCourseController;
use App\Http\Controllers\FinalMockController;
use App\Http\Controllers\CertificateHandleController;
use App\Http\Controllers\HeaderFooterController;
use App\Http\Controllers\ViewProfileController;

Route::get('/', function () {
    return view('main.index');
});

Route::get('/contact', function () {
    return view('course.contact');
});

Route::get('/about-us', function () {
    return view('course.about');
});

Route::get('/blog', function () {
    return view('course.blog');
});

Route::get('/course', function () {
    return view('course.main-course');
})->name('course.page');

Route::get('/map', function () {
    return view('map.index');
})->name('map.index');

Route::get('/pay', function () {
    return view('emails.successPayment');
});

Route::get('/demo/checkout/success', function () {
    return view('emails.successPayment');
})->name('checkout.demosuccess');

// Clear Cache
Route::get('/clear', [ClearController::class, 'clearCache']);


Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginPage'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::get('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [AdminLoginController::class, 'showDashboard'])->name('admin.dashboard');
});

// Create Course 
Route::get('/create-course', [CreateCourseController::class, 'index'])->name('create.course');
Route::post('/create-course', [CreateCourseController::class, 'store'])->name('create.course.store');
Route::get('/check-title', [CreateCourseController::class, 'checkTitle'])->name('check.title');

// Edit Course
Route::get('/edit-course', [EditCourseController::class, 'index'])->name('edit-course.index');
Route::get('/edit-course/search', [EditCourseController::class, 'search'])->name('edit-course.search');
Route::get('/edit-course/{id}', [EditCourseController::class, 'show'])->name('edit-course.show');
Route::put('/edit-course/{id}', [EditCourseController::class, 'update'])->name('edit-course.update');
Route::get('/edit-course/check-title/{id}', [EditCourseController::class, 'checkTitle'])->name('edit-course.check-title');
Route::get('/edit-course/check-meta-title/{id}', [EditCourseController::class, 'checkMetaTitle'])->name('edit-course.check-meta-title');
Route::delete('/edit-course/{id}', [EditCourseController::class, 'destroy'])->name('edit-course.destroy');

// Create Section
Route::get('/section', [CreateSectionController::class, 'index'])->name('section.index');
Route::post('/section', [CreateSectionController::class, 'store'])->name('section.store');

// Connect Section to Course
Route::get('/section/connect', [ConnectSectionController::class, 'index'])->name('section.connect.index');
Route::get('/section/connect/search-courses', [ConnectSectionController::class, 'searchCourses'])->name('section.connect.searchCourses');
Route::get('/section/connect/search-sections', [ConnectSectionController::class, 'searchSections'])->name('section.connect.searchSections');
Route::post('/section/connect', [ConnectSectionController::class, 'store'])->name('section.connect.store');

// Remove Section to Course
Route::get('/section/remove', [RemoveSectionController::class, 'index'])->name('section.remove.index');
Route::get('/section/remove/search-courses', [RemoveSectionController::class, 'searchCourses'])->name('section.remove.searchCourses');
Route::get('/section/remove/sections/{course_unique_id}', [RemoveSectionController::class, 'getSections'])->name('section.remove.getSections');
Route::delete('/section/remove/{course_unique_id}/{section_unique_id}', [RemoveSectionController::class, 'removeSection'])->name('section.remove.delete');

//Edit and Delete Section 
Route::get('/edit-section', [EditSectionController::class, 'index'])->name('edit-section.index');
Route::get('/edit-section/search', [EditSectionController::class, 'search'])->name('edit-section.search');
Route::get('/edit-section/{id}', [EditSectionController::class, 'show'])->name('edit-section.show');
Route::put('/edit-section/{id}', [EditSectionController::class, 'update'])->name('edit-section.update');
Route::delete('/edit-section/{id}', [EditSectionController::class, 'destroy'])->name('edit-section.destroy');

// Create Practice
Route::get('/practice', [CreatePracticeController::class, 'index'])->name('practice.index');
Route::get('/practice/search', [CreatePracticeController::class, 'search'])->name('practice.search');
Route::post('/practice', [CreatePracticeController::class, 'store'])->name('practice.store');

// Create Practice Question
Route::get('/question', [PracticeQuestionController::class, 'index'])->name('question.index');
Route::get('/question/search', [PracticeQuestionController::class, 'search'])->name('question.search');
Route::post('/question', [PracticeQuestionController::class, 'store'])->name('question.store');
Route::get('/question/check-exists', [PracticeQuestionController::class, 'checkQuestionExists'])->name('question.check-exists');

//Edit Practice Question
Route::get('/editpracticequestion', [EditpracticeQuestionController::class, 'index'])->name('editpracticequestion.index');
Route::get('/editpracticequestion/search', [EditpracticeQuestionController::class, 'searchSections'])->name('editpracticequestion.search');
Route::get('/editpracticequestion/questions', [EditpracticeQuestionController::class, 'getQuestions'])->name('editpracticequestion.questions');
Route::get('/editpracticequestion/check-exists', [EditpracticeQuestionController::class, 'checkQuestionExists'])->name('editpracticequestion.check-exists');
Route::post('/editpracticequestion/update', [EditpracticeQuestionController::class, 'updateQuestion'])->name('editpracticequestion.update');
Route::post('/editpracticequestion/delete', [EditpracticeQuestionController::class, 'deleteQuestion'])->name('editpracticequestion.delete');
Route::post('/editpracticequestion/delete-all', [EditpracticeQuestionController::class, 'deleteAllQuestions'])->name('editpracticequestion.delete-all');

// Create Mock 
Route::get('/createmock', [CreateMockController::class, 'index'])->name('createmock.index');
Route::get('/createmock/search', [CreateMockController::class, 'search'])->name('createmock.search');
Route::post('/createmock', [CreateMockController::class, 'store'])->name('createmock.store');

// Create Mock 1 Question
Route::get('/mockonequestion', [MockOneQuestionController::class, 'index'])->name('mockonequestion.index');
Route::get('/mockonequestion/search', [MockOneQuestionController::class, 'search'])->name('mockonequestion.search');
Route::post('/mockonequestion', [MockOneQuestionController::class, 'store'])->name('mockonequestion.store');
Route::get('/mockonequestion/check-exists', [MockOneQuestionController::class, 'checkQuestionExists'])->name('mockonequestion.check-exists');

// Create Mock 2 Question
Route::get('/mocksecondquestion', [MockSecondQuestionController::class, 'index'])->name('mocksecondquestion.index');
Route::get('/mocksecondquestion/search', [MockSecondQuestionController::class, 'search'])->name('mocksecondquestion.search');
Route::get('/mocksecondquestion/questions', [MockSecondQuestionController::class, 'getQuestions'])->name('mocksecondquestion.questions');
Route::post('/mocksecondquestion', [MockSecondQuestionController::class, 'store'])->name('mocksecondquestion.store');

// Edit Mock 1 Question
Route::get('/editmockonequestion', [EditMockOneQuestionController::class, 'index'])->name('editmockonequestion.index');
Route::get('/editmockonequestion/search', [EditMockOneQuestionController::class, 'searchSections'])->name('editmockonequestion.search');
Route::get('/editmockonequestion/questions', [EditMockOneQuestionController::class, 'getQuestions'])->name('editmockonequestion.questions');
Route::post('/editmockonequestion/update', [EditMockOneQuestionController::class, 'updateQuestion'])->name('editmockonequestion.update');
Route::post('/editmockonequestion/delete', [EditMockOneQuestionController::class, 'deleteQuestion'])->name('editmockonequestion.delete');
Route::post('/editmockonequestion/delete-all', [EditMockOneQuestionController::class, 'deleteAllQuestions'])->name('editmockonequestion.delete-all');

// Edit Mock 2 Question 
Route::get('/editmocksecondquestion', [EditMockSecondQuestionController::class, 'index'])->name('editmocksecondquestion.index');
Route::get('/editmocksecondquestion/search', [EditMockSecondQuestionController::class, 'searchSections'])->name('editmocksecondquestion.search');
Route::get('/editmocksecondquestion/questions', [EditMockSecondQuestionController::class, 'getQuestions'])->name('editmocksecondquestion.questions');
Route::post('/editmocksecondquestion/update', [EditMockSecondQuestionController::class, 'updateQuestion'])->name('editmocksecondquestion.update');
Route::post('/editmocksecondquestion/delete', [EditMockSecondQuestionController::class, 'deleteQuestion'])->name('editmocksecondquestion.delete');
Route::post('/editmocksecondquestion/delete-all', [EditMockSecondQuestionController::class, 'deleteAllQuestions'])->name('editmocksecondquestion.delete-all');

// Main course 
Route::get('/main-courses', [MainCourseController::class, 'index'])->name('main.course.index');
Route::get('/main-courses/search', [MainCourseController::class, 'search'])->name('main.course.search');
Route::post('/main-courses/rate', [MainCourseController::class, 'rate'])->name('main.course.rate');

// Course Details 
Route::get('/course-details/{slug}', [CourseDetailsController::class, 'show'])->name('course.details');
Route::get('/api/course-details/{slug}', [CourseDetailsController::class, 'apiShow'])->name('api.course.details');

// Assign Course
Route::get('/assign-course', [AssignCourseController::class, 'index'])->name('assign-course.index');
Route::get('/assign-course/search-learners', [AssignCourseController::class, 'searchLearners'])->name('assign-course.search-learners');
Route::get('/assign-course/search-courses', [AssignCourseController::class, 'searchCourses'])->name('assign-course.search-courses');
Route::post('/assign-course', [AssignCourseController::class, 'store'])->name('assign-course.store');



Route::post('/learner/check-emails', [LearnerAssignController::class, 'checkEmail'])->name('learner.check-emails');
Route::post('/learner/create', [LearnerAssignController::class, 'create'])->name('learner.create');

// Bookings
Route::get('/bookings', [BookingsController::class, 'index'])->name('bookings.index');
Route::get('/bookings/api', [BookingsController::class, 'apiIndex'])->name('bookings.api');
Route::get('/bookings/months', [BookingsController::class, 'getMonths'])->name('bookings.months');
Route::delete('/bookings/{id}', [BookingsController::class, 'destroy'])->name('bookings.destroy');

// Activity Log
Route::get('/activities', fn() => view('lms.activity'))->name('activities.index');
Route::get('/activities/get', [ActivityController::class, 'getActivities'])->name('activities.get');
Route::get('/activities/poll', [ActivityController::class, 'pollActivities'])->name('activities.poll');

// Blog Section
Route::get('/create-blog', [CreateBlogController::class, 'create'])->name('blog.create');
Route::post('/create-blog', [CreateBlogController::class, 'store'])->name('blog.store');
Route::post('/check-slug', [CreateBlogController::class, 'checkSlug'])->name('blog.checkSlug');

// Tag Manager
Route::get('/pixel/setup', [HeaderFooterController::class, 'index'])->name('pixel.index');
Route::get('/pixel/{id}', [HeaderFooterController::class, 'show'])->name('pixel.show');
Route::put('/pixel/{id}', [HeaderFooterController::class, 'update'])->name('pixel.update');


/////////////////////////////////////////////////////////////////////////////////// Learner Panel ///////////////////////////////////////////////////////////// 

Route::get('/learner-page', function() {   
    return view('learner.learnerPage');
});


Route::get('/learner/regi', [LearnerRegistrationController::class, 'showLearnerRegistrationPage'])->name('learner.learnerRegistration');
Route::post('/learner/register', [LearnerRegistrationController::class, 'register'])->name('learner.register');

Route::get('/learner/login', [LearnerLoginController::class, 'showLearnerLoginPage'])->name('learner.learnerLogin');
Route::post('/learner/login', [LearnerLoginController::class, 'login'])->name('learner.login');
Route::post('/learner/check-email', [LearnerLoginController::class, 'checkEmail'])->name('learner.checkEmail');
Route::post('/learner/forgot-password', [LearnerLoginController::class, 'forgotPassword'])->name('learner.forgotPassword');
Route::get('/password', [LearnerLoginController::class, 'showResetPasswordPage'])->name('learner.showResetPassword');
Route::post('/learner/reset-password', [LearnerLoginController::class, 'resetPassword'])->name('learner.resetPassword');




Route::post('/learner/cart', [LearnerCartController::class, 'showCart']);


Route::middleware(['auth:learner'])->group(function () {
    // Web route to render learner courses view
    Route::get('/learner/courses', function () {
        return view('learner.learnerCourse');
    })->name('learner.courses');

    // Web route to render continue course view with unique_id
    Route::get('/learner/course/{unique_id}', function ($unique_id) {
        return view('learner.continueCourse', ['unique_id' => $unique_id]);
    })->name('learner.continueCourse');

    // Route::get('/learner/final-mock', function () {
    // return view('learner.finalMock');});

     Route::get('/learner/final-mock', function () {
        return view('learner.finalMock');
    })->name('learner.finalMock');

    // New: “start final mock” POST (no /api in URL)
    Route::post('/learner/final-mock/start', [FinalMockController::class, 'start'])
        ->name('learner.finalMock.start');

    Route::get('/learner/course/{unique_id}', [LearnerCourseController::class, 'showContinueCourse'])
        ->name('learner.continueCourse');

    // keep your JSON API for AJAX inside the page if you want
    Route::post('/api/final-mock/questions', [FinalMockController::class, 'fetchFinalMockQuestions']);

    // API routes
    Route::get('/api/learner/courses', [LearnerCourseController::class, 'fetchCourses']);
    Route::get('/api/learner/course/{unique_id}', [LearnerCourseController::class, 'getCourseDetails']);
    Route::post('/api/learner/course/{courseId}/progress/module', [LearnerCourseController::class, 'updateModuleProgress']);
    Route::post('/api/learner/course/{courseId}/progress/quiz', [LearnerCourseController::class, 'updateQuizProgress']);
    Route::delete('/api/learner/course-progress/{unique_id}', [LearnerCourseController::class, 'deleteCourseProgress']);

    Route::get('/learner/logout', [LearnerLoginController::class, 'logout'])->name('learner.logout');
    // Route::get('/learner/page', [LearnerLoginController::class, 'showLearnerPage'])->name('learner.page');
    Route::get('/learner/profile', [ViewProfileController::class, 'show'])->name('learner.profile.show');
    Route::put('/learner/profile', [ViewProfileController::class, 'updateProfile'])->name('learner.profile.update');
    Route::put('/learner/password', [ViewProfileController::class, 'updatePassword'])->name('learner.password.update');
});
Route::get('/learner/page', [LearnerLoginController::class, 'showLearnerPage'])->name('learner.page');
// Payment Form
Route::get('/cart', function () {
    return view('course.cart');
})->name('cart');

// Route::get('/mail', function () {
//     return view('emails.forgetPasswordMail');
// })->name('cart');

Route::get('/learner/cart', [LearnerCartController::class, 'showCartFromLogin'])->name('learner.cart');

// Payment Form
Route::get('/payment', [PaymentFormController::class, 'showPayment'])->name('learner.payment');
Route::post('/payment/process', [PaymentFormController::class, 'processPayment'])->name('learner.payment.process');

// Paypal 
Route::get('/paypal/create', [PaypalController::class, 'createTransaction'])->name('paypal.create');
Route::get('/paypal/success', [PaypalController::class, 'successTransaction'])->name('paypal.success');
Route::get('/paypal/cancel', [PaypalController::class, 'cancelTransaction'])->name('paypal.cancel');
Route::get('/paypal/error', [PaypalController::class, 'errorTransaction'])->name('paypal.error');

//Stripe
Route::get('/stripe/payment', [StripeController::class, 'payment'])->name('stripe.payment');


Route::get('/checkout/success', function () {
    return view('stripe.success');
})->name('checkout.success');

Route::get('/checkout/cancel', function () {
    return view('stripe.cancel');
})->name('checkout.cancel');

Route::get('/test-403', fn() => abort(403));
Route::get('/test-404', fn() => abort(404));
Route::get('/test-500', fn() => abort(500));
