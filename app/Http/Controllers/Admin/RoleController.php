<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
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
        $permissions = Permission::withoutGlobalScope('active')
            ->select('id', 'name', 'description', 'group')
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Role/Create', [
            'permissions' => $permissions,
        ]);
    }

    // Crear nuevo rol
    public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:roles',
        'description' => 'nullable|string',
        'status' => 'boolean',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    // Crear el rol
    $role = Role::create([
        'name' => $request->name,
        'description' => $request->description,
        'status' => $request->status ?? true,
    ]);

    // Asignar permisos al rol
    if (!empty($validated['permissions'])) {
        $permissions = Permission::withoutGlobalScope('active')
            ->whereIn('id', $validated['permissions'])
            ->pluck('name');
        $role->givePermissionTo($permissions);
    }
        // Redirigir al listado de áreas después de crearla
        return redirect()->route('admin.roles.index')
                         ->with('success', 'Rol creado correctamente.');
    }

    // Mostrar formulario para editar un rol
    public function edit($id)
    {
        // Buscar rol por id
        $role = Role::findOrFail($id);

        $permissions = Permission::withoutGlobalScope('active')
            ->select('id', 'name', 'description', 'group')
            ->orderBy('group')
            ->orderBy('name')
            ->get();
        // Obtener IDs de permisos asignados al rol
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return Inertia::render('Admin/Role/Edit', [
            'role' => array_merge($role->toArray(), ['permissions' => $rolePermissions]),
            'permissions' => $permissions,
        ]);
    }

    // Actualizar rol
    public function update(Request $request, $id)
    {
        $role = Role::withoutGlobalScope('active')->findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:roles,name,' . $id,
        'description' => 'nullable|string',
        'status' => 'boolean',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    $role->update([
        'name' => $validated['name'],
        'description' => $validated['description'] ?? null,
        'status' => $validated['status'] ?? true,
    ]);

    // Sincronizar permisos (elimina los anteriores y asigna los nuevos)
    if (isset($validated['permissions'])) {
        $permissions = Permission::withoutGlobalScope('active')
            ->whereIn('id', $validated['permissions'])
            ->pluck('name');
        $role->syncPermissions($permissions);
    } else {
        $role->syncPermissions([]); // Elimina todos los permisos si no se envía ninguno
    }

    return redirect()->route('admin.roles.index')
        ->with('success', 'Rol actualizado exitosamente.');
    }
}
