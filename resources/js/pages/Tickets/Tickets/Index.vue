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
    tickets: any;
    flash: {
        success: string;
        error: string;
        message: string;
        //default: () => ({})
    };
}>();

//Crear prioridad
const newTicket = () => {
    router.visit(route('tickets.tickets.create'));
};
//editar prioridad

const editTicket = (tickets: any) => {
    let id = tickets.id;
    router.visit(route('tickets.tickets.edit', id));
};

//breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gestion de tickets',
        href: '/tickets/tickets',
    },
];
</script>

<template>

    <Head title="Gestion de tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-full px-4 py-6 sm:max-w-xl md:max-w-2xl lg:max-w-4xl">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Gestion de tickets
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Aquí puedes gestionar los tickets de soporte.
                </p>
            </div>
            <!-- boton nuevo estado -->
            <div class="mb-4 flex justify-end">
                <Button class="bg-green-600 text-white hover:bg-green-500" @click="newTicket">
                    <CirclePlus class="mr-0 h-4 w-4" />
                    Gestion de tickets
                </Button>
            </div>
            <div class="mt-6 overflow-x-auto">
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>Fecha</TableCell>
                            <TableCell>Titulo</TableCell>
                            <TableCell>Categoría</TableCell>
                            <TableCell>Prioridad</TableCell>
                            <TableCell>Estado</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        <TableRow v-for="ticketItem in props.tickets" :key="ticketItem.id">
                            <TableCell>{{ ticketItem.name }}</TableCell>
                            <TableCell>
                                <span class="inline-block h-6 w-16 rounded"
                                    :style="{ backgroundColor: ticketItem.color }"></span>
                            </TableCell>
                            <TableCell>
                                <Button variant="outline" size="sm" @click="editTicket(ticketItem)">
                                    <SquarePen class="mr-2 h-4 w-4" />
                                    Editar
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
