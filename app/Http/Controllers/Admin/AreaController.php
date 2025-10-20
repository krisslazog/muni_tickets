<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AreaController extends Controller
{
    
    // Mostrar listado de areas
    public function index(Request $request)
    {
        $areas = Area::all();

        return Inertia::render('Admin/Area/Index',
            [
                'areas' => $areas ,
            ]);
    }
    
    // Mostrar formulario para crear una area
    public function create()
    {
        return Inertia::render('Admin/Area/Create');
    }
    
    //Crear nueva area
     public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        // Crear las areas
        Area::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de categorías después de crearla
        return redirect()->route('admin.area.index')
                         ->with('success', 'Prioridad creada correctamente.');
    }

}
