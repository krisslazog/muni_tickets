<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { disable } from '@/routes/two-factor';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';

// Definir las props que recibimos desde el controlador
const props = defineProps<{
    category?: {
        type: Object;
        name: string;
        description: string;
        status: boolean;
    };
    errors: {
        type: Object;
        name: string;
        description: string;
        //  default: () => ({})
    };
}>();

// Evitar múltiples envíos y deshabilitar el botón
const submitdisabled = ref(false);

//Volver reactivo el formulario
const form = useForm({
    name: props.category?.name || '',
    description: props.category?.description || '',
    status: props.category?.status ?? true,
});

//breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Categorias', href: route('tickets.category.index') },
    { title: 'Crear', href: route('tickets.category.create') },
];

//Crear nueva
function submitForm() {
    submitdisabled.value = true;
    form.post(route('tickets.category.store'), {
        onSuccess: () => {
            form.reset(); // Limpia el formulario
            console.log('Categoría creada correctamente');
        },
        onError: (errors) => {
            console.log('Errores de validación:', errors);
        },
    });
}
</script>

<template>
    <Head title="Crear Categoría" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto mt-6 w-full max-w-full rounded-lg border bg-card p-6 text-card-foreground shadow-sm sm:max-w-xl md:max-w-2xl lg:max-w-4xl"
        >
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Crear Nueva Categoría</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Completa el formulario para agregar una nueva categoría de
                    ticket.
                </p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Nombre -->
                <div>
                    <label
                        for="name"
                        class="mb-2 block text-sm font-medium text-foreground"
                        >Nombre</label
                    >
                    <input
                        type="text"
                        id="name"
                        v-model="form.name"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{
                            'border-destructive focus-visible:ring-destructive':
                                props.errors.name,
                        }"
                        placeholder="Nombre de la categoría"
                    />
                    <p
                        v-if="props.errors.name"
                        class="mt-2 text-sm font-medium text-destructive"
                    >
                        {{ props.errors.name }}
                    </p>
                </div>

                <!-- Descripción -->
                <div>
                    <label
                        for="description"
                        class="mb-2 block text-sm font-medium text-foreground"
                        >Descripción</label
                    >
                    <input
                        type="text"
                        id="description"
                        v-model="form.description"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{
                            'border-destructive focus-visible:ring-destructive':
                                props.errors.description,
                        }"
                        placeholder="Descripción de la categoría"
                    />
                    <p
                        v-if="props.errors.description"
                        class="mt-2 text-sm font-medium text-destructive"
                    >
                        {{ props.errors.description }}
                    </p>
                </div>

                <!-- Checkbox Estado -->
                <div class="flex items-center space-x-2">
                    <input
                        type="checkbox"
                        id="status"
                        v-model="form.status"
                        class="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                    />
                    <label
                        for="status"
                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >
                        Activo
                    </label>
                </div>

                <!-- Botón Guardar al final -->
                <div class="flex justify-end border-t border-border pt-6">
                    <Button
                        type="submit"
                        :disabled="submitdisabled"
                        class="bg-green-600 text-white hover:bg-green-500"
                        >Guardar</Button
                    >
                </div>
            </form>
        </div>
    </AppLayout>
</template>
