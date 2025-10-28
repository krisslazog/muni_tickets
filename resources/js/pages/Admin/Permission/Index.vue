<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
} from '@/components/ui/table';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination'
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { CirclePlus, SquarePen } from 'lucide-vue-next';

// 1. Definir las props que recibimos desde el controlador sin interfaz.
const props = defineProps<{
    permissions: {
        data: any[]; // Los permisos actuales
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: { url: string | null; label: string; page: number; active: boolean }[];
    };
    flash: {
        success?: string;
        error?: string;
    };
}>();

const goToPage = (url: string | null) => {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true,
        });
    }
};

// 2. Crear permiso
const newPermission = () => {
    router.visit(route('admin.permissions.create'));
};

// 3. Editar permiso
const editPermission = (permission: any) => {
    router.visit(route('admin.permissions.edit', permission.id));
};

// 4. Breadcrumbs con la ruta correcta
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Permisos',
        href: route('admin.permissions.index'),
    },
];

console.log(props.permissions)
</script>

<template>

    <Head title="Gestión de Permisos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-full px-4 py-6 sm:max-w-xl md:max-w-2xl lg:max-w-4xl">
            <!-- 5. Título y descripción -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Gestión de Permisos
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Aquí puedes gestionar los permisos de la organización.
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
                        <span class="font-medium">{{
                            props.flash.success
                        }}</span>
                    </div>
                </div>
                <!--fin mensaje flash-->
                <!-- 6. Botón de Nuevo Permiso -->
                <div class="mb-4 flex justify-end">
                    <Button class="bg-green-600 text-white hover:bg-green-500" @click="newPermission">
                        <CirclePlus class="mr-2 h-4 w-4" />
                        Nuevo Permiso
                    </Button>
                </div>
            </div>
            <Table hover bordered responsive>
                <TableHead sticky>
                    <TableRow>
                        <TableCell header>ID</TableCell>
                        <TableCell header>Nombre</TableCell>
                        <TableCell header>Descripción</TableCell>
                        <TableCell header>Estado</TableCell>
                        <TableCell class="text-center">Acciones</TableCell>
                    </TableRow>
                </TableHead>
                <TableBody>
                    <TableRow v-for="permission in props.permissions.data" :key="permission.id">
                        <TableCell>{{ permission.id }}</TableCell>
                        <TableCell class="font-medium">{{
                            permission.name
                        }}</TableCell>
                        <TableCell>{{ permission.description }}</TableCell>
                        <TableCell>
                            <span class="rounded px-2 py-1 text-xs font-medium" :class="permission.status
                                ? 'bg-green-100 text-green-800'
                                : 'bg-red-100 text-red-800'
                                ">
                                {{ permission.status ? 'Activo' : 'Inactivo' }}
                            </span>
                        </TableCell>
                        <TableCell class="text-center align-middle">
                            <button @click="editPermission(permission)"
                                class="inline-flex items-center justify-center rounded bg-yellow-500 p-1 shadow-none hover:bg-yellow-400"
                                style="line-height: 1">
                                <SquarePen class="h-4 w-4" color="white" />
                            </button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination v-slot="{ page }" :items-per-page="10" :total="30" :default-page="2">
                <PaginationContent v-slot="{ items }">
                    <PaginationPrevious />

                    <template v-for="(item, index) in props.permissions.links" :key="index">
                        <PaginationItem :value="item.page" :is-active="item.active" @click="goToPage(item.url)">
                            {{ item.label }}
                        </PaginationItem>
                    </template>

                    <PaginationEllipsis :index="4" />

                    <PaginationNext />
                </PaginationContent>
            </Pagination>
        </div>
    </AppLayout>
</template>
