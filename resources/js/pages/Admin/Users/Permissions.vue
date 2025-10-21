<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { useForm } from '@inertiajs/vue3'

// shadcn/ui
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'


const props = defineProps<{
    user: any
    roles: any[]
    permissions: any[]
    errors?: any
}>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Usuarios', href: '/admin/users' },
    { title: 'Asignar Roles y Permisos', href: '/admin/users/assign-permission' },
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

        },
        onError: () => {

        },
    })
}
</script>

<template>

    <Head title="Asignar Roles y Permisos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-5xl px-4 py-10 space-y-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        Asignar Roles y Permisos
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Gestiona los privilegios del usuario:
                        <span class="font-semibold text-blue-600 dark:text-blue-400">
                            {{ props.user?.name }}
                        </span>
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-8">

                <!-- Sección Roles -->
                <div>
                    <Card class="border border-gray-200 dark:border-gray-700 shadow-sm">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Badge variant="secondary" class="text-blue-600">Roles</Badge>
                                <span class="text-gray-900 dark:text-gray-100">Asignar Roles</span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
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
                        </CardContent>
                    </Card>
                </div>

                <!-- Sección Permisos -->
                <div>
                    <Card class="border border-gray-200 dark:border-gray-700 shadow-sm">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Badge variant="secondary" class="text-amber-600">Permisos</Badge>
                                <span class="text-gray-900 dark:text-gray-100">Asignar Permisos</span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
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
                        </CardContent>
                    </Card>
                </div>

                <!-- Botón Guardar -->
                <div class="flex justify-end">
                    <Button type="submit" size="lg" :disabled="form.processing" class="px-8">
                        Guardar cambios
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
