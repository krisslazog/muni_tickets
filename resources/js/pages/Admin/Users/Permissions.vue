<template>
    <AppLayout>
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow max-w-2xl mx-auto">
            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Asignar Roles y Permisos</h2>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Roles -->
                <div>
                    <h3 class="font-semibold mb-2 text-gray-900 dark:text-white">Roles</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <label v-for="role in props.roles ?? []" :key="role.id" class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.roles" :value="role.id"
                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800" />
                            <span class="text-gray-700 dark:text-gray-200">{{ role.name }}</span>
                        </label>
                    </div>
                </div>

                <!-- Permisos -->
                <div>
                    <h3 class="font-semibold mb-2 text-gray-900 dark:text-white">Permisos</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <label v-for="perm in props.permissions ?? []" :key="perm.id" class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.permissions" :value="perm.id"
                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800" />
                            <span class="text-gray-700 dark:text-gray-200">{{ perm.name }}</span>
                        </label>
                    </div>
                </div>

                <div class="text-right">
                    <Button type="submit" :disabled="form.processing"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Guardar cambios
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    user?: any,
    roles?: any[],
    permissions?: any[],
    errors?: any
}>();

const form = useForm({
    roles: (props.user?.roles ?? []).map((r: any) => r.id),
    permissions: (props.user?.permissions ?? []).map((p: any) => p.id),
});

const submit = () => {
    form.post(route('admin.users.assign-permissions', props.user.id), {
        onSuccess: () => {
            // Mensaje de éxito opcional
        },
        onError: (errors) => {
            console.error('Errores:', errors);
        }
    });
};
</script>
