<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { defineProps, ref, computed } from 'vue';

// Definir las props que recibimos desde el controlador
const props = defineProps<{
    role: {
        id: number;
        name: string;
        description: string;
        status: boolean;
        permissions?: number[]; // IDs asignados al rol
    };
    permissions: {
        id: number;
        name: string;
        description?: string;
        group?: string;
    }[];
    errors?: Record<string, any>;
}>();

// Evitar múltiples envíos y deshabilitar el botón
const submitdisabled = ref(false);

//Volver reactivo el formulario
const form = useForm({
    name: props.role.name || '',
    description: props.role.description || '',
    status: props.role.status ?? true,
    permissions: props.role.permissions || [],
});

//breadcrums
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Roles', href: route('admin.roles.index') },
    {
        title: 'Editar',
        href: route('admin.roles.edit', props.role.id),
    },
];
const cancel = () => {
    router.visit(route('admin.roles.index'));
};
//Actualizar
function submitForm() {
    submitdisabled.value = true;
    form.put(route('admin.roles.update', props.role.id), {
        onSuccess: () => {
            form.reset(); // Limpia el formulario
            console.log('Actualizada correctamente');
        },
        onError: (errors) => {
            console.log('Errores de validación:', errors);
        },
    });
}

// Agrupar permisos por su campo `group` para renderizado
const groupedPermissions = computed(() => {
    const map = new Map<string, any[]>();
    (props.permissions || []).forEach((p: any) => {
        const g = p.group ?? 'Sin grupo';
        if (!map.has(g)) map.set(g, []);
        map.get(g)!.push(p);
    });
    return Array.from(map.entries());
});
</script>

<template>

    <Head title="Editar Rol" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto mt-6 w-full max-w-full rounded-lg border bg-card p-6 text-card-foreground shadow-sm sm:max-w-xl md:max-w-2xl lg:max-w-4xl">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Editar Rol</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Modifica los datos del rol.
                </p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-foreground">Nombre</label>
                    <input type="text" id="name" v-model="form.name"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{
                            'border-destructive focus-visible:ring-destructive':
                                props.errors.name,
                        }" placeholder="Ej: Tesorería, Informática, etc." />
                    <p v-if="props.errors.name" class="mt-2 text-sm font-medium text-destructive">
                        {{ props.errors.name }}
                    </p>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-foreground">Descripción</label>
                    <input type="text" id="description" v-model="form.description"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{
                            'border-destructive focus-visible:ring-destructive':
                                props.errors.description,
                        }" placeholder="Describe el propósito del área" />
                    <p v-if="props.errors.description" class="mt-2 text-sm font-medium text-destructive">
                        {{ props.errors.description }}
                    </p>
                </div>

                <!-- Checkbox Estado -->
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="status" v-model="form.status" :checked="form.status"
                        class="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground" />
                    <label for="status"
                        class="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        Activo
                    </label>
                </div>

                <!-- Botón Guardar al final -->
                <!-- Selector de permisos agrupados por 'group' -->
                <div>
                    <label class="mb-2 block text-sm font-medium">Permisos</label>
                    <div class="space-y-2 max-h-80 overflow-y-auto border rounded-md p-4">
                        <div v-for="([groupName, perms]) in groupedPermissions" :key="groupName" class="mb-3">
                            <details class="rounded-md border bg-card p-3">
                                <summary class="cursor-pointer list-none font-medium">
                                    {{ groupName || 'Sin grupo' }}
                                    <span class="ml-2 text-xs text-gray-500">({{ perms.length }})</span>
                                </summary>
                                <div class="mt-3 space-y-2">
                                    <div v-for="permission in perms" :key="permission.id" class="flex items-center space-x-2">
                                        <input type="checkbox" :id="`perm-${permission.id}`" :value="permission.id"
                                            v-model="form.permissions" class="h-4 w-4 rounded border-gray-300" />
                                        <label :for="`perm-${permission.id}`" class="text-sm">
                                            <span class="font-medium">{{ permission.name }}</span>
                                            <span class="ml-2 text-xs text-gray-500">{{ permission.description || '' }}</span>
                                        </label>
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-4">
                    <Button type="button" class="bg-gray-600 text-white hover:bg-gray-500" @click="cancel">
                        Cancelar
                    </Button>
                    <Button type="submit" :disabled="form.processing || submitdisabled"
                        class="bg-green-600 text-white hover:bg-green-500">Actualizar</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
