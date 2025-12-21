<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Welcome/Landing page for guests
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Authenticated dashboard (only for logged-in users)
Route::middleware('auth')->get('/home', [HomepageController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.email');
Route::get('/verify-reset-code', [AuthController::class, 'showVerifyCode'])->name('password.verify');
Route::post('/verify-reset-code', [AuthController::class, 'verifyCode'])->name('password.verify.post');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset.post');

// Profile routes (only for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Language switching route
Route::post('/language/switch', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale', 'en');
    
    if (in_array($locale, ['en', 'vi'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    
    return redirect()->back();
})->name('language.switch');