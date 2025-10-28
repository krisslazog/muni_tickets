<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermissionController extends Controller
{
    // Mostrar listado de permisos
    public function index(Request $request)
    {
        $permissions = Permission::paginate(10);

        return Inertia::render('Admin/Permission/Index', [
            'permissions' => $permissions,
        ]);
    }

    // Mostrar formulario para crear un permiso
    public function create()
    {
        return Inertia::render('Admin/Permission/Create');
    }

    // Crear nuevo permiso
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
        return redirect()->route('admin.permissions.index')
                         ->with('success', 'Permiso creado correctamente.');
    }

    // Mostrar formulario para editar un permiso
    public function edit($id)
    {
        // Buscar permiso por id
        $permission = Permission::findOrFail($id);
        // Retornar vista con el permiso
        return Inertia::render('Admin/Permission/Edit', [
            'permission' => $permission,
        ]);
    }

    // Actualizar permiso
    public function update(Request $request, $id)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        // Buscar permiso por id
        $permission = Permission::findOrFail($id);

        // Actualizar permiso
        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de permisos después de actualizar
        return redirect()->route('admin.permissions.index')
                         ->with('success', 'Permiso actualizado correctamente.');
    }
}
