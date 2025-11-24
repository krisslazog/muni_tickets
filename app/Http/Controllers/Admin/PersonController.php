<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PersonStoreRequest;
use App\Http\Requests\Admin\PersonUpdateRequest;
use App\Models\Person;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Person::query();
        
        // Búsqueda por nombre, apellido o documento
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name_paternal', 'like', "%{$search}%")
                  ->orWhere('last_name_maternal', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtro por estado
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        $persons = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return Inertia::render('Admin/Person/Index', [
            'persons' => $persons,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Person/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonStoreRequest $request)
    {
        Person::create($request->validated());
        
        return redirect()->route('admin.person.index')
                        ->with('success', 'Persona creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        return Inertia::render('Admin/Person/Show', [
            'person' => $person,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        return Inertia::render('Admin/Person/Edit', [
            'person' => $person,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonUpdateRequest $request, Person $person)
    {
        $person->update($request->validated());
        
        return redirect()->route('admin.person.index')
                        ->with('success', 'Persona actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        try {
            $person->delete();
            
            return redirect()->route('admin.person.index')
                            ->with('success', 'Persona eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.person.index')
                            ->with('error', 'No se pudo eliminar la persona. Verifique que no tenga datos relacionados.');
        }
    }
}