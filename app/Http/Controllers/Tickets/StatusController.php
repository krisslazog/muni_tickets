<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\TktStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatusController extends Controller
{
    
    // Mostrar listado de estados
    public function index(Request $request)
    {
        $status = TktStatus::all();

        return Inertia::render('Tickets/Status/Index',
            [
                'statuses' => $status,
            ]);
    }
    
     // Mostrar formulario para crear un nuevo estado
    public function create()
    {
        return Inertia::render('Tickets/Status/Create');
    }

    //Crear nuevo estado
     public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        // Crear la estado en la base de datos usando el modelo de estado
        TktStatus::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de estado después de crearla
        return redirect()->route('tickets.status.index')
                         ->with('success', 'Estado creado correctamente.');
    }

    public function edit($id)
    {
        //Buscar estado por id
        $status = TktStatus::findOrFail($id);
        //Retornar vista con el estado
        return Inertia::render('Tickets/Status/Edit', [
            'status' => $status,
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
        //Buscar estado por id
        $status = TktStatus::findOrFail($id);

        //Actualizar estado
        $status->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de estados después de actualizar
        return redirect()->route('tickets.status.index')
                         ->with('success', 'Estado actualizado correctamente.');
    }

}
