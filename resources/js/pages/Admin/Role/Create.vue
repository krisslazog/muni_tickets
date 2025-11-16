<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { defineProps, ref, computed } from 'vue';

// Definir las props que recibimos desde el controlador (corregido)
const props = defineProps<{
    role?: {
        id: number;
        name: string;
        description: string;
        status: boolean;
        permissions: number[]; // IDs de los permisos asignados
    };
    permissions: {
        id: number;
        name: string;
        description: string;
        group: string;
    }[];
    errors?: Record<string, any>;
}>();

// Evitar múltiples envíos y deshabilitar el botón
const submitdisabled = ref<boolean>(false);

// Volver reactivo el formulario
const form = useForm({
    name: props.role?.name || '',
    description: props.role?.description || '',
    status: props.role?.status ?? true,
    permissions: props.role?.permissions || [],
});

// breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Roles', href: route('admin.roles.index') },
    { title: 'Crear', href: route('admin.roles.create') },
];

const cancel = () => {
    router.visit(route('admin.roles.index'));
};
// Crear nuevo rol
function submitForm() {
    submitdisabled.value = true;
    form.post(route('admin.roles.store'), {
        onSuccess: () => {
            form.reset(); // Limpia el formulario
            console.log('Rol creado correctamente');
        },
        onError: (errors) => {
            console.log('Errores de validación:', errors);
        },
        onFinish: () => {
            // Siempre re-habilitar el botón al terminar la petición
            submitdisabled.value = false;
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

    <Head title="Crear rol" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto mt-6 w-full max-w-full rounded-lg border bg-card p-6 text-card-foreground shadow-sm sm:max-w-xl md:max-w-2xl lg:max-w-4xl">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Crear Nuevo Rol</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Completa el formulario para agregar un nuevo rol.
                </p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-foreground">Nombre</label>
                    <input type="text" id="name" v-model="form.name"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{
                            'border-destructive focus-visible:ring-destructive':
                                props.errors?.name,
                        }" placeholder="Nombre del rol" />
                    <p v-if="props.errors?.name" class="mt-2 text-sm font-medium text-destructive">
                        {{ props.errors?.name }}
                    </p>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-foreground">Descripción</label>
                    <input type="text" id="description" v-model="form.description"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{
                            'border-destructive focus-visible:ring-destructive':
                                props.errors?.description,
                        }" placeholder="Descripción del rol" />
                    <p v-if="props.errors?.description" class="mt-2 text-sm font-medium text-destructive">
                        {{ props.errors?.description }}
                    </p>
                </div>

                <!-- Checkbox Estado -->
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="status" v-model="form.status"
                        class="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground" />
                    <label for="status"
                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        Activo
                    </label>
                </div>
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
                                    <div v-for="permission in perms" :key="permission.id"
                                        class="flex items-center space-x-2">
                                        <input type="checkbox" :id="`perm-${permission.id}`" :value="permission.id"
                                            v-model="form.permissions" class="h-4 w-4 rounded border-gray-300" />
                                        <label :for="`perm-${permission.id}`" class="text-sm">
                                            <span class="font-medium">{{ permission.name }}</span>
                                            <span class="ml-2 text-xs text-gray-500">{{ permission.description || ''
                                                }}</span>
                                        </label>
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
                <!-- Botón Guardar al final -->
                <div class="flex justify-end space-x-4">
                    <Button type="button" class="bg-gray-600 text-white hover:bg-gray-500" @click="cancel">
                        Cancelar
                    </Button>
                    <Button type="submit" :disabled="submitdisabled"
                        class="bg-green-600 text-white hover:bg-green-500">Guardar</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
