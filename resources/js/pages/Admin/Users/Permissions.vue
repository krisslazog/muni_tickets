<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { useForm, Head, router } from '@inertiajs/vue3'

// shadcn/ui
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'


const props = defineProps<{
    user: any
    roles: any[]
    permissions: any[]
    errors?: any
}>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administración', href: '/admin' },
    { title: 'Usuarios', href: route('admin.users.index') },
    { title: 'Asignar Permisos', href: '' },
]

const form = useForm({
    roles: (props.user?.roles ?? []).map((r: any) => r.id),
    permissions: (props.user?.permissions ?? []).map((p: any) => p.id),
})

function toggleRole(id: number, checked: boolean) {
    if (checked) {
        if (!form.roles.includes(id)) form.roles.push(id)
    } else {
        form.roles = form.roles.filter((rid: any) => rid !== id)
    }
}

function togglePermission(id: number, checked: boolean) {
    if (checked) {
        if (!form.permissions.includes(id)) form.permissions.push(id)
    } else {
        form.permissions = form.permissions.filter((pid: any) => pid !== id)
    }
}


const submit = () => {
    form.post(route('admin.users.store-assign-permission', props.user.id), {
        onSuccess: () => {
            router.visit(route('admin.users.index'))
        },
        onError: (errors) => {
            console.error('Error al asignar permisos:', errors)
        },
    })
}

// Función para cancelar y volver al listado
const goBack = () => {
    router.visit(route('admin.users.index'))
}
</script>

<template>

    <Head title="Asignar Roles y Permisos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto mt-6 w-full max-w-full rounded-lg border bg-card p-6 text-card-foreground shadow-sm sm:max-w-xl md:max-w-2xl lg:max-w-4xl">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Asignar Roles y Permisos</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Gestiona los privilegios del usuario: <span class="font-semibold">{{ props.user?.name }}</span>
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <!-- Sección Roles -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Roles</h3>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <label v-for="role in props.roles" :key="role.id"
                            class="flex items-center gap-2 rounded-md border border-gray-200 dark:border-gray-700 p-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer">
                            <Checkbox :model-value="form.roles.includes(role.id)"
                                @update:model-value="(checked: any) => toggleRole(role.id, checked)" />
                            <span class="text-sm text-gray-700 dark:text-gray-200">
                                {{ role.description ?? role.name }}
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Sección Permisos -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Permisos</h3>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <label v-for="perm in props.permissions" :key="perm.id"
                            class="flex items-center gap-2 rounded-md border border-gray-200 dark:border-gray-700 p-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer">
                            <Checkbox :model-value="form.permissions.includes(perm.id)"
                                @update:model-value="(checked: any) => togglePermission(perm.id, checked)" />
                            <span class="text-sm text-gray-700 dark:text-gray-200">
                                {{ perm.description ?? perm.name }}
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-4 pt-4">
                    <Button 
                        type="submit" 
                        class="bg-green-600 hover:bg-green-700"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                    </Button>
                    <Button 
                        type="button" 
                        variant="outline" 
                        @click="goBack"
                    >
                        Cancelar
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
