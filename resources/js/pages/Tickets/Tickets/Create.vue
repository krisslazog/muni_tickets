<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

// Definimos las props que recibimos del controlador
const props = defineProps<{
    categories: { id: number; name: string }[];
    priorities: { id: number; name: string }[];
    areas: { id: number; name: string }[];
}>();

// Usamos el helper 'useForm' de Inertia. ¡Es súper útil!
// Gestiona el estado, los errores y el envío por nosotros.

interface TicketForm {
    title: string;
    description: string;
    category_id: number | null;
    priority_id: number | null;
    area_id: number | null;
    attachment: File | null; // <-- Le decimos que 'attachment' puede ser un Archivo o nulo
}
const form = useForm<TicketForm>({
    title: '',
    description: '',
    category_id: null,
    priority_id: null,
    area_id: null,
    attachment: null,
});

// Función que se ejecuta al enviar el formulario
const submit = () => {
    form.post(route('tickets.tickets.store'), {
        // Hacemos un POST a la ruta 'tickets.store' que creaste en web.php
        onSuccess: () => form.reset(),
    });
};
</script>

<template>

    <Head title="Crear Nuevo Ticket" />

    <AppLayout>
        <div class="container mx-auto max-w-2xl px-4 py-8">
            <header class="mb-8 text-center">
                <h1 class="text-3xl font-bold">
                    Crear un Nuevo Ticket de Soporte
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Por favor, detalla tu solicitud y te ayudaremos lo antes
                    posible.
                </p>
            </header>

            <form @submit.prevent="submit" class="space-y-8 rounded-lg border bg-white p-8 shadow-sm dark:bg-gray-800">
                <div>
                    <Label for="title" class="mb-2 block">Incidencia</Label>
                    <Input id="title" v-model="form.title" type="text" placeholder="Ej: Problema al iniciar sesión"
                        required />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                        {{ form.errors.title }}
                    </div>
                </div>
                <div>
                    <Label for="area" class="mb-2 block">Área Usuaria</Label>
                    <Select v-model="form.area_id">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecciona tu área" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="area in props.areas" :key="area.id" :value="area.id.toString()">
                                {{ area.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div v-if="form.errors.area_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.area_id }}
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <Label for="category" class="mb-2 block">Categoría</Label>
                        <Select v-model="form.category_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Selecciona una categoría" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="category in props.categories" :key="category.id"
                                    :value="category.id.toString()">
                                    {{ category.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.category_id }}
                        </div>
                    </div>

                    <div>
                        <Label for="priority" class="mb-2 block">Prioridad</Label>
                        <Select v-model="form.priority_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Selecciona una prioridad" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="priority in props.priorities" :key="priority.id"
                                    :value="priority.id.toString()">
                                    {{ priority.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="form.errors.priority_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.priority_id }}
                        </div>
                    </div>
                </div>

                <div>
                    <Label for="description" class="mb-2 block">Descripción del Problema</Label>
                    <Textarea id="description" v-model="form.description"
                        placeholder="Describe el problema con el mayor detalle posible..." rows="6" required />
                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                        {{ form.errors.description }}
                    </div>
                </div>

                <div>
                    <Label for="attachment" class="mb-2 block">Adjuntar Imagen (Opcional)</Label>
                    <Input id="attachment" type="file" @input="form.attachment = $event.target.files[0]"
                        class="dark:file:text-gray-300" />
                    <div v-if="form.attachment" class="mt-2 text-sm text-gray-500">
                        Archivo seleccionado: {{ form.attachment.name }}
                    </div>
                    <div v-if="form.errors.attachment" class="mt-1 text-sm text-red-600">
                        {{ form.errors.attachment }}
                    </div>
                </div>

                <div class="pt-4 text-right">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Enviando...' : 'Crear Ticket' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<style scoped></style>
