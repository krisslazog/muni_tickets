<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3'; // Importa usePage
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
} from '@/components/ui/table';
import { CirclePlus, SquarePen } from 'lucide-vue-next';
import { computed } from 'vue'; // Importa computed

// --- 1. Interfaces para la Estructura de Datos ---
interface Ticket {
    id: number;
    title: string;
    created_at: string;
    updated_at: string;
    category: { name: string } | null; // Permite nulo si la categoría puede faltar
    priority: { name: string; color: string } | null;
    status: { name: string; color: string } | null;
    requester: { name: string } | null;
    assignee: { name: string } | null;
}

// Interfaz para el objeto de paginación de Laravel
interface PaginatedData<T> {
    data: T[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    // Añade otras propiedades de paginación si las necesitas (current_page, total, etc.)
}

// --- 2. Definir Props ---
// Esperamos 'tickets' (paginados) y mensajes 'flash' opcionales
const props = defineProps<{
    tickets: PaginatedData<Ticket>;
    flash?: { // Haz flash opcional
        success?: string;
        error?: string; // Añade flash de error si lo usas
    };
    // Incluye categorías, prioridades, estados si los necesitas para filtrar después
    categories?: { id: number; name: string }[];
    priorities?: { id: number; name: string }[];
    statuses?: { id: number; name: string }[];
}>();

// --- 3. Funciones de Navegación ---
const newTicket = () => {
    router.get(route('tickets.tickets.create')); // Usa el nombre de ruta correcto
};

const editTicket = (ticketId: number) => {
    router.get(route('tickets.tickets.edit', { ticket: ticketId }));
};

// --- 4. Funciones Auxiliares ---
const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const options: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    };
    return new Date(dateString).toLocaleDateString('es-ES', options);
};

// Propiedad computada para los mensajes flash, para un acceso más limpio en la plantilla
const flashMessage = computed(() => props.flash?.success || props.flash?.error);
const isSuccessFlash = computed(() => !!props.flash?.success);

</script>

<template>

    <Head title="Gestión de Tickets" />

    <AppLayout>
        <div class="container mx-auto px-4 py-8">

            <div v-if="flashMessage" :class="[
                'mb-6 rounded-md border-l-4 p-4',
                isSuccessFlash
                    ? 'border-green-600 bg-green-100 text-green-800 dark:border-green-800 dark:bg-gray-700 dark:text-green-400'
                    : 'border-red-600 bg-red-100 text-red-800 dark:border-red-800 dark:bg-gray-700 dark:text-red-400',
            ]">
                {{ flashMessage }}
            </div>

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                        Bandeja de Tickets
                    </h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        Visualiza y gestiona todos los tickets de soporte.
                    </p>
                </div>
                <Button @click="newTicket">
                    <CirclePlus class="mr-2 h-4 w-4" />
                    Nuevo Ticket
                </Button>
            </div>

            <div
                class="overflow-x-auto rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell class="w-16">ID</TableCell>
                            <TableCell>Título</TableCell>
                            <TableCell>Solicitante</TableCell>
                            <TableCell>Asignado a</TableCell>
                            <TableCell>Estado</TableCell>
                            <TableCell>Prioridad</TableCell>
                            <TableCell>Creado</TableCell>
                            <TableCell>Últ. Actualización</TableCell>
                            <TableCell class="text-right">Acciones</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        <template v-if="props.tickets.data && props.tickets.data.length > 0">
                            <TableRow v-for="ticket in props.tickets.data.filter(t => t !== null)" :key="ticket.id">
                                <TableCell class="text-sm text-gray-500 dark:text-gray-400">{{ ticket.id }}</TableCell>

                                <TableCell class="font-medium">{{ ticket.title }}</TableCell>

                                <TableCell>{{ ticket.requester?.name ?? 'N/A' }}</TableCell>

                                <TableCell>{{ ticket.assignee?.name ?? 'Sin asignar' }}</TableCell>

                                <TableCell>
                                    <span v-if="ticket.status" class="rounded px-2 py-1 text-xs font-medium" :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': ticket.status.name === 'Abierto',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': ticket.status.name === 'En Progreso',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': ticket.status.name === 'En Espera',
                                        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300': ticket.status.name === 'Resuelto',
                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300':
                                            ticket.status.name === 'Cerrado' ||
                                            ticket.status.name === 'Cancelado' ||
                                            !['Abierto', 'En Progreso', 'En Espera', 'Resuelto'].includes(ticket.status.name)
                                    }">
                                        {{ ticket.status.name }}
                                    </span>
                                    <span v-else class="text-xs text-gray-400 dark:text-gray-500">N/A</span>
                                </TableCell>

                                <TableCell>
                                    <span v-if="ticket.priority"
                                        class="rounded-full px-3 py-1 text-xs font-semibold leading-tight" :class="{
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': ticket.priority.name === 'Baja',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': ticket.priority.name === 'Media',
                                            'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300': ticket.priority.name === 'Alta',
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': ticket.priority.name === 'Urgente',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': !['Baja', 'Media', 'Alta', 'Urgente'].includes(ticket.priority.name)
                                        }">
                                        {{ ticket.priority.name }}
                                    </span>
                                    <span v-else class="text-xs text-gray-400 dark:text-gray-500">N/A</span>
                                </TableCell>

                                <TableCell class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatDate(ticket.created_at) }}
                                </TableCell>

                                <TableCell class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatDate(ticket.updated_at) }}
                                </TableCell>

                                <TableCell class="text-right">
                                    <Button variant="outline" size="sm" @click="editTicket(ticket.id)">
                                        <SquarePen class="mr-2 h-4 w-4" />
                                        Ver / Editar
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </template>
                        <template v-else>
                            <TableRow>
                                <TableCell :colspan="9" class="py-10 text-center text-gray-500 dark:text-gray-400">
                                    No hay tickets para mostrar.
                                </TableCell>
                            </TableRow>
                        </template>
                    </TableBody>
                </Table>
            </div>

            <div v-if="props.tickets.links.length > 3" class="mt-6 flex justify-center space-x-1">
                <Link v-for="(link, index) in props.tickets.links" :key="index" :href="link.url ?? '#'"
                    v-html="link.label" class="rounded-md px-4 py-2 text-sm" :class="{
                        'bg-blue-600 text-white': link.active,
                        'text-gray-700 hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-700': !link.active && link.url,
                        'text-gray-400 cursor-not-allowed': !link.url,
                    }" :disabled="!link.url" as="button" type="button" />
            </div>
        </div>
    </AppLayout>
</template>