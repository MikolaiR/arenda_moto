<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RenterController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('home') : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', LogoutController::class)->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/home', HomeController::class)->name('home');

    Route::resource('motorcycles', MotorcycleController::class)->except(['show']);
    Route::patch('motorcycles/{motorcycle}/restore', [MotorcycleController::class, 'restore'])->name('motorcycles.restore');

    Route::resource('renters', RenterController::class)->except(['show']);
    Route::patch('renters/{renter}/restore', [RenterController::class, 'restore'])->name('renters.restore');

    Route::post('motorcycles/{motorcycle}/rentals', [RentalController::class, 'store'])->name('rentals.store');
    Route::patch('rentals/{rental}', [RentalController::class, 'update'])->name('rentals.update');
    Route::delete('rentals/{rental}', [RentalController::class, 'destroy'])->name('rentals.destroy');
});
