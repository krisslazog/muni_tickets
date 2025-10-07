<?php

use App\Http\Controllers\Tickets\CategoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('category', [CategoryController::class, 'index'])
        ->name('category.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
