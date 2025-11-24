<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { CirclePlus, SquarePen, Trash2, Search, Eye } from 'lucide-vue-next';
import { defineProps, ref, watch } from 'vue';

interface Person {
    id: number;
    first_name: string;
    last_name_paternal: string;
    last_name_maternal: string;
    document_type: string;
    document_number: string;
    gender: string;
    birth_date: string;
    phone: string;
    email: string;
    city: string;
    address: string;
    status: boolean;
    full_name: string;
    created_at: string;
}

// Definir las props que recibimos desde el controlador
const props = defineProps<{
    persons: {
        data: Person[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: any[];
    };
    filters: {
        search?: string;
        status?: string;
    };
    flash?: {
        success?: string;
        error?: string;
    };
}>();

// Form para filtros
const searchForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || undefined,
});

// Estado para el modal de eliminación
const showDeleteModal = ref(false);
const personToDelete = ref<Person | null>(null);

// Función para buscar
const search = () => {
    searchForm.get(route('admin.person.index'), {
        preserveState: true,
        replace: true,
    });
};

// Función para limpiar filtros
const clearSearch = () => {
    searchForm.search = '';
    searchForm.status = undefined;
    search();
};

// Crear persona
const newPerson = () => {
    router.visit(route('admin.person.create'));
};

// Ver persona
const showPerson = (person: Person) => {
    router.visit(route('admin.person.show', { person: person.id }));
};

// Editar persona
const editPerson = (person: Person) => {
    router.visit(route('admin.person.edit', { person: person.id }));
};

// Eliminar persona - abrir modal
const deletePerson = (person: Person) => {
    personToDelete.value = person;
    showDeleteModal.value = true;
};

// Confirmar eliminación
const confirmDelete = () => {
    if (personToDelete.value) {
        router.delete(route('admin.person.destroy', { person: personToDelete.value.id }));
        showDeleteModal.value = false;
        personToDelete.value = null;
    }
};

// Cancelar eliminación
const cancelDelete = () => {
    showDeleteModal.value = false;
    personToDelete.value = null;
};

// Función para obtener nombre completo
const getFullName = (person: Person) => {
    const names = [person.first_name, person.last_name_paternal, person.last_name_maternal].filter(Boolean);
    return names.join(' ');
};

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Administración',
        href: '/admin',
    },
    {
        title: 'Personas',
        href: '/admin/person',
    },
];

// Watch para búsqueda en tiempo real
watch(() => searchForm.search, () => {
    if (searchForm.search.length > 2 || searchForm.search.length === 0) {
        search();
    }
});

// Watch para filtro de estado
watch(() => searchForm.status, () => {
    search();
});

// Formatear fecha
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('es-ES');
};

// Formatear género
const formatGender = (gender: string) => {
    return gender === 'M' ? 'Masculino' : 'Femenino';
};
</script>

<template>
    <Head title="Personas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-full px-4 py-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Personas
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Aquí puedes gestionar las personas del sistema.
                </p>
            </div>

            <!-- Mensaje flash -->
            <div v-if="props.flash?.success"
                class="mt-4 mb-4 flex items-center rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-gray-800 dark:text-green-400"
                role="alert">
                <svg class="me-3 inline h-4 w-4 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    <span class="font-medium">{{ props.flash?.success }}</span>
                </div>
            </div>

            <div v-if="props.flash?.error"
                class="mt-4 mb-4 flex items-center rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                <svg class="me-3 inline h-4 w-4 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Error</span>
                <div>
                    <span class="font-medium">{{ props.flash?.error }}</span>
                </div>
            </div>

            <!-- Filtros y botón de crear -->
            <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <Input
                            v-model="searchForm.search"
                            placeholder="Buscar por nombre, apellido, documento o email..."
                            class="pl-10 w-80"
                        />
                    </div>
                    <Select v-model="searchForm.status">
                        <SelectTrigger class="w-40">
                            <SelectValue placeholder="Todos los estados" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="1">Activo</SelectItem>
                            <SelectItem value="0">Inactivo</SelectItem>
                        </SelectContent>
                    </Select>
                    <Button 
                        variant="outline" 
                        @click="clearSearch"
                        class="whitespace-nowrap"
                    >
                        Limpiar
                    </Button>
                </div>
                <Button class="bg-green-600 text-white hover:bg-green-500" @click="newPerson">
                    <CirclePlus class="mr-2 h-4 w-4" />
                    Nueva Persona
                </Button>
            </div>

            <!-- Tabla -->
            <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <Table>
                    <TableHead class="bg-gray-50 dark:bg-gray-800">
                        <TableRow>
                            <TableCell class="font-semibold">Nombre Completo</TableCell>
                            <TableCell class="font-semibold">Tipo Doc.</TableCell>
                            <TableCell class="font-semibold">Documento</TableCell>
                            <TableCell class="font-semibold">Email</TableCell>
                            <TableCell class="font-semibold">Teléfono</TableCell>
                            <TableCell class="font-semibold">Estado</TableCell>
                            <TableCell class="font-semibold text-center">Acciones</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        <TableRow 
                            v-for="person in props.persons.data" 
                            :key="person.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800"
                        >
                            <TableCell class="font-medium">
                                {{ getFullName(person) }}
                                <div class="text-sm text-gray-500">
                                    {{ formatGender(person.gender) }}
                                </div>
                            </TableCell>
                            <TableCell>{{ person.document_type }}</TableCell>
                            <TableCell>{{ person.document_number }}</TableCell>
                            <TableCell>{{ person.email }}</TableCell>
                            <TableCell>{{ person.phone || '-' }}</TableCell>
                            <TableCell>
                                <Badge 
                                    :variant="person.status ? 'default' : 'secondary'"
                                    :class="person.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ person.status ? 'Activo' : 'Inactivo' }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex justify-center gap-2">
                                    <Button 
                                        variant="ghost" 
                                        size="sm" 
                                        @click="showPerson(person)"
                                        class="text-blue-600 hover:text-blue-800"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                    <Button 
                                        variant="ghost" 
                                        size="sm" 
                                        @click="editPerson(person)"
                                        class="text-amber-600 hover:text-amber-800"
                                    >
                                        <SquarePen class="h-4 w-4" />
                                    </Button>
                                    <Button 
                                        variant="ghost" 
                                        size="sm" 
                                        @click="deletePerson(person)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.persons.data.length === 0">
                            <TableCell :colspan="7" class="text-center py-8 text-gray-500">
                                No se encontraron personas
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Paginación -->
            <div v-if="props.persons.last_page > 1" class="mt-4 flex justify-center">
                <div class="flex gap-2">
                    <Button
                        v-for="link in props.persons.links"
                        :key="link.label"
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        @click="router.get(link.url)"
                        :disabled="!link.url"
                        v-html="link.label"
                    />
                </div>
            </div>

            <!-- Información de paginación -->
            <div class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Mostrando {{ props.persons.data.length }} de {{ props.persons.total }} personas
            </div>
        </div>

        <!-- Modal de confirmación para eliminar -->
        <Dialog v-model:open="showDeleteModal">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2 text-red-600">
                        <Trash2 class="h-5 w-5" />
                        Confirmar Eliminación
                    </DialogTitle>
                    <DialogDescription class="pt-2">
                        Esta acción no se puede deshacer. ¿Estás seguro de que deseas eliminar a:
                    </DialogDescription>
                </DialogHeader>
                
                <div v-if="personToDelete" class="py-4">
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/50">
                        <div class="font-semibold text-red-900 dark:text-red-100">
                            {{ getFullName(personToDelete) }}
                        </div>
                        <div class="text-sm text-red-700 dark:text-red-300">
                            {{ personToDelete.document_type }}: {{ personToDelete.document_number }}
                        </div>
                        <div class="text-sm text-red-600 dark:text-red-400">
                            {{ personToDelete.email }}
                        </div>
                    </div>
                </div>

                <DialogFooter class="gap-2 sm:gap-0">
                    <Button 
                        variant="outline" 
                        @click="cancelDelete"
                        class="w-full sm:w-auto"
                    >
                        Cancelar
                    </Button>
                    <Button 
                        variant="destructive" 
                        @click="confirmDelete"
                        class="w-full sm:w-auto"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>