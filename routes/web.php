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


// Grupo de rutas para el módulo de tickets
Route::prefix('tickets')->name('tickets.')->group(function () {

         // Listar todos las categorias
        Route::get('category', [CategoryController::class, 'index'])
        ->name('category.index');
        // Formulario para crear una nueva categoria
        Route::get('category/create', [CategoryController::class, 'create'])
        ->name('category.create');
        // Guardar una nueva categoria
        Route::post('category', [CategoryController::class, 'store'])
        ->name('category.store');
        // Formulario para editar una categoria existente
        Route::get('category/{id}/edit', [CategoryController::class, 'edit'])
        ->name('category.edit');
        // Actualizar una categoria existente
        Route::put('category/{id}', [CategoryController::class, 'update'])
        ->name('category.update');
        //Eliminar Categoria
        Route::delete('category/{id}', [CategoryController::class, 'destroy'])
        ->name('category.destroy');
});

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
