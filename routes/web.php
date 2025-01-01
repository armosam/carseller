<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('about', 'about')->name('about');

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::get('/password-reset', 'passwordReset')->name('password.reset');
    Route::post('/password-reset', 'storePasswordReset');
    Route::get('/set-password', 'setPassword')->name('password.set');
    Route::post('/set-password', 'storePassword');
})->middleware('guest');

Route::controller(SessionController::class)->prefix('auth')->group(function () {
    Route::get('/signup', 'signup')->name('signup');
    Route::post('/signup', 'registration');
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'authentication');
    Route::post('/logout', 'logout')->name('logout');
})->middleware('guest');

Route::get('/car/search', [CarController::class, 'search'])->name('car.search');
Route::get('/car/watchlist', [CarController::class, 'watchList'])->name('car.watchlist');
Route::resource('car', CarController::class);
