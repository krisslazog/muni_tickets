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
import { defineProps } from 'vue';

// Definir las props que recibimos desde el controlador
const props = defineProps<{
    priorities: any;
    flash: {
        success: string;
        error: string;
        message: string;
        //default: () => ({})
    };
}>();

//Crear categoria
const newPriority = () => {
    router.visit(route('admin.priority.create'));
};
//editar categoria

const editPriority = (priority: any) => {
    let id = priority.id;
    router.visit(route('admin.priority.edit', id));
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Prioridades',
        href: '/admin/priority',
    },
];
</script>

<template>
    <Head title="Prioridades" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto w-full max-w-full px-4 py-6 sm:max-w-xl md:max-w-2xl lg:max-w-4xl"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Prioridades
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Aquí puedes gestionar las prioridades de tus tickets.
                </p>
            </div>
            <div>
                <!-- mensaje flash -->
                <div
                    v-if="props.flash.success"
                    class="mb-4 flex items-center rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-gray-800 dark:text-green-400"
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
                <div class="mb-4 flex justify-end">
                    <Button
                        class="bg-green-600 text-white hover:bg-green-500"
                        @click="newPriority"
                    >
                        <CirclePlus class="mr-0 h-4 w-4" />
                        Nueva Prioridad
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
                        <TableCell header>Acciones</TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    <TableRow
                        v-for="priority in props.priorities"
                        :key="priority.id"
                        striped
                        hover
                    >
                        <TableCell>{{ priority.id }}</TableCell>
                        <TableCell nowrap>{{ priority.name }}</TableCell>
                        <TableCell>{{ priority.description }}</TableCell>
                        <TableCell>
                            <span
                                class="rounded px-2 py-1 text-xs font-medium"
                                :class="
                                    priority.status
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                "
                            >
                                {{ priority.status ? 'Activo' : 'Inactivo' }}
                            </span>
                        </TableCell>
                        <TableCell class="flex items-center justify-center">
                            <button
                                @click="editPriority(priority)"
                                class="flex items-center justify-center rounded bg-yellow-500 p-1 hover:bg-yellow-400"
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
