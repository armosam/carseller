<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::get('/signup', 'signup')->name('signup');
    Route::get('/login', 'login')->name('login');
    Route::get('/password-reset', 'passwordReset')->name('password.reset');
});

//Route::get('/signup', [AuthController::class, 'create'])->name('signup');
//Route::get('/password-reset', [AuthController::class, 'create'])->name('password-reset');
//Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::get('/car/search', [CarController::class, 'search'])->name('car.search');
Route::get('/car/watchlist', [CarController::class, 'watchList'])->name('car.watchlist');
Route::resource('car', CarController::class);
