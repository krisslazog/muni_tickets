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
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { CirclePlus, SquarePen } from 'lucide-vue-next';

// 1. Definir las props que recibimos desde el controlador sin interfaz.
const props = defineProps<{
    areas: any[];
    flash: {
        success?: string;
        error?: string;
    };
}>();

// 2. Crear área
const newArea = () => {
    router.visit(route('admin.areas.create'));
};

// 3. Editar área
const editArea = (area: any) => {
    router.visit(route('admin.areas.edit', area.id));
};

// 4. Breadcrumbs con la ruta correcta
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Áreas',
        href: route('admin.areas.index'),
    },
];
</script>

<template>
    <Head title="Gestión de Áreas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto w-full max-w-full px-4 py-6 sm:max-w-xl md:max-w-2xl lg:max-w-4xl"
        >
            <!-- 5. Título y descripción -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Gestión de Áreas
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Aquí puedes gestionar las áreas de la organización.
                </p>
            </div>

            <div>
                <!-- Mensaje flash -->
                <div
                    v-if="props.flash.success"
                    class="mt-4 mb-4 flex items-center rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-gray-800 dark:text-green-400"
                    role="alert"
                >
                    <svg
                        class="me-3 inline h-4 w-4 shrink-0"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
                        />
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        <span class="font-medium">{{
                            props.flash.success
                        }}</span>
                    </div>
                </div>
                <!--fin mensaje flash-->
                <!-- 6. Botón de Nueva Área -->
                <div class="mb-4 flex justify-end">
                    <Button
                        class="bg-green-600 text-white hover:bg-green-500"
                        @click="newArea"
                    >
                        <CirclePlus class="mr-2 h-4 w-4" />
                        Nueva Área
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
                    <TableRow v-for="area in props.areas" :key="area.id">
                        <TableCell>{{ area.id }}</TableCell>
                        <TableCell class="font-medium">{{
                            area.name
                        }}</TableCell>
                        <TableCell>{{ area.description }}</TableCell>
                        <TableCell>
                            <span
                                class="rounded px-2 py-1 text-xs font-medium"
                                :class="
                                    area.status
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                "
                            >
                                {{ area.status ? 'Activo' : 'Inactivo' }}
                            </span>
                        </TableCell>
                        <TableCell class="text-center align-middle">
                            <button
                                @click="editArea(area)"
                                class="inline-flex items-center justify-center rounded bg-yellow-500 p-1 shadow-none hover:bg-yellow-400"
                                style="line-height: 1"
                            >
                                <SquarePen class="h-4 w-4" color="white" />
                            </button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
