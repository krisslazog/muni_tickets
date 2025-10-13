<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Person;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with(['person', 'roles'])
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            ->when($request->role, function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->when($request->dni, function ($query, $dni) {
                $query->byDni($dni);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
            $roles = Role::all();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role', 'dni'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $persons = Person::whereDoesntHave('user')->get();

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
            'persons' => $persons
        ]);
    }

    /// Buscar persona por tipo y número de documento (AJAX)
    public function searchByDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'document_number' => 'required|string|max:20|min:4',
        ]);

        $person = Person::where('document_type', $request->document_type)
            ->where('document_number', $request->document_number)
            ->whereDoesntHave('user')
            ->first();

        if ($person) {
            return response()->json([
                'success' => true,
                'message' => 'Persona encontrada.',
                'data' => [
                    'person' => $person
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ninguna persona con ese documento.',
                'data' => null
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
