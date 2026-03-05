<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as CsrfMiddleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Admin\ForkliftController as AdminForkliftController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('home');
Route::view('/how-it-works', 'how-it-works')->name('how');
Route::view('/contact', 'contact')->name('contact');

Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

Route::get('/debug/forklifts', function () {
    return \App\Models\Forklift::with('location')
        ->get()
        ->makeHidden(['created_at', 'updated_at'])
        ->toArray();
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Social Authentication (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('oauth.redirect');

    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('oauth.callback');
});

/*
|--------------------------------------------------------------------------
| Auth Only Routes (NOT requiring verified email)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // OTP Verification
    Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.show');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/resend', [OtpController::class, 'send'])->name('otp.send');

    // Email verified success page — only reachable after OTP verified
    Route::get('/email/verified', fn () => view('auth.verify-email-success'))->name('verification.success');
});

/*
|--------------------------------------------------------------------------
| Authenticated + Verified Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'otp.verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::get('/bookings/availability', [BookingController::class, 'availability'])->name('bookings.availability');
    Route::get('/bookings/forklifts', [BookingController::class, 'forklifts'])->name('bookings.forklifts');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/mine', [BookingController::class, 'mine'])->name('bookings.mine');
    Route::get('/bookings/{booking}/thanks', [BookingController::class, 'thanks'])->name('bookings.thanks');
    Route::get('/bookings/{booking}/thankyou', [BookingController::class, 'thankyou'])->name('bookings.thankyou');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout/intent', [CheckoutController::class, 'createIntent'])->name('checkout.intent');
    Route::post('/checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');

    // Payments
    Route::post('/payments/intent', [PaymentController::class, 'intent'])->name('payments.intent');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Bookings Management
        Route::get('/bookings', [AdminController::class, 'dashboard'])->name('bookings.index');
        Route::get('/bookings/{booking}', [AdminController::class, 'viewBooking'])->name('booking-show');
        Route::post('/bookings/{booking}/approve', [AdminController::class, 'approveBooking'])->name('booking.approve');
        Route::patch('/bookings/{booking}/reject', [AdminController::class, 'rejectBooking'])->name('booking.reject');
        Route::patch('/bookings/{booking}/status', [AdminController::class, 'updateStatus'])->name('booking.status');
        Route::patch('/bookings/{booking}/complete', [AdminController::class, 'completeBooking'])->name('booking.complete');
        Route::patch('/bookings/{booking}/cancel', [AdminController::class, 'cancelBooking'])->name('booking.cancel');
        Route::patch('/bookings/{booking}/paid', [BookingController::class, 'markPaid'])->name('booking.markPaid');

        // Forklifts Management
        Route::resource('forklifts', AdminForkliftController::class);
        Route::post('/forklifts/{forklift}/toggle-status', [AdminForkliftController::class, 'toggleStatus'])
            ->name('forklifts.toggle-status');

        // Users Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/{user}', [AdminController::class, 'viewUser'])->name('user.show');
        Route::post('/users/{user}/toggle-role', [AdminController::class, 'toggleUserRole'])->name('user.toggle-role');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        
    });
    Route::post('/admin/bookings/{booking}/mark-paid',
    [AdminController::class, 'markPaid']
)->name('admin.bookings.mark-paid');
});

/*
|--------------------------------------------------------------------------
| Stripe Webhook (No Auth, No CSRF)
|--------------------------------------------------------------------------
*/
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([CsrfMiddleware::class])
    ->middleware('throttle:120,1')
    ->name('payments.webhook');

/*
|--------------------------------------------------------------------------
| Laravel Breeze Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';