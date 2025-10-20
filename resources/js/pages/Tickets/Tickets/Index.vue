<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3'; // Importa usePage
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { CirclePlus, SquarePen } from 'lucide-vue-next';
import { computed } from 'vue'; // Importa computed

// --- 1. Interfaces para la Estructura de Datos ---
interface Ticket {
    id: number;
    title: string;
    created_at: string;
    category: { name: string } | null; // Permite nulo si la categoría puede faltar
    priority: { name: string; color: string } | null;
    status: { name: string; color: string } | null;
    requester: { name: string } | null;
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
                    ? 'border-green-600 bg-green-100 text-green-800' // Estilo éxito
                    : 'border-red-600 bg-red-100 text-red-800', // Estilo error
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

            <div class="overflow-x-auto rounded-lg border bg-white dark:bg-gray-800">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Título</TableHead>
                            <TableHead>Solicitante</TableHead>
                            <TableHead>Estado</TableHead>
                            <TableHead>Prioridad</TableHead>
                            <TableHead>Fecha Creación</TableHead>
                            <TableHead class="text-right">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="props.tickets.data.length > 0">
                            <TableRow v-for="ticket in props.tickets.data" :key="ticket.id">
                                <TableCell class="font-medium">{{ ticket.title }}</TableCell>
                                <TableCell>{{ ticket.requester?.name ?? 'N/A' }}</TableCell> {/* Muestra N/A si no hay
                                solicitante */}
                                <TableCell>
                                    <span v-if="ticket.status"
                                        class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                        :style="{ backgroundColor: ticket.status.color }">
                                        {{ ticket.status.name }}
                                    </span>
                                    <span v-else class="text-xs text-gray-500">N/A</span>
                                </TableCell>
                                <TableCell>
                                    <span v-if="ticket.priority"
                                        class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                        :style="{ backgroundColor: ticket.priority.color }">
                                        {{ ticket.priority.name }}
                                    </span>
                                    <span v-else class="text-xs text-gray-500">N/A</span>
                                </TableCell>
                                <TableCell>{{ formatDate(ticket.created_at) }}</TableCell>
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
                                <TableCell colspan="6" class="py-10 text-center text-gray-500">
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
                        'bg-blue-600 text-white': link.active, // Estilo para página activa
                        'text-gray-700 hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-700': !link.active && link.url, // Estilo normal
                        'text-gray-400 cursor-not-allowed': !link.url, // Estilo deshabilitado
                    }" :disabled="!link.url" as="button" />
            </div>
        </div>
    </AppLayout>
</template>