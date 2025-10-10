<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;           // ← Faltaba este import
use Spatie\Permission\Models\Permission;     // ← Faltaba este import
use App\Models\User;                         // ← Faltaba este import

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
// ========================================
        // CREAR PERMISOS
        // ========================================

        // Permisos para Tickets
        $ticketPermissions = [
            'view_tickets',           // Ver tickets
            'view_own_tickets',       // Ver solo sus tickets
            'view_all_tickets',       // Ver todos los tickets
            'create_tickets',         // Crear tickets
            'edit_tickets',           // Editar cualquier ticket
            'edit_own_tickets',       // Editar solo sus tickets
            'delete_tickets',         // Eliminar tickets
            'assign_tickets',         // Asignar tickets a empleados
            'resolve_tickets',        // Resolver tickets
            'close_tickets',          // Cerrar tickets
            'reopen_tickets',         // Reabrir tickets
            'comment_tickets',        // Comentar en tickets
            'view_ticket_comments',   // Ver comentarios
            'manage_ticket_status',   // Cambiar estado de tickets
        ];

        // Permisos para Categorías
        $categoryPermissions = [
            'view_categories',        // Ver categorías
            'create_categories',      // Crear categorías
            'edit_categories',        // Editar categorías
            'delete_categories',      // Eliminar categorías
        ];

        // Permisos para Personas
        $personPermissions = [
            'view_persons',           // Ver personas
            'create_persons',         // Crear personas
            'edit_persons',           // Editar personas
            'delete_persons',         // Eliminar personas
            'search_persons',         // Buscar personas
        ];

        // Permisos para Usuarios
        $userPermissions = [
            'view_users',             // Ver usuarios
            'create_users',           // Crear usuarios
            'edit_users',             // Editar usuarios
            'delete_users',           // Eliminar usuarios
            'manage_user_roles',      // Gestionar roles de usuarios
            'manage_user_permissions', // Gestionar permisos de usuarios
        ];

        // Permisos para Roles y Permisos
        $rolePermissions = [
            'view_roles',             // Ver roles
            'create_roles',           // Crear roles
            'edit_roles',             // Editar roles
            'delete_roles',           // Eliminar roles
            'view_permissions',       // Ver permisos
            'create_permissions',     // Crear permisos
            'edit_permissions',       // Editar permisos
            'delete_permissions',     // Eliminar permisos
            'assign_permissions',     // Asignar permisos a roles
        ];

        // Permisos administrativos
        $adminPermissions = [
            'access_admin_panel',     // Acceder al panel admin
            'view_reports',           // Ver reportes
            'export_data',            // Exportar datos
            'view_logs',              // Ver logs del sistema
            'manage_settings',        // Gestionar configuraciones
            'backup_database',        // Respaldar base de datos
        ];

        // Combinar todos los permisos
        $allPermissions = array_merge(
            $ticketPermissions,
            $categoryPermissions,
            $personPermissions,
            $userPermissions,
            $rolePermissions,
            $adminPermissions
        );

        // Crear todos los permisos
        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ========================================
        // CREAR ROLES
        // ========================================

        // 1. ROL: SUPER ADMIN
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all()); // Todos los permisos

        // 6. ROL: INVITADO (para consultas públicas)
        $guest = Role::create(['name' => 'guest']);
        $guest->givePermissionTo([
            'view_categories', // Solo ver categorías públicas
        ]);

        // ========================================
        // CREAR USUARIOS DE PRUEBA
        // ========================================

        // Usuario Super Admin
        $superAdminUser = User::create([
            'name' => 'Super Administrador',
            'email' => 'superadmin@mgmail.com',
            'password' => bcrypt('123456789'),
            'email_verified_at' => now(),
        ]);
        $superAdminUser->assignRole('super_admin');

        // Usuario Ciudadano
        $citizenUser = User::create([
            'name' => 'Carlos López',
            'email' => 'ciudadano@email.com',
            'password' => bcrypt('123456789'),
            'email_verified_at' => now(),
        ]);

        $citizenUser->assignRole('guest');
    }
}
