<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\TktCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    
    // Mostrar listado de categorias
    public function index(Request $request)
    {
        $categories = TktCategory::with('createdBy', 'updatedBy')
            ->paginate(10); // O el número que prefieras

        return Inertia::render('Tickets/Category/Index',
            [
                'categories' => $categories,
            ]);
    }
    
     // Mostrar formulario para crear una nueva categoria
    public function create()
    {
        return Inertia::render('Tickets/Category/Create');
    }

    //Crear nueva categoria
     public function store(Request $request)
    {
        // Validar los datos que vienen del formulario
            $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:tkt_categories', 
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);
        // Crear la categoría en la base de datos usando el modelo Category
        TktCategory::create($validatedData);
        
        return redirect()->route('tickets.category.index')
                         ->with('success', 'Categoría creada correctamente.');
    }

    public function edit($id)
    {
        //Buscar categoria por id
        $category = TktCategory::findOrFail($id);
        //Retornar vista con la categoria
        return Inertia::render('Tickets/Category/Edit', [
            'category' => $category,
        ]);
    }
    public function update(Request $request, $id)
    {
        // Buscar categoria primero
        $category = TktCategory::findOrFail($id);

        // 6. CORREGIDO: Validación de 'update'
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Le decimos que la regla 'unique' ignore el ID actual
                Rule::unique('tkt_categories')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'status' => 'required|boolean', // 7. CORREGIDO: Lógica de status
        ]);

        // 8. MEJORADO: Actualización simple
        // El Trait 'Auditable' se encarga de 'updated_by' automáticamente
        $category->update($validatedData);
        
        return redirect()->route('tickets.category.index')
                         ->with('success', 'Categoría actualizada correctamente.');
    }
}
