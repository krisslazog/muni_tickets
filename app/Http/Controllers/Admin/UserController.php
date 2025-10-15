<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Person;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\StoreUserWithPersonRequest;

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
                'success' => true,
                'message' => 'No se encontró ninguna persona con ese documento.',
                'data' => null
            ]);
        }
    }


    public function storeWithPerson(StoreUserWithPersonRequest $request)
    {
        // ✅ Los datos ya vienen validados
        $validatedData = $request->validated();

        DB::beginTransaction();
        // 1. Crear o actualizar persona
        if ($validatedData['id']) {
            // Persona existente - verificar que no tenga usuario
            $person = Person::where('id', $validatedData['person_id'])
                ->whereDoesntHave('user')
                ->first();

            if (!$person) {
                return back()->withErrors([
                    'person_id' => 'Esta persona ya tiene un usuario asociado o no existe.'
                ]);
            }

            // Actualizar datos de la persona existente
            $person->update([
                'first_name' => $validatedData['first_name'],
                'last_name_paternal' => $validatedData['last_name_paternal'],
                'last_name_maternal' => $validatedData['last_name_maternal'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'],
                'birth_date' => $validatedData['birth_date'],
                'gender' => $validatedData['gender'],
            ]);

        } else {
                // Crear nueva persona
                $person = Person::create([
                    'document_type' => $validatedData['document_type'],
                    'document_number' => $validatedData['document_number'],
                    'first_name' => $validatedData['first_name'],
                    'last_name_paternal' => $validatedData['last_name_paternal'],
                    'last_name_maternal' => $validatedData['last_name_maternal'],
                    'email' => $validatedData['email'],
                    'phone' => $validatedData['phone'],
                    'address' => $validatedData['address'],
                    'birth_date' => $validatedData['birth_date'],
                    'gender' => $validatedData['gender'],
                ]);
        }
            // 2. Crear usuario
            $user = User::create([
                'name' => '',
                'email' => $validatedData['email'] ?? $person->email,
                'password' => Hash::make($validatedData['password']),
                'person_id' => $person->id,
                'email_verified_at' => now(),
            ]);

        DB::commit();
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
