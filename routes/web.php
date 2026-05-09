<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\SponsorAuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketPaymentController;
use App\Http\Controllers\DashboardController;
use App\Models\Setting;
use App\Services\HomepageLayoutService;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------------------------
// Public
// -------------------------------------------------------------------------

Route::get('/', function () {
    $settings = Setting::whereIn('group', ['general', 'homepage'])
        ->pluck('value', 'key')
        ->all();
    $homeLayout = app(HomepageLayoutService::class)->read();

    return view('welcome', compact('settings', 'homeLayout'));
})->name('home');

Route::get('/raffle', [RaffleController::class, 'index'])->name('raffle.index');

Route::get('/raffle/{raffle:slug}/tickets',          [TicketController::class, 'index'])->name('ticket.index');
Route::get('/raffle/{raffle:slug}/tickets/{ticket}', [TicketController::class, 'show'])->name('ticket.show');

// -------------------------------------------------------------------------
// Admin auth (guests only)
// -------------------------------------------------------------------------

Route::middleware('guest')->group(function () {
    Route::get('/admin/register',  [AdminAuthController::class, 'showRegister'])->name('admin.register');
    Route::post('/admin/register', [AdminAuthController::class, 'register'])->name('admin.register.store');
    Route::get('/admin/login',     [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login',    [AdminAuthController::class, 'login'])->name('admin.login.store');
});

Route::post('/admin/verify', [AdminAuthController::class, 'verifyOtp'])->name('admin.otp.verify');
Route::post('/admin/resend', [AdminAuthController::class, 'resendOtp'])->name('admin.otp.resend');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// -------------------------------------------------------------------------
// Admin (authenticated)
// -------------------------------------------------------------------------

Route::middleware('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',                  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',                [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',               [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/send-delete-otp', [ProfileController::class, 'sendDeleteOtp'])->name('profile.delete.otp');

    // Raffle management — create MUST come before {raffle}
    Route::get('/raffle/create',        [RaffleController::class, 'create'])->name('raffle.create');
    Route::post('/raffle',              [RaffleController::class, 'store'])->name('raffle.store');
    Route::get('/raffle/{raffle}/edit', [RaffleController::class, 'edit'])->name('raffle.edit');
    Route::put('/raffle/{raffle}',      [RaffleController::class, 'update'])->name('raffle.update');
    Route::delete('/raffle/{raffle}',   [RaffleController::class, 'destroy'])->name('raffle.destroy');

    // Payment management
    Route::get('/admin/payments',                    [AdminPaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/admin/payments/{payment}',          [AdminPaymentController::class, 'show'])->name('admin.payments.show');
    Route::post('/admin/payments/{payment}/confirm', [AdminPaymentController::class, 'confirm'])->name('admin.payments.confirm');
    Route::post('/admin/payments/{payment}/reject',  [AdminPaymentController::class, 'reject'])->name('admin.payments.reject');

    // Settings
    Route::get('/admin/settings',  [AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

    Route::post('/admin/payments/confirm-all', [AdminPaymentController::class, 'confirmAll'])->name('admin.payments.confirm-all');
    Route::post('/admin/payments/reject-all',  [AdminPaymentController::class, 'rejectAll'])->name('admin.payments.reject-all');
});

// MUST come after /raffle/create
Route::get('/raffle/{raffle}', [RaffleController::class, 'show'])->name('raffle.show');

// -------------------------------------------------------------------------
// Sponsor auth (guests only)
// -------------------------------------------------------------------------

Route::middleware('guest:sponsor')->group(function () {
    Route::get('/sponsor/register',  [SponsorAuthController::class, 'showRegister'])->name('sponsor.register');
    Route::post('/sponsor/register', [SponsorAuthController::class, 'register'])->name('sponsor.register.store');
    Route::get('/sponsor/login',     [SponsorAuthController::class, 'showLogin'])->name('sponsor.login');
    Route::post('/sponsor/login',    [SponsorAuthController::class, 'login'])->name('sponsor.login.store');
});

Route::post('/sponsor/verify', [SponsorAuthController::class, 'verifyOtp'])->name('sponsor.otp.verify');
Route::post('/sponsor/resend', [SponsorAuthController::class, 'resendOtp'])->name('sponsor.otp.resend');
Route::post('/sponsor/logout', [SponsorAuthController::class, 'logout'])->name('sponsor.logout');

// -------------------------------------------------------------------------
// Sponsor (authenticated)
// -------------------------------------------------------------------------

Route::middleware('sponsor')->group(function () {
    Route::post('/raffle/{raffle}/tickets/{ticket}/reserve', [TicketPaymentController::class, 'reserve'])->name('ticket.reserve');
    Route::get('/raffle/{raffle}/payment',                   [TicketPaymentController::class, 'showPayment'])->name('ticket.payment');
    Route::post('/raffle/{raffle}/payment',                  [TicketPaymentController::class, 'submitProof'])->name('ticket.proof');
    Route::patch('/raffle/{raffle}/tickets/{ticket}/cancel', [TicketPaymentController::class, 'cancelReservation'])->name('ticket.cancel');
});
