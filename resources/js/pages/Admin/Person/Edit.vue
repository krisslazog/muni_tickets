<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

interface Person {
    id: number;
    first_name: string;
    last_name_paternal: string;
    last_name_maternal: string;
    document_type: string;
    document_number: string;
    gender: string;
    birth_date: string;
    phone: string;
    email: string;
    city: string;
    address: string;
    status: boolean;
}

const props = defineProps<{
    person: Person;
}>();

// Form para editar persona
const form = useForm({
    first_name: props.person.first_name,
    last_name_paternal: props.person.last_name_paternal,
    last_name_maternal: props.person.last_name_maternal,
    document_type: props.person.document_type,
    document_number: props.person.document_number,
    gender: props.person.gender,
    birth_date: props.person.birth_date,
    phone: props.person.phone,
    email: props.person.email,
    city: props.person.city,
    address: props.person.address,
    status: props.person.status,
});

// Función para enviar el formulario
const submit = () => {
    form.put(route('admin.person.update', props.person.id));
};

// Función para volver al listado
const goBack = () => {
    window.history.back();
};

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Administración',
        href: '/admin',
    },
    {
        title: 'Personas',
        href: '/admin/person',
    },
    {
        title: 'Editar',
        href: `/admin/person/${props.person.id}/edit`,
    },
];
</script>

<template>
    <Head :title="`Editar ${person.first_name} ${person.last_name_paternal}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl px-4 py-6">
            <div class="mb-6 flex items-center gap-4">
                <Button variant="ghost" @click="goBack" class="p-2">
                    <ArrowLeft class="h-4 w-4" />
                </Button>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                        Editar Persona
                    </h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        Modifica los datos de {{ person.first_name }} {{ person.last_name_paternal }}.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit">
                <Card>
                    <CardHeader>
                        <CardTitle>Información Personal</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Nombres -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="first_name">Nombres *</Label>
                                <Input
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    :class="{ 'border-red-500': form.errors.first_name }"
                                    placeholder="Ingresa los nombres"
                                />
                                <div v-if="form.errors.first_name" class="text-sm text-red-600">
                                    {{ form.errors.first_name }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="last_name_paternal">Apellido Paterno *</Label>
                                <Input
                                    id="last_name_paternal"
                                    v-model="form.last_name_paternal"
                                    type="text"
                                    :class="{ 'border-red-500': form.errors.last_name_paternal }"
                                    placeholder="Ingresa el apellido paterno"
                                />
                                <div v-if="form.errors.last_name_paternal" class="text-sm text-red-600">
                                    {{ form.errors.last_name_paternal }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="last_name_maternal">Apellido Materno</Label>
                                <Input
                                    id="last_name_maternal"
                                    v-model="form.last_name_maternal"
                                    type="text"
                                    :class="{ 'border-red-500': form.errors.last_name_maternal }"
                                    placeholder="Ingresa el apellido materno"
                                />
                                <div v-if="form.errors.last_name_maternal" class="text-sm text-red-600">
                                    {{ form.errors.last_name_maternal }}
                                </div>
                            </div>
                        </div>

                        <!-- Documento -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="document_type">Tipo de Documento *</Label>
                                <Select v-model="form.document_type">
                                    <SelectTrigger :class="{ 'border-red-500': form.errors.document_type }">
                                        <SelectValue placeholder="Selecciona el tipo" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="DNI">DNI</SelectItem>
                                        <SelectItem value="CE">Carnet de Extranjería</SelectItem>
                                        <SelectItem value="PASSPORT">Pasaporte</SelectItem>
                                        <SelectItem value="RUC">RUC</SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.document_type" class="text-sm text-red-600">
                                    {{ form.errors.document_type }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="document_number">Número de Documento *</Label>
                                <Input
                                    id="document_number"
                                    v-model="form.document_number"
                                    type="text"
                                    :class="{ 'border-red-500': form.errors.document_number }"
                                    placeholder="Ingresa el número de documento"
                                />
                                <div v-if="form.errors.document_number" class="text-sm text-red-600">
                                    {{ form.errors.document_number }}
                                </div>
                            </div>
                        </div>

                        <!-- Género y Fecha -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="gender">Género *</Label>
                                <Select v-model="form.gender">
                                    <SelectTrigger :class="{ 'border-red-500': form.errors.gender }">
                                        <SelectValue placeholder="Selecciona el género" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="M">Masculino</SelectItem>
                                        <SelectItem value="F">Femenino</SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.gender" class="text-sm text-red-600">
                                    {{ form.errors.gender }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="birth_date">Fecha de Nacimiento *</Label>
                                <Input
                                    id="birth_date"
                                    v-model="form.birth_date"
                                    type="date"
                                    :class="{ 'border-red-500': form.errors.birth_date }"
                                />
                                <div v-if="form.errors.birth_date" class="text-sm text-red-600">
                                    {{ form.errors.birth_date }}
                                </div>
                            </div>
                        </div>

                        <!-- Contacto -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="email">Email *</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    :class="{ 'border-red-500': form.errors.email }"
                                    placeholder="correo@ejemplo.com"
                                />
                                <div v-if="form.errors.email" class="text-sm text-red-600">
                                    {{ form.errors.email }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="phone">Teléfono</Label>
                                <Input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    :class="{ 'border-red-500': form.errors.phone }"
                                    placeholder="999 999 999"
                                />
                                <div v-if="form.errors.phone" class="text-sm text-red-600">
                                    {{ form.errors.phone }}
                                </div>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="city">Ciudad</Label>
                                <Input
                                    id="city"
                                    v-model="form.city"
                                    type="text"
                                    :class="{ 'border-red-500': form.errors.city }"
                                    placeholder="Ingresa la ciudad"
                                />
                                <div v-if="form.errors.city" class="text-sm text-red-600">
                                    {{ form.errors.city }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="address">Dirección</Label>
                                <Input
                                    id="address"
                                    v-model="form.address"
                                    type="text"
                                    :class="{ 'border-red-500': form.errors.address }"
                                    placeholder="Ingresa la dirección"
                                />
                                <div v-if="form.errors.address" class="text-sm text-red-600">
                                    {{ form.errors.address }}
                                </div>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="space-y-2">
                            <Label for="status">Estado</Label>
                            <Select v-model="form.status">
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="true">Activo</SelectItem>
                                    <SelectItem :value="false">Inactivo</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-4 pt-4">
                            <Button 
                                type="submit" 
                                class="bg-blue-600 hover:bg-blue-700"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Persona' }}
                            </Button>
                            <Button 
                                type="button" 
                                variant="outline" 
                                @click="goBack"
                            >
                                Cancelar
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </form>
        </div>
    </AppLayout>
</template>