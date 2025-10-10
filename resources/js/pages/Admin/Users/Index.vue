<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { BreadcrumbItem } from '@/types';
import { Table, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { CirclePlus, SquarePen, Eye, Trash2 } from 'lucide-vue-next';

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
    dni: props.filters.dni || ''
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

const showUser = (user: any) => {
    router.visit(route('admin.users.show', user.id));
};

const deleteUser = (user: any) => {
    if (confirm(`¿Eliminar usuario ${user.full_name}?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

const formatRoleName = (role: string): string => {
    const roleNames: Record<string, string> = {
        'admin': 'Administrador',
        'employee': 'Empleado',
        'citizen': 'Ciudadano',
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
        preserveScroll: true
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
        <div class="w-full max-w-full px-4 py-6 mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Usuarios</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Aquí puedes gestionar los usuarios del sistema.</p>
            </div>

            <div>
                <!-- Mensaje flash -->
                <div v-if="props.flash?.success"
                    class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800">
                    <span class="font-medium">{{ props.flash.success }}</span>
                </div>

                <!-- Filtros -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <input v-model="form.search" type="text" placeholder="Buscar por nombre, email..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md" @input="debounceSearch" />
                    </div>
                    <div>
                        <input v-model="form.dni" type="text" placeholder="Buscar por DNI..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md" @input="debounceSearch" />
                    </div>
                    <div>
                        <select v-model="form.role" class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            @change="search">
                            <option value="">Todos los roles</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ formatRoleName(role.name) }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Button @click="clearFilters" class="w-full">Limpiar</Button>
                    </div>
                </div>

                <!-- Botón nuevo usuario -->
                <div class="flex justify-end mb-4">
                    <Button class="bg-green-600 hover:bg-green-500 text-white" @click="newUser">
                        <CirclePlus class="h-4 w-4 mr-2" />
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
                    <TableRow v-for="user in props.users.data" :key="user.id" striped hover>
                        <TableCell>
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center mr-3">
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
                            <span v-for="role in user.roles" :key="role.id"
                                class="inline-block px-2 py-1 mr-1 text-xs font-medium rounded" :class="{
                                    'bg-red-100 text-red-800': role.name === 'admin',
                                    'bg-blue-100 text-blue-800': role.name === 'employee',
                                    'bg-green-100 text-green-800': role.name === 'citizen'
                                }">
                                {{ formatRoleName(role.name) }}
                            </span>
                        </TableCell>
                        <TableCell>
                            {{ new Date(user.created_at).toLocaleDateString('es-ES') }}
                        </TableCell>
                        <TableCell>
                            <div class="flex space-x-2">
                                <button @click="showUser(user)" title="Ver">
                                    <Eye class="h-4 w-4 text-blue-500" />
                                </button>
                                <button @click="editUser(user)" title="Editar">
                                    <SquarePen class="h-4 w-4 text-yellow-500" />
                                </button>
                                <button @click="deleteUser(user)" title="Eliminar">
                                    <Trash2 class="h-4 w-4 text-red-500" />
                                </button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Sin resultados -->
            <div v-if="props.users.data.length === 0" class="text-center py-8">
                <p class="text-gray-500">No se encontraron usuarios.</p>
            </div>
        </div>
    </AppLayout>
</template>
