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
import { CirclePlus, Key, SquarePen, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    users: any;
    roles: any;
    filters: any;
    flash?: {
        success?: string;
        error?: string;
    };
}>();

// Reactive data
const form = ref({
    search: props.filters.search || '',
    role: props.filters.role || '',
    dni: props.filters.dni || '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Usuarios',
        href: '/admin/users',
    },
];

// Methods
const newUser = () => {
    router.visit(route('admin.users.create'));
};

const editUser = (user: any) => {
    router.visit(route('admin.users.edit', user.id));
};

const asignarPermisos = (user: any) => {
    router.visit(route('admin.users.assign-permission', user.id));
};

const deleteUser = (user: any) => {
    if (confirm(`¿Eliminar usuario ${user.full_name}?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

const formatRoleName = (role: string): string => {
    const roleNames: Record<string, string> = {
        admin: 'Administrador',
        employee: 'Empleado',
        citizen: 'Ciudadano',
    };
    return roleNames[role] || role;
};

// Search functionality
let searchTimeout: number | null = null;
const debounceSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        search();
    }, 500);
};

const search = () => {
    router.get(route('admin.users.index'), form.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    form.value = { search: '', role: '', dni: '' };
    router.get(route('admin.users.index'));
};
</script>

<template>
    <Head title="Usuarios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-full px-4 py-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Usuarios
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Aquí puedes gestionar los usuarios del sistema.
                </p>
            </div>

            <div>
                <!-- Mensaje flash -->
                <div
                    v-if="props.flash?.success"
                    class="mb-4 flex items-center rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-gray-800 dark:text-green-400"
                >
                    <span class="font-medium">{{ props.flash.success }}</span>
                </div>

                <!-- Filtros -->
                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <input
                            v-model="form.search"
                            type="text"
                            placeholder="Buscar por nombre, email..."
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                            @input="debounceSearch"
                        />
                    </div>
                    <div>
                        <input
                            v-model="form.dni"
                            type="text"
                            placeholder="Buscar por DNI..."
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                            @input="debounceSearch"
                        />
                    </div>
                    <div>
                        <select
                            v-model="form.role"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                            @change="search"
                        >
                            <option value="">Todos los roles</option>
                            <option
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.name"
                            >
                                {{ formatRoleName(role.name) }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Button @click="clearFilters" class="w-full"
                            >Limpiar</Button
                        >
                    </div>
                </div>

                <!-- Botón nuevo usuario -->
                <div class="mb-4 flex justify-end">
                    <Button
                        class="bg-green-600 text-white hover:bg-green-500"
                        @click="newUser"
                    >
                        <CirclePlus class="mr-2 h-4 w-4" />
                        Nuevo Usuario
                    </Button>
                </div>
            </div>

            <Table hover bordered responsive>
                <TableHead sticky>
                    <TableRow>
                        <TableCell header>Usuario</TableCell>
                        <TableCell header>Email</TableCell>
                        <TableCell header>DNI</TableCell>
                        <TableCell header>Roles</TableCell>
                        <TableCell header>Fecha</TableCell>
                        <TableCell header>Acciones</TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    <TableRow
                        v-for="user in props.users.data"
                        :key="user.id"
                        striped
                        hover
                    >
                        <TableCell>
                            <div class="flex items-center">
                                <div
                                    class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500"
                                >
                                    <span
                                        class="text-xs font-medium text-white"
                                        >{{ user.initials }}</span
                                    >
                                </div>
                                <span class="font-medium">{{
                                    user.full_name
                                }}</span>
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
                            <span
                                v-for="role in user.roles"
                                :key="role.id"
                                class="mr-1 inline-block rounded px-2 py-1 text-xs font-medium"
                                :class="{
                                    'bg-red-100 text-red-800':
                                        role.name === 'admin',
                                    'bg-blue-100 text-blue-800':
                                        role.name === 'employee',
                                    'bg-green-100 text-green-800':
                                        role.name === 'citizen',
                                }"
                            >
                                {{ formatRoleName(role.name) }}
                            </span>
                        </TableCell>
                        <TableCell>
                            {{
                                new Date(user.created_at).toLocaleDateString(
                                    'es-ES',
                                )
                            }}
                        </TableCell>
                        <TableCell>
                            <div class="flex space-x-2">
                                <button @click="editUser(user)" title="Editar">
                                    <SquarePen
                                        class="h-4 w-4 text-yellow-500"
                                    />
                                </button>
                                <button
                                    @click="asignarPermisos(user)"
                                    title="Asignar permisos"
                                >
                                    <Key class="h-4 w-4 text-indigo-600" />
                                </button>
                                <button
                                    @click="deleteUser(user)"
                                    title="Eliminar"
                                >
                                    <Trash2 class="h-4 w-4 text-red-500" />
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
