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
        $search = $request->get('search', '');
        $group = $request->get('group', '');
        $perPage = (int) $request->get('per_page', 10);

        // Desactivar el Global Scope para ver TODOS los permisos (activos e inactivos)
        $query = Permission::withoutGlobalScope('active');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($group)) {
            $query->where('group', $group);
        }

        $permissions = $query->paginate($perPage)->appends($request->only(['search', 'group', 'per_page']));

        // Obtener lista de grupos únicos (no nulos) para el selector
        $groups = Permission::withoutGlobalScope('active')
            ->whereNotNull('group')
            ->pluck('group')
            ->unique()
            ->values();

        return Inertia::render('Admin/Permission/Index', [
            'permissions' => $permissions,
            'filters' => $request->only(['search', 'group']),
            'groups' => $groups,
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
            'group' => 'required|string|max:255',
            'status' => 'boolean',
        ]);
        // Crear el área
        Area::create([
            'name' => $request->name,
            'description' => $request->description,
            'group' => $request->group,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de áreas después de crearla
        return redirect()->route('admin.permissions.index')
                         ->with('success', 'Permiso creado correctamente.');
    }

    // Mostrar formulario para editar un permiso
    public function edit($id)
    {
        // Desactivar el Global Scope para ver TODOS los permisos (activos e inactivos)
        $query = Permission::withoutGlobalScope('active');
        // Buscar permiso por id
        $permission = $query->findOrFail($id);
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
            'group' => 'required|string|max:255',
            'status' => 'boolean',
        ]);
        // Buscar permiso por id
        $query = Permission::withoutGlobalScope('active');
        $permission = $query->findOrFail($id);

        // Actualizar permiso
        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'group' => $request->group,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de permisos después de actualizar
        return redirect()->route('admin.permissions.index')
                         ->with('success', 'Permiso actualizado correctamente.');
    }
}
