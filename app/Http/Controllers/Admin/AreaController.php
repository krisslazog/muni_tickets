<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AreaController extends Controller
{
    // Mostrar listado de áreas
    public function index(Request $request)
    {
        $areas = Area::all();

        return Inertia::render('Admin/Area/Index', [
            'areas' => $areas,
        ]);
    }
    
    // Mostrar formulario para crear un área
    public function create()
    {
        return Inertia::render('Admin/Area/Create');
    }
    
    // Crear nueva área
    public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        // Crear el área
        Area::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de áreas después de crearla
        return redirect()->route('admin.areas.index')
                         ->with('success', 'Área creada correctamente.');
    }

    // Mostrar formulario para editar un área
    public function edit($id)
    {
        // Buscar área por id
        $area = Area::findOrFail($id);
        // Retornar vista con el área
        return Inertia::render('Admin/Area/Edit', [
            'area' => $area,
        ]);
    }

    // Actualizar área
    public function update(Request $request, $id)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        // Buscar área por id
        $area = Area::findOrFail($id);

        // Actualizar área
        $area->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de áreas después de actualizar
        return redirect()->route('admin.areas.index')
                         ->with('success', 'Área actualizada correctamente.');
    }
}
