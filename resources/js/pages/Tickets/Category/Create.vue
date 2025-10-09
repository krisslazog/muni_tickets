<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { defineProps, computed, ref, watch, onMounted } from 'vue';
import { Button } from '@/components/ui/button';

// Definir las props que recibimos desde el controlador
const props = defineProps<{
    category?: {
        type: Object,
        name: string,
        description: string,
        status: boolean
    },
    errors: {
        type: Object,
        name: string,
        description: string,
        //  default: () => ({})
    },


}>();

const form = useForm({
    name: props.category?.name || '',
    description: props.category?.description || '',
    status: props.category?.status ?? true
});

//breadcrums

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tickets', href: route('tickets.category.index') },
    { title: 'Crear', href: route('tickets.category.create') },
];

//Crear nueva
function submitForm() {
    form.post(route('tickets.category.store'), {
        onSuccess: () => {
            form.reset(); // Limpia el formulario
            console.log('Categoría creada correctamente');
        },
        onError: (errors) => {
            console.log('Errores de validación:', errors);
        }
    });
}
</script>

<template>

    <Head title="Categorias" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full max-w-full sm:max-w-xl md:max-w-2xl lg:max-w-4xl px-4 py-6 mx-auto">
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Nombre -->
                <div class="mb-5">
                    <label for="name"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                    <input type="text" id="name" v-model="form.name" :class="[props.errors.name ?
                        'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 dark:bg-gray-700 focus:border-red-500 block w-full p-2.5 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' :
                        'shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light'
                    ]" placeholder="Nombre de la categoria" />
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ props.errors.name }}</p>
                </div>
                <!-- Descripción -->
                <div class="mb-5">
                    <label for="description"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripción</label>
                    <input type="text" id="description" v-model="form.description" :class="[props.errors.description ?
                        'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 dark:bg-gray-700 focus:border-red-500 block w-full p-2.5 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' :
                        'shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light'
                    ]" placeholder="Descripción de la categoria" />
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ props.errors.description }}</p>

                </div>
                <!-- Checkbox -->
                <div class="flex items-center mb-5">
                    <input id="estado" type="checkbox" v-model="form.status" class="w-4 h-4 border-gray-300 rounded" />
                    <label for="estado" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Activo</label>
                </div>

                <!-- Botón Guardar al final -->
                <div class="flex justify-end">
                    <Button type="submit" class="bg-green-600 hover:bg-green-500 text-white">Guardar</Button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
