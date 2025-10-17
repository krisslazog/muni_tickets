<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
    user?: any
    roles?: any[]
    permissions?: any[]
}>()

const form = useForm({
    roles: (props.user?.roles ?? []).map((r: any) => r.id),
    permissions: (props.user?.permissions ?? []).map((p: any) => p.id),
})

const submit = () => {
    form.put(route('users.roles.update', props.user.id))
}
</script>

<template>
    <div class="bg-white p-6 rounded-xl shadow max-w-2xl mx-auto">
        <h2 class="text-lg font-bold mb-4">Asignar Roles y Permisos</h2>

        <div class="mb-6">
            <h3 class="font-semibold mb-2">Roles</h3>
            <div class="grid grid-cols-2 gap-2">
                <label v-for="role in props.roles" :key="role.id" class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.roles" :value="role.id" />
                    {{ role.name }}
                </label>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-semibold mb-2">Permisos</h3>
            <div class="grid grid-cols-3 gap-2">
                <label v-for="perm in props.permissions" :key="perm.id" class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.permissions" :value="perm.id" />
                    {{ perm.name }}
                </label>
            </div>
        </div>

        <div class="text-right">
            <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Guardar cambios
            </button>
        </div>
    </div>
</template>
