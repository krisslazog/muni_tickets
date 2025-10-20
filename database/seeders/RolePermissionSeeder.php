<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Person;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos (name y description en español)
        $permisos = [
            ['name' => 'ver_tickets',           'description' => 'Ver tickets'],
            ['name' => 'ver_mis_tickets',       'description' => 'Ver solo mis tickets'],
            ['name' => 'ver_todos_los_tickets', 'description' => 'Ver todos los tickets'],
            ['name' => 'crear_tickets',         'description' => 'Crear tickets'],
            ['name' => 'editar_tickets',        'description' => 'Editar cualquier ticket'],
            ['name' => 'editar_mis_tickets',    'description' => 'Editar solo mis tickets'],
            ['name' => 'eliminar_tickets',      'description' => 'Eliminar tickets'],
            ['name' => 'asignar_tickets',       'description' => 'Asignar tickets a empleados'],
            ['name' => 'resolver_tickets',      'description' => 'Resolver tickets'],
            ['name' => 'cerrar_tickets',        'description' => 'Cerrar tickets'],
            ['name' => 'reabrir_tickets',       'description' => 'Reabrir tickets'],
            ['name' => 'comentar_tickets',      'description' => 'Comentar en tickets'],
            ['name' => 'ver_comentarios',       'description' => 'Ver comentarios de tickets'],
            ['name' => 'gestionar_estado',      'description' => 'Gestionar estado de tickets'],

            // Categorías
            ['name' => 'ver_categorias',        'description' => 'Ver categorías'],
            ['name' => 'crear_categorias',      'description' => 'Crear categorías'],
            ['name' => 'editar_categorias',     'description' => 'Editar categorías'],
            ['name' => 'eliminar_categorias',   'description' => 'Eliminar categorías'],

            // Personas
            ['name' => 'ver_personas',          'description' => 'Ver personas'],
            ['name' => 'crear_personas',        'description' => 'Crear personas'],
            ['name' => 'editar_personas',       'description' => 'Editar personas'],
            ['name' => 'eliminar_personas',     'description' => 'Eliminar personas'],
            ['name' => 'buscar_personas',       'description' => 'Buscar personas'],

            // Usuarios
            ['name' => 'ver_usuarios',          'description' => 'Ver usuarios'],
            ['name' => 'crear_usuarios',        'description' => 'Crear usuarios'],
            ['name' => 'editar_usuarios',       'description' => 'Editar usuarios'],
            ['name' => 'eliminar_usuarios',     'description' => 'Eliminar usuarios'],
            ['name' => 'gestionar_roles',       'description' => 'Gestionar roles de usuarios'],
            ['name' => 'gestionar_permisos',    'description' => 'Gestionar permisos de usuarios'],

            // Roles y Permisos
            ['name' => 'ver_roles',             'description' => 'Ver roles'],
            ['name' => 'crear_roles',           'description' => 'Crear roles'],
            ['name' => 'editar_roles',          'description' => 'Editar roles'],
            ['name' => 'eliminar_roles',        'description' => 'Eliminar roles'],
            ['name' => 'ver_permisos',          'description' => 'Ver permisos'],
            ['name' => 'crear_permisos',        'description' => 'Crear permisos'],
            ['name' => 'editar_permisos',       'description' => 'Editar permisos'],
            ['name' => 'eliminar_permisos',     'description' => 'Eliminar permisos'],
            ['name' => 'asignar_permisos',      'description' => 'Asignar permisos a roles'],

            // Administrativos
            ['name' => 'acceder_panel_admin',   'description' => 'Acceder al panel de administración'],
            ['name' => 'ver_reportes',          'description' => 'Ver reportes'],
            ['name' => 'exportar_datos',        'description' => 'Exportar datos'],
            ['name' => 'ver_logs',              'description' => 'Ver logs del sistema'],
            ['name' => 'gestionar_config',      'description' => 'Gestionar configuraciones'],
            ['name' => 'respaldar_bd',          'description' => 'Respaldar base de datos'],
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso['name'],
                'description' => $permiso['description'],
                'guard_name' => 'web',
            ]);
        }

        // Roles (en español)
        $roles = [
            ['name' => 'super_admin', 'description' => 'Super Administrador'],
            ['name' => 'invitado',    'description' => 'Invitado'],
        ];

        foreach ($roles as $rol) {
            Role::create([
                'name' => $rol['name'],
                'description' => $rol['description'],
                'guard_name' => 'web',
            ]);
        }

        // Asignar todos los permisos al super_admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        $superAdmin->givePermissionTo(Permission::all());

        // Asignar permisos básicos al invitado
        $guest = Role::where('name', 'invitado')->first();
        $guest->givePermissionTo(['ver_categorias']);

        // Usuarios de prueba
        $superAdminPerson = Person::create([
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'first_name' => 'Super',
            'last_name_paternal' => 'Administrador',
            'last_name_maternal' => '',
            'email' => 'superadmin@mgmail.com',
            'phone' => '999999999',
            'address' => 'Av. Principal 123',
            'birth_date' => '1990-01-01',
            'gender' => 'M',
        ]);

        $superAdminUser = User::create([
            'name' => 'Super Administrador',
            'email' => 'superadmin@mgmail.com',
            'password' => bcrypt('123456789'),
            'email_verified_at' => now(),
            'person_id' => $superAdminPerson->id,
        ]);
        $superAdminUser->assignRole('super_admin');

        $citizenPerson = Person::create([
            'document_type' => 'DNI',
            'document_number' => '87654321',
            'first_name' => 'Carlos',
            'last_name_paternal' => 'López',
            'last_name_maternal' => '',
            'email' => 'ciudadano@email.com',
            'phone' => '988888888',
            'address' => 'Calle Secundaria 456',
            'birth_date' => '1995-05-05',
            'gender' => 'M',
        ]);

        $citizenUser = User::create([
            'name' => 'Carlos López',
            'email' => 'ciudadano@email.com',
            'password' => bcrypt('123456789'),
            'email_verified_at' => now(),
            'person_id' => $citizenPerson->id,
        ]);
        $citizenUser->assignRole('invitado');
    }
}
