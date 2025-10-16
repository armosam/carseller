<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\tests\OrderController;
use App\Http\Controllers\tests\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/about', 'about', ['app_name' => env('APP_NAME')])->name('about');

// Named routes
Route::view('/contact', 'contact')->name('contact');

// http://laravel.test/product/test1111
Route::get('/product/{category?}', function (string $category = '') {
    return "Product for category: $category";
})->whereAlphaNumeric('category')->name('product.category');

// http://laravel.test/en/product/1111/review/good333
Route::get('{lang}/product/{id}/review/{rid}', function (string $lang, string $id, string $rid) {
    return "Product language: $lang  id: $id reviewID: $rid";
})->whereNumber('id')->whereIn('lang', ['en', 'ru'])->whereAlphaNumeric('rid');

// http://laravel.test/order/111/aaaaaaaa
Route::get('/order/{id}/{slag?}', function (string $id, string $slag = '') {
    return "Order id: $id and slag: $slag";
})->where(['id' => '[0-9]+', 'slag' => '[a-z]{0,8}']);

// http://laravel.test/search/1111/2222/333
Route::get('/search/{query}', function ($query) {
    return "Search id: $query";
})->where('query', '.+');

// Redirection
Route::get('/profile', function () {})->name('profile');
Route::get('/active-user', function () {
    return to_route('profile', ['uid' => 123]);
});

// Route groups
Route::prefix('/admin')->group(function () {
    // http://laravel.test/admin
    Route::get('/', function () {
        return view('admin/welcome');
    })->name('home');

    // http://laravel.test/admin/login
    Route::get('/login', function () {
        return view('admin/login');
    })->name('login');
});

// Challenge
Route::get('/sum/{a}/{b}', function (float $a, float $b) {
    return $a + $b;
})->whereNumber(['a', 'b']);

// Controller route
Route::get('car', [\App\Http\Controllers\CarController::class, 'index']);

// Grouping by the controller
Route::controller(CarController::class)->group(function () {
    Route::get('car', 'index')->name('car.list');
    Route::get('car/create', 'create')->name('car.create');
    Route::get('car/view', 'view')->name('car.view');
});

Route::resource('/product', ProductController::class);
Route::apiResource('/order', OrderController::class);

// Fallback routes
Route::fallback(function () {
    return view('404');
});
