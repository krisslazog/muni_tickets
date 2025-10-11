<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tkt_category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    
    // Mostrar listado de categorias
    public function index(Request $request)
    {
        $categories = Tkt_category::all();

        return Inertia::render('Admin/Category/Index',
            [
                'categories' => $categories,
            ]);
    }
    
     // Mostrar formulario para crear una nueva categoria
    public function create()
    {
        return Inertia::render('Admin/Category/Create');
    }

    //Crear nueva categoria
     public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);
        // Crear la categoría en la base de datos usando el modelo Category
        Tkt_category::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de categorías después de crearla
        return redirect()->route('admin.category.index')
                         ->with('success', 'Categoría creada correctamente.');
    }

    public function edit($id)
    {
        //Buscar categoria por id
        $category = Tkt_category::findOrFail($id);
        //Retornar vista con la categoria
        return Inertia::render('Admin/Category/Edit', [
            'category' => $category,
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
        $category = Tkt_category::findOrFail($id);

        //Actualizar categoria
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);
        // Redirigir al listado de categorías después de actualizar
        return redirect()->route('admin.category.index')
                         ->with('success', 'Categoría actualizada correctamente.');
    }

}
