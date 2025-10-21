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

const form = useForm({
    title: '',
    description: '',
    category_id: null as number | null,
    priority_id: null as number | null,
    area_id: null as number | null,
    attachments: [] as File[],
});

const removeAttachment = (index: number) => {
    // 'splice' modifica el array, eliminando 1 elemento en la posición 'index'
    form.attachments.splice(index, 1);
};

// Función que se ejecuta al enviar el formulario
const submit = () => {
    console.log('✅ Enviando formulario...'); // <-- prueba
    console.log('Ruta:', route('tickets.tickets.store'));

    form.post(route('tickets.tickets.store'), {
        forceFormData: true,
        onSuccess: () => {
            console.log('✅ Ticket creado con éxito');
            form.reset();
        },
        onError: (errors) => {
            console.error('❌ Errores:', errors);
        },
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

            <form
                @submit.prevent="submit"
                class="space-y-8 rounded-lg border bg-white p-8 shadow-sm dark:bg-gray-800"
            >
                <div>
                    <Label for="title" class="mb-2 block">Incidencia</Label>
                    <Input
                        id="title"
                        v-model="form.title"
                        type="text"
                        placeholder="Ej: Problema al iniciar sesión"
                        required
                    />
                    <div
                        v-if="form.errors.title"
                        class="mt-1 text-sm text-red-600"
                    >
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
                            <SelectItem
                                v-for="area in props.areas"
                                :key="area.id"
                                :value="area.id.toString()"
                            >
                                {{ area.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div
                        v-if="form.errors.area_id"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.area_id }}
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <Label for="category" class="mb-2 block"
                            >Categoría</Label
                        >
                        <Select v-model="form.category_id">
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Selecciona una categoría"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="category in props.categories"
                                    :key="category.id"
                                    :value="category.id.toString()"
                                >
                                    {{ category.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div
                            v-if="form.errors.category_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.category_id }}
                        </div>
                    </div>

                    <div>
                        <Label for="priority" class="mb-2 block"
                            >Prioridad</Label
                        >
                        <Select v-model="form.priority_id">
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Selecciona una prioridad"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="priority in props.priorities"
                                    :key="priority.id"
                                    :value="priority.id.toString()"
                                >
                                    {{ priority.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div
                            v-if="form.errors.priority_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.priority_id }}
                        </div>
                    </div>
                </div>

                <div>
                    <Label for="description" class="mb-2 block"
                        >Descripción del Problema</Label
                    >
                    <Textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Describe el problema con el mayor detalle posible..."
                        rows="6"
                        required
                    />
                    <div
                        v-if="form.errors.description"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.description }}
                    </div>
                </div>

                <div>
                    <Label for="attachment" class="mb-2 block">
                        Adjuntar Archivos (Opcional)
                    </Label>
                    <Input
                        id="attachment"
                        type="file"
                        multiple
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                        @input="
                            form.attachments = Array.from(
                                $event.target.files || [],
                            )
                        "
                    />

                    <div
                        v-if="form.attachments.length > 0"
                        class="mt-2 text-sm text-gray-500"
                    >
                        <p>Archivos seleccionados:</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            <li
                                v-for="(file, index) in form.attachments"
                                :key="file.name"
                                class="flex items-center justify-between text-gray-600 dark:text-gray-400"
                            >
                                <span>
                                    {{ file.name }}
                                    <span class="text-xs text-gray-400">
                                        ({{ (file.size / 1024).toFixed(1) }} KB)
                                    </span>
                                </span>

                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    @click="removeAttachment(index)"
                                    class="ml-2 text-red-500 hover:text-red-700"
                                    title="Eliminar archivo"
                                >
                                    &#10005;
                                </Button>
                            </li>
                        </ul>
                    </div>

                    <div
                        v-if="form.errors.attachments"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.attachments }}
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
