<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PriorityController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Grupo de rutas para el módulo de tickets
Route::prefix('admin')->name('admin.')->group(function () {

// Rutas para la gestión de Categorias

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

// Rutas para la gestión de Prioridades

        // Listar todos las prioridades
        Route::get('priority', [PriorityController::class, 'index'])
        ->name('priority.index');
        // Formulario para crear una nueva prioridad
        Route::get('priority/create', [PriorityController::class, 'create'])
        ->name('priority.create');
        // Guardar una nueva prioridad
        Route::post('priority', [PriorityController::class, 'store'])
        ->name('priority.store');
        // Formulario para editar una prioridad existente
        Route::get('priority/{id}/edit', [PriorityController::class, 'edit'])
        ->name('priority.edit');
        // Actualizar una prioridad existente
        Route::put('priority/{id}', [PriorityController::class, 'update'])
        ->name('priority.update');
        //Eliminar Prioridad
        Route::delete('priority/{id}', [PriorityController::class, 'destroy'])
        ->name('priority.destroy');
});

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
