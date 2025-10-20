<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';

// Definir las props que recibimos desde el controlador
const props = defineProps<{
    priority: {
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
    name: props.priority.name || '',
    description: props.priority.description || '',
    status: props.priority.status ?? true,
});

//breadcrums
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Prioridades', href: route('tickets.priority.index') },
    {
        title: 'Editar',
        href: route('tickets.priority.edit', props.priority.id),
    },
];

//Crear nueva
function submitForm() {
    submitdisabled.value = true;
    form.put(route('tickets.priority.update', props.priority.id), {
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
    <Head title="Editar Prioridad" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto mt-6 w-full max-w-full rounded-lg border bg-card p-6 text-card-foreground shadow-sm sm:max-w-xl md:max-w-2xl lg:max-w-4xl"
        >
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Editar Prioridad</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Modifica los datos de la prioridad.
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
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{
                            'border-destructive focus-visible:ring-destructive':
                                props.errors.name,
                        }"
                        placeholder="Ej: Alta, Media, Baja"
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
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{
                            'border-destructive focus-visible:ring-destructive':
                                props.errors.description,
                        }"
                        placeholder="Describe la prioridad"
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
                        class="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                    />
                    <label
                        for="status"
                        class="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
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
                        >Actualizar</Button
                    >
                </div>
            </form>
        </div>
    </AppLayout>
</template>
