<?php

// use App\Http\Controllers\Admin\RoleController;
// use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PersonController as AdminPersonController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Todas las rutas de administración requieren autenticación y rol admin
Route::middleware(['auth', 'verified', 'role:admin|super_admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard administrativo
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

    // ========================================
    // GESTIÓN DE USUARIOS (CREAR DESDE ADMIN)
    // ========================================
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create'); //
        Route::get('/search-by-document', [UserController::class, 'searchByDocument'])->name('search-by-document'); //
        Route::post('/store-with-person', [UserController::class, 'storeWithPerson'])->name('store-with-person'); // ← CREAR USUARIO Y PERSONA
        Route::post('/', [UserController::class, 'store'])->name('store'); // ← GUARDAR USUARIO DESDE ADMIN
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // Gestión de roles del usuario
        Route::post('/{user}/assign-role', [UserController::class, 'assignRole'])
            ->name('assign-role');
        Route::delete('/{user}/remove-role/{role}', [UserController::class, 'removeRole'])
            ->name('remove-role');

        // Gestión de permisos directos del usuario
        Route::post('/{user}/assign-permission', [UserController::class, 'assignPermission'])
            ->name('assign-permission');
        Route::delete('/{user}/remove-permission/{permission}', [UserController::class, 'removePermission'])
            ->name('remove-permission');
    });

    // ========================================
    // GESTIÓN DE ROLES
    // ========================================
    // Route::prefix('roles')->name('roles.')->group(function () {
    //     Route::get('/', [RoleController::class, 'index'])->name('index');
    //     Route::get('/create', [RoleController::class, 'create'])->name('create');
    //     Route::post('/', [RoleController::class, 'store'])->name('store');
    //     Route::get('/{role}', [RoleController::class, 'show'])->name('show');
    //     Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
    //     Route::put('/{role}', [RoleController::class, 'update'])->name('update');
    //     Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');

    //     // Gestión de permisos del rol
    //     Route::post('/{role}/assign-permissions', [RoleController::class, 'assignPermissions'])
    //         ->name('assign-permissions');
    //     Route::delete('/{role}/remove-permission/{permission}', [RoleController::class, 'removePermission'])
    //         ->name('remove-permission');
    // });

    // ========================================
    // GESTIÓN DE PERMISOS
    // ========================================
    // Route::prefix('permissions')->name('permissions.')->group(function () {
    //     Route::get('/', [PermissionController::class, 'index'])->name('index');
    //     Route::get('/create', [PermissionController::class, 'create'])->name('create');
    //     Route::post('/', [PermissionController::class, 'store'])->name('store');
    //     Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
    //     Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
    //     Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
    //     Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    // });

    // ========================================
    // GESTIÓN DE PERSONAS (DESDE ADMIN)
    // ========================================
    // Route::prefix('persons')->name('persons.')->group(function () {
    //     Route::get('/', [AdminPersonController::class, 'index'])->name('index');
    //     Route::get('/create', [AdminPersonController::class, 'create'])->name('create');
    //     Route::post('/', [AdminPersonController::class, 'store'])->name('store');
    //     Route::get('/{person}', [AdminPersonController::class, 'show'])->name('show');
    //     Route::get('/{person}/edit', [AdminPersonController::class, 'edit'])->name('edit');
    //     Route::put('/{person}', [AdminPersonController::class, 'update'])->name('update');
    //     Route::delete('/{person}', [AdminPersonController::class, 'destroy'])->name('destroy');

    //     // Crear usuario para una persona específica
    //     Route::post('/{person}/create-user', [AdminPersonController::class, 'createUser'])
    //         ->name('create-user');
    // });

    // ========================================
    // ESTADÍSTICAS Y REPORTES
    // ========================================
    // Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
    // Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    // Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
});
