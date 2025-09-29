<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('about', 'about')->name('about');

Route::middleware(['EnsureTodayMonday'])->group(function () {
    Route::get('/monday-sale', [HomeController::class, 'mondaySale'])->name('home.monday');
});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::get('/password-reset-request', 'passwordResetRequest')->name('password.resetRequest');
    Route::post('/store-password-reset', 'storePasswordReset')->name('password.storeResetRequest');
    Route::get('/password-reset/{token}', 'passwordReset')->name('password.reset');
    Route::post('/store-password', 'storePassword')->name('password.store');
})->middleware('guest');

Route::controller(SessionController::class)->prefix('auth')->group(function () {
    Route::post('/logout', 'logout')->name('logout');

    Route::middleware(['guest'])->group(function () {
        Route::get('/signup', 'signup')->name('signup');
        Route::post('/signup', 'registration');
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authentication');
    });
});

Route::controller(SocialiteController::class)->group(function () {
    Route::get('/login/oauth/{provider}', 'redirectToProvider')->name('login.oauth');
    Route::get('/callback/oauth/{provider}', 'handleProviderCallback');
});


Route::controller(EmailVerificationController::class)->prefix('email')->group(function () {
    Route::get('/verify/{id}/{hash}', 'verify')->middleware(['signed'])->name('verification.verify');
    Route::get('/verify', 'notice')->name('verification.notice')->middleware(['auth']);
    Route::post('/verification-notification', 'send')->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});

Route::controller(CarController::class)->prefix('car')->group(function () {
    Route::get('/search', 'search')->name('car.search');

    Route::middleware(['auth'])->group(function () {
        Route::middleware(['verified'])->group(function () {
            Route::get('/', 'index')->name('car.index');
            Route::post('/', 'store')->name('car.store');
            Route::get('/create', 'create')->name('car.create');
            Route::get('/watchlist', 'watchList')->name('car.watchlist');
            Route::get('/{car}', 'show')->name('car.show');
            Route::get('/{car}/edit', 'edit')->name('car.edit')->can('update','car');
            Route::addRoute(['PUT','PATCH'],'{car}', 'update')->name('car.update')->can('update', 'car');
            Route::delete('/{car}', 'destroy')->name('car.destroy')->can('delete', 'car');
            Route::get('/{car}/images', 'carImages')->name('car.images')->can('update','car');
            Route::post('/{car}/images', 'addImages')->name('car.addImages')->can('update','car');
            Route::addRoute(['PUT','PATCH'],'/{car}/images', 'updateImages')->name('car.updateImages')->can('update','car');
        });
    });
});


Route::prefix('admin')->group(function () {
    Route::controller(CarController::class)->prefix('car')->group(function () {
        Route::get('/', 'index')->name('armin.car.index');
        Route::post('/', 'store')->name('admin.car.store');
        Route::get('/create', 'create')->name('admin.car.create');
        Route::get('/search', 'search')->name('admin.car.search');
        Route::get('/{car}', 'show')->name('admin.car.show');
        Route::get('/{car}/edit', 'edit')->name('admin.car.edit')->can('update','car');
        Route::addRoute(['PUT','PATCH'],'{car}', 'update')->name('admin.car.update')->can('update', 'car');
        Route::delete('/{car}', 'destroy')->name('admin.car.destroy')->can('delete', 'car');
    });
});


//Route::fallback(ErrorController::class);
