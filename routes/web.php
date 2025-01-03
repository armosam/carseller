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

Route::controller(CarController::class)->prefix('car')->group(function () {
    Route::get('/', 'index')->name('car.index')->middleware('auth');
    Route::post('/', 'store')->name('car.store')->middleware('auth');
    Route::get('/create', 'create')->name('car.create')->middleware('auth');
    Route::get('/search', 'search')->name('car.search');
    Route::get('/watchlist', 'watchList')->name('car.watchlist')->middleware('auth');
    Route::get('/{car}', 'show')->name('car.show');
    Route::get('/{car}/edit', 'edit')->name('car.edit')->middleware('auth')
        ->can('update','car');
    Route::addRoute(['PUT','PATCH'],'{car}', 'update')->name('car.update')->middleware('auth')
        ->can('update', 'car');
    Route::delete('/{car}', 'destroy')->name('car.destroy')->middleware('auth')
        ->can('delete', 'car');
});
