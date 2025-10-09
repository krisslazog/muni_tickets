<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps, ref, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Table, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { CirclePlus } from 'lucide-vue-next';
import { SquarePen } from 'lucide-vue-next';

// Definir las props que recibimos desde el controlador
const props = defineProps<{

    categories: any,
    flash: {
        success: string,
        error: string,
        message: string,
        default: () => ({})
    }

}>();

//Crear categoria
const newCategory = () => {
    router.visit(route('tickets.category.create'))
}
//editar categoria

const editCategory = (category: any) => {
    let id = category.id
    router.visit(route('tickets.category.edit', id))
}



const breadcrumbs: BreadcrumbItem[] = [
    {

        title: 'Categorias',
        href: '/tickets/category',
    },
];



</script>

<template>

    <Head title="Categorias" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="w-full max-w-full sm:max-w-xl md:max-w-2xl lg:max-w-4xl px-4 py-6 mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Categorias</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Aquí puedes gestionar las categorias de tus tickets.
                </p>
            </div>
            <div>
                <!-- mensaje flash -->
                <div v-if="props.flash.success"
                    class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800"
                    role="alert">
                    <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
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
                <div class="flex justify-end mb-4">
                    <Button class="bg-green-600 hover:bg-green-500 text-white" @click="newCategory">
                        <CirclePlus class="h-4 w-4 mr-0" />
                        Nueva Categoria
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
                    <TableRow v-for="category in props.categories" :key="category.id" striped hover>
                        <TableCell>{{ category.id }}</TableCell>
                        <TableCell nowrap>{{ category.name }}</TableCell>
                        <TableCell>{{ category.description }}</TableCell>
                        <TableCell>
                            <span class="px-2 py-1 rounded text-xs font-medium"
                                :class="category.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                {{ category.status ? 'Activo' : 'Inactivo' }}
                            </span>
                        </TableCell>
                        <TableCell>
                            <button @click="editCategory(category)">
                                <SquarePen color="yellow" />
                            </button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>

</template>
