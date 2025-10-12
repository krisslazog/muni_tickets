<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';

// Definir las props que recibimos desde el controlador
const props = defineProps<{
    status: {
        type: Object;
        id: any;
        name: string;
        description: string;
        status: boolean;
    };
    errors: {
        type: Object;
        name: string;
        description: string;
        // default: () => ({})
    };
}>();

// Evitar múltiples envíos y deshabilitar el botón
const submitdisabled = ref(false);

//Volver reactivo el formulario
const form = useForm({
    name: props.status.name || '',
    description: props.status.description || '',
    status: props.status.status ?? true,
});

//breadcrums
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Estados', href: route('tickets.status.index') },
    {
        title: 'Editar',
        href: route('tickets.status.edit', props.status.id),
    },
];

//Crear nueva
function submitForm() {
    submitdisabled.value = true;
    form.put(route('tickets.status.update', props.status.id), {
        onSuccess: () => {
            form.reset(); // Limpia el formulario
            console.log('Actualizada correctamente');
        },
        onError: (errors) => {
            console.log('Errores de validación:', errors);
        },
    });
}
</script>

<template>

    <Head title="Estados" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-full px-4 py-6 sm:max-w-xl md:max-w-2xl lg:max-w-4xl">
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Nombre -->
                <div class="mb-5">
                    <label for="name"
                        class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                    <input type="text" id="name" v-model="form.name" :class="[
                        props.errors.name
                            ? 'block w-full rounded-lg border border-red-500 bg-red-50 p-2.5 text-sm text-red-900 placeholder-red-700 focus:border-red-500 focus:ring-red-500 dark:border-red-500 dark:bg-gray-700 dark:text-red-500 dark:placeholder-red-500'
                            : 'dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500',
                    ]" placeholder="Nombre de estado" />
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                        {{ props.errors.name }}
                    </p>
                </div>
                <!-- Descripción -->
                <div class="mb-5">
                    <label for="description"
                        class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Descripción</label>
                    <input type="text" id="description" v-model="form.description" :class="[
                        props.errors.description
                            ? 'block w-full rounded-lg border border-red-500 bg-red-50 p-2.5 text-sm text-red-900 placeholder-red-700 focus:border-red-500 focus:ring-red-500 dark:border-red-500 dark:bg-gray-700 dark:text-red-500 dark:placeholder-red-500'
                            : 'dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500',
                    ]" placeholder="Descripción de estado" />
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                        {{ props.errors.description }}
                    </p>
                </div>
                <!-- Checkbox -->
                <div class="mb-5 flex items-center">
                    <input id="estado" type="checkbox" v-model="form.status" class="h-4 w-4 rounded border-gray-300" />
                    <label for="estado" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Activo</label>
                </div>


                <!-- Botón Guardar al final -->
                <div class="flex justify-end">
                    <Button type="submit" :disabled="submitdisabled"
                        class="bg-green-600 text-white hover:bg-green-500">Actualizar</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
