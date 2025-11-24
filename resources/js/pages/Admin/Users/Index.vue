<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { CirclePlus, Key, SquarePen, Trash2, RotateCcw } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';


const props = defineProps<{
    users: any;
    roles: any;
    filters: any;
    flash: {
        success?: string;
        error?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Administración',
        href: '/admin',
    },
    {
        title: 'Usuarios',
        href: route('admin.users.index'),
    },
];

// Crear usuario
const newUser = () => {
    router.visit(route('admin.users.create'));
};

// Editar usuario
const editUser = (user: any) => {
    router.visit(route('admin.users.edit', user.id));
};

// Asignar permisos
const asignarPermisos = (user: any) => {
    router.visit(route('admin.users.assign-permission', user.id));
};

// Desactivar usuario
const deleteUser = (user: any) => {
    if (confirm(`¿Desactivar usuario ${user.full_name}? El usuario no podrá ingresar al sistema.`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

// Reactivar usuario
const activateUser = (user: any) => {
    if (confirm(`¿Reactivar usuario ${user.full_name}?`)) {
        router.patch(route('admin.users.activate', user.id));
    }
};

// Formatear nombres de roles
const formatRoleName = (role: string): string => {
    const roleNames: Record<string, string> = {
        admin: 'Administrador',
        employee: 'Empleado',
        citizen: 'Ciudadano',
    };
    return roleNames[role] || role;
};
</script>

<template>

    <Head title="Usuarios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-full px-4 py-6 sm:max-w-xl md:max-w-2xl lg:max-w-4xl">
            <!-- Título y descripción -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Gestión de Usuarios
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Aquí puedes gestionar los usuarios del sistema.
                </p>
            </div>

            <div>
                <!-- Mensaje flash -->
                <div v-if="props.flash.success"
                    class="mt-4 mb-4 flex items-center rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-gray-800 dark:text-green-400"
                    role="alert">
                    <svg class="me-3 inline h-4 w-4 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        <span class="font-medium">{{ props.flash.success }}</span>
                    </div>
                </div>
                <!--fin mensaje flash-->

                <!-- Botón de Nuevo Usuario -->
                <div class="mb-4 flex justify-end">
                    <Button class="bg-green-600 text-white hover:bg-green-500" @click="newUser">
                        <CirclePlus class="mr-2 h-4 w-4" />
                        Nuevo Usuario
                    </Button>
                </div>
            </div>

            <Table hover bordered responsive>
                <TableHead sticky>
                    <TableRow>
                        <TableCell header>ID</TableCell>
                        <TableCell header>Usuario</TableCell>
                        <TableCell header>Email</TableCell>
                        <TableCell header>DNI</TableCell>
                        <TableCell header>Roles</TableCell>
                        <TableCell header>Estado</TableCell>
                        <TableCell header>Acciones</TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    <TableRow v-for="user in props.users.data" :key="user.id" striped hover>
                        <TableCell>{{ user.id }}</TableCell>
                        <TableCell>
                            <div class="flex items-center">
                                <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500">
                                    <span class="text-xs font-medium text-white">{{ user.initials }}</span>
                                </div>
                                <span class="font-medium">{{ user.full_name }}</span>
                            </div>
                        </TableCell>
                        <TableCell>{{ user.email }}</TableCell>
                        <TableCell>
                            <span v-if="user.person">
                                {{ user.person.document_number }}
                            </span>
                            <span v-else class="text-gray-400">Sin DNI</span>
                        </TableCell>
                        <TableCell>
                            <Badge v-for="role in user.roles" :key="role.id" variant="secondary"
                                class="mr-1 inline-block rounded px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800">
                                {{ formatRoleName(role.name) }}
                            </Badge>
                        </TableCell>
                        <TableCell>
                            <Badge :variant="user.is_active ? 'default' : 'secondary'"
                                :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                {{ user.is_active ? 'Activo' : 'Inactivo' }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-center align-middle">
                            <div class="flex justify-center space-x-2">
                                <button
                                    @click="editUser(user)"
                                    class="inline-flex items-center justify-center rounded bg-yellow-500 p-1 shadow-none hover:bg-yellow-400"
                                    style="line-height: 1"
                                    title="Editar"
                                >
                                    <SquarePen class="h-4 w-4" color="white" />
                                </button>
                                <button
                                    @click="asignarPermisos(user)"
                                    class="inline-flex items-center justify-center rounded bg-indigo-500 p-1 shadow-none hover:bg-indigo-400"
                                    style="line-height: 1"
                                    title="Asignar permisos"
                                >
                                    <Key class="h-4 w-4" color="white" />
                                </button>
                                <!-- Mostrar botón de desactivar solo si el usuario está activo -->
                                <button
                                    v-if="user.is_active"
                                    @click="deleteUser(user)"
                                    class="inline-flex items-center justify-center rounded bg-red-500 p-1 shadow-none hover:bg-red-400"
                                    style="line-height: 1"
                                    title="Desactivar usuario"
                                >
                                    <Trash2 class="h-4 w-4" color="white" />
                                </button>
                                <!-- Mostrar botón de reactivar solo si el usuario está inactivo -->
                                <button
                                    v-if="!user.is_active"
                                    @click="activateUser(user)"
                                    class="inline-flex items-center justify-center rounded bg-green-500 p-1 shadow-none hover:bg-green-400"
                                    style="line-height: 1"
                                    title="Reactivar usuario"
                                >
                                    <RotateCcw class="h-4 w-4" color="white" />
                                </button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Sin resultados -->
            <div v-if="props.users.data.length === 0" class="py-8 text-center">
                <p class="text-gray-500">No se encontraron usuarios.</p>
            </div>
        </div>
    </AppLayout>
</template>
