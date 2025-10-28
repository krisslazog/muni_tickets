<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    // Mostrar listado de roles
    public function index(Request $request)
    {
        $roles = Role::all();

        return Inertia::render('Admin/Role/Index', [
            'roles' => $roles,
        ]);
    }

    // Mostrar formulario para crear un rol
    public function create()
    {
        return Inertia::render('Admin/Role/Create');
    }

    // Crear nuevo rol
    public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'boolean',
        ]);
        // Crear el área
        Area::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de áreas después de crearla
        return redirect()->route('admin.roles.index')
                         ->with('success', 'Rol creado correctamente.');
    }

    // Mostrar formulario para editar un rol
    public function edit($id)
    {
        // Buscar rol por id
        $role = Role::findOrFail($id);
        // Retornar vista con el rol
        return Inertia::render('Admin/Role/Edit', [
            'role' => $role,
        ]);
    }

    // Actualizar rol
    public function update(Request $request, $id)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        // Buscar rol por id
        $role = Role::findOrFail($id);

        // Actualizar rol
        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de roles después de actualizar
        return redirect()->route('admin.roles.index')
                         ->with('success', 'Rol actualizado correctamente.');
    }
}
