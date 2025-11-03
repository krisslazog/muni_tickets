<?php

use App\Http\Controllers\Tickets\CategoryController;
use App\Http\Controllers\Tickets\PriorityController;
use App\Http\Controllers\Tickets\StatusController;
use App\Http\Controllers\Tickets\TicketsController;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Grupo de rutas para el módulo de tickets
Route::prefix('tickets')->name('tickets.')
//->middleware(['auth', 'verified','role:invitado'])
->group(function () {

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

// Rutas para la gestión de Estados

        // Listar todos los estados
        Route::get('status', [StatusController::class, 'index'])
        ->name('status.index');
        // Formulario para crear una nueva prioridad
        Route::get('status/create', [StatusController::class, 'create'])
        ->name('status.create');
        // Guardar una nueva prioridad
        Route::post('status', [StatusController::class, 'store'])
        ->name('status.store');
        // Formulario para editar una prioridad existente
        Route::get('status/{id}/edit', [StatusController::class, 'edit'])
        ->name('status.edit');
        // Actualizar una prioridad existente
        Route::put('status/{id}', [StatusController::class, 'update'])
        ->name('status.update');
        //Eliminar Prioridad
        Route::delete('status/{id}', [StatusController::class, 'destroy'])
        ->name('status.destroy');

//Nuevo ticket
        // Listar todos los tickets
        Route::get('tickets', [TicketsController::class, 'index'])
        ->name('tickets.index');
        // Formulario para crear un nuevo ticket
        Route::get('tickets/create', [TicketsController::class, 'create'])
        ->name('tickets.create');
        // Guardar un nuevo ticket
        Route::post('tickets', [TicketsController::class, 'store'])
        ->name('tickets.store');
        // Formulario para editar un ticket existente
        Route::get('tickets/{id}/edit', [TicketsController::class, 'edit'])
        ->name('tickets.edit');
        // Actualizar un ticket existente
        Route::put('tickets/{id}', [TicketsController::class, 'update'])
        ->name('tickets.update');
        //Eliminar Ticket
        Route::delete('tickets/{id}', [TicketsController::class, 'destroy'])
        ->name('tickets.destroy');
});

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
