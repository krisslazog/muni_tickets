<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, SquarePen, Trash2 } from 'lucide-vue-next';

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
    full_name: string;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    person: Person;
}>();

// Función para volver al listado
const goBack = () => {
    router.visit(route('admin.person.index'));
};

// Función para editar
const editPerson = () => {
    router.visit(route('admin.person.edit', props.person.id));
};

// Función para eliminar
const deletePerson = () => {
    if (confirm(`¿Estás seguro de que deseas eliminar a ${props.person.full_name}?`)) {
        router.delete(route('person.destroy', props.person.id));
    }
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
        title: props.person.full_name,
        href: `/admin/person/${props.person.id}`,
    },
];

// Formatear fecha
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Formatear género
const formatGender = (gender: string) => {
    return gender === 'M' ? 'Masculino' : 'Femenino';
};

// Calcular edad
const calculateAge = (birthDate: string) => {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    
    return age;
};
</script>

<template>
    <Head :title="person.full_name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl px-4 py-6">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="ghost" @click="goBack" class="p-2">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ person.full_name }}
                        </h1>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            Información detallada de la persona
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button 
                        @click="editPerson"
                        class="bg-amber-600 hover:bg-amber-700"
                    >
                        <SquarePen class="mr-2 h-4 w-4" />
                        Editar
                    </Button>
                    <Button 
                        @click="deletePerson"
                        variant="destructive"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </div>

            <div class="grid gap-6">
                <!-- Información Personal -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center justify-between">
                            Información Personal
                            <Badge 
                                :variant="person.status ? 'default' : 'secondary'"
                                :class="person.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            >
                                {{ person.status ? 'Activo' : 'Inactivo' }}
                            </Badge>
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-2">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Nombres</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ person.first_name }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Apellido Paterno</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ person.last_name_paternal }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Apellido Materno</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ person.last_name_maternal || '-' }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Género</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ formatGender(person.gender) }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Fecha de Nacimiento</h4>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ formatDate(person.birth_date) }} 
                                <span class="text-sm text-gray-500">({{ calculateAge(person.birth_date) }} años)</span>
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Documento de Identidad -->
                <Card>
                    <CardHeader>
                        <CardTitle>Documento de Identidad</CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-2">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Tipo de Documento</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ person.document_type }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Número de Documento</h4>
                            <p class="text-gray-600 dark:text-gray-400 font-mono">{{ person.document_number }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Información de Contacto -->
                <Card>
                    <CardHeader>
                        <CardTitle>Información de Contacto</CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-2">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Email</h4>
                            <p class="text-gray-600 dark:text-gray-400">
                                <a :href="`mailto:${person.email}`" class="text-blue-600 hover:underline">
                                    {{ person.email }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Teléfono</h4>
                            <p class="text-gray-600 dark:text-gray-400">
                                <template v-if="person.phone">
                                    <a :href="`tel:${person.phone}`" class="text-blue-600 hover:underline">
                                        {{ person.phone }}
                                    </a>
                                </template>
                                <template v-else>-</template>
                            </p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Ciudad</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ person.city || '-' }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Dirección</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ person.address || '-' }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Información del Sistema -->
                <Card>
                    <CardHeader>
                        <CardTitle>Información del Sistema</CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-2">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Fecha de Registro</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ formatDate(person.created_at) }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Última Actualización</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ formatDate(person.updated_at) }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>