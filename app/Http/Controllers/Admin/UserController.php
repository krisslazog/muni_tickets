<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Person;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
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
            ->with('user')
            ->first();

        if ($person) {
            return response()->json([
                'success' => true,
                'message' => 'Persona encontrada.',
                'data' => $person
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
        try {
            // 1. Crear o actualizar persona
            if ($validatedData['id']) {
                // Persona existente - verificar que no tenga usuario
                $person = Person::where('id', $validatedData['id'])
                    ->with('user')
                    ->first();
                if (!$person) {
                    return response()->json(['error' => 'Esta persona ya tiene un usuario asociado o no existe.'], 422);
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

            if (empty($validatedData['user_id'])) {
                // Crear usuario: password requerido o controla en Request
                $userData = [
                    'name' => '',
                    'email' => $validatedData['email'] ?? $person->email,
                    'person_id' => $person->id,
                    'email_verified_at' => now(),
                ];

                if (!empty($validatedData['password'])) {
                    $userData['password'] = Hash::make($validatedData['password']);
                }

                $user = User::create($userData);
            } else {
                $user = User::find($validatedData['user_id']);
                if (!$user) {
                    DB::rollBack();
                    return response()->json(['error' => 'Usuario no encontrado.'], 422);
                }

                // Actualiza campos no sensibles
                $user->email = $validatedData['email'] ?? $person->email;
                $user->person_id = $person->id;

                // Actualiza password solo si viene no vacío
                if (!empty($validatedData['password'])) {
                    $user->password = Hash::make($validatedData['password']);
                }

                $user->save();
            }
            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Usuario guardado.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('storeWithPerson error: '.$e->getMessage());
            return back()->withErrors(['error' => 'Ocurrió un error al guardar.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }
        $person = Person::where('id', $user->person_id)
            ->with('user')
            ->first();

        if (!$person) {
            abort(404);
        }
        return Inertia::render('Admin/Users/Edit', [
            'person' => $person
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function assignPermission(Request $request, string $id)
    {

        $user = User::with(['roles', 'permissions'])->find($id);

        $roles = Role::all();
        $permissions = Permission::all();
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }


        return Inertia::render('Admin/Users/Permissions', [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updatePermission(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'roles' => ['array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        dd($request->all());
    }
}
