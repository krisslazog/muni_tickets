<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tkt_priority;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PriorityController extends Controller
{
    
    // Mostrar listado de prioridades
    public function index(Request $request)
    {
        $priority = Tkt_priority::all();

        return Inertia::render('Admin/Priority/Index',
            [
                'priorities' => $priority ,
            ]);
    }
    
     // Mostrar formulario para crear una nueva prioridad
    public function create()
    {
        return Inertia::render('Admin/Priority/Create');
    }

    //Crear nueva prioridad
     public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        // Crear la categoría en la base de datos usando el modelo prioridad
        Tkt_priority::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de categorías después de crearla
        return redirect()->route('admin.priority.index')
                         ->with('success', 'Prioridad creada correctamente.');
    }

    public function edit($id)
    {
        //Buscar categoria por id
        $priority = Tkt_priority::findOrFail($id);
        //Retornar vista con la categoria
        return Inertia::render('Admin/priority/Edit', [
            'priority' => $priority,
        ]);
    }
    public function update(Request $request,$id)
    {

        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        //Buscar categoria por id
        $priority = Tkt_priority::findOrFail($id);

        //Actualizar categoria
        $priority->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de categorías después de actualizar
        return redirect()->route('admin.priority.index')
                         ->with('success', 'Prioridades actualizada correctamente.');
    }

}
