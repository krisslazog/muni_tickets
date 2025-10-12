<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Save, Search, UserPlus } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps<{
    roles: any[];
    persons: any[];
    errors: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Usuarios', href: '/admin/users' },
    { title: 'Crear Usuario', href: '/admin/users/create' },
];

// Estados para búsqueda
const searchDocument = ref('');
const searchDocumentType = ref('DNI'); // ← Agregar esta línea
const searchLoading = ref(false);
const personFound = ref(null);
const searchCompleted = ref(false);

// Formulario para crear persona
const personForm = useForm({
    document_type: 'DNI',
    document_number: '',
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
    birth_date: '',
    gender: ''
});

// Formulario para crear usuario
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    person_id: '',
    roles: [] as string[]
});

const searchByDocument = () => {
    if (!searchDocument.value.trim() || !searchDocumentType.value) {
        alert('Selecciona el tipo y número de documento');
        return;
    }

    searchLoading.value = true;

    router.get('/api/persons/search', {
        document_type: searchDocumentType.value,
        document_number: searchDocument.value.trim()
    }, {
        preserveState: true,
        onSuccess: (response) => {
            const data = response.props as any;
            if (data.success) {
                let personFound = data.data.person;
                // Llenar el formulario de usuario con los datos de la persona encontrada
                form.name = personFound.value.first_name + ' ' + personFound.value.last_name;
                form.email = personFound.value.email || '';
                form.person_id = personFound.value.id;
            } else {
                personFound.value = null;
                alert(data.message || 'Persona no encontrada');
            }
        }
    });
};


const createPerson = () => {
    personForm.post(route('admin.persons.store'), {
        onSuccess: (response) => {
            // Llenar datos del usuario con la persona creada
            form.name = personForm.first_name + ' ' + personForm.last_name;
            form.email = personForm.email;
            //form.person_id = response.props.person.id;
            searchCompleted.value = true;
        }
    });
};


const goBack = () => {
    router.visit(route('admin.users.index'));
};
</script>

<template>

    <Head title="Crear Usuario" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-full px-4 py-6 sm:max-w-xl md:max-w-2xl lg:max-w-4xl">
            <!-- PASO 1: Búsqueda por documento -->
            <div class="rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Buscar Persona</h2>

                <!-- Grid responsive -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end mb-5">
                    <!-- Tipo de documento -->
                    <div class="sm:col-span-1 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Tipo
                        </label>
                        <select v-model="searchDocumentType"
                            class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            <option value="DNI">DNI</option>
                            <option value="CE">CE</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="RUC">RUC</option>
                        </select>
                    </div>

                    <!-- Número de documento -->
                    <div class="sm:col-span-1 lg:col-span-7">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Número
                        </label>
                        <input type="text" v-model="searchDocument"
                            class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                            placeholder="Número de documento" />
                    </div>

                    <!-- Botón -->
                    <div class="sm:col-span-2 lg:col-span-2">
                        <Button @click="searchByDocument" :disabled="searchLoading" size="lg"
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white">
                            <Search class="h-4 w-4 mr-2" />
                            <span class="hidden sm:inline">{{ searchLoading ? 'Buscando...' : 'Buscar' }}</span>
                            <span class="sm:hidden">{{ searchLoading ? '...' : 'Buscar' }}</span>
                        </Button>
                    </div>
                </div>
            </div>
            <!-- FORMULARIO PARA CREAR PERSONA -->
            <div class="rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Crear Nueva Persona</h2>

                <form @submit.prevent="createPerson" class="space-y-6">
                    <!-- Datos del documento -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Tipo de Documento *
                            </label>
                            <select v-model="personForm.document_type"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                :class="{ 'border-red-500': errors.document_type }">
                                <option value="DNI">DNI</option>
                                <option value="CE">Carnet de Extranjería</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="RUC">RUC</option>
                            </select>
                            <span v-if="errors.document_type" class="text-red-500 text-sm">{{ errors.document_type
                                }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Número de Documento *
                            </label>
                            <input type="text" v-model="personForm.document_number"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                :class="{ 'border-red-500': errors.document_number }" placeholder="12345678" />
                            <span v-if="errors.document_number" class="text-red-500 text-sm">{{ errors.document_number
                                }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Género
                            </label>
                            <select v-model="personForm.gender"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="O">Otro</option>
                            </select>
                        </div>
                    </div>

                    <!-- Nombres -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Nombres *
                            </label>
                            <input type="text" v-model="personForm.first_name"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                :class="{ 'border-red-500': errors.first_name }" placeholder="Juan Carlos" />
                            <span v-if="errors.first_name" class="text-red-500 text-sm">{{ errors.first_name }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Apellidos *
                            </label>
                            <input type="text" v-model="personForm.last_name"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                :class="{ 'border-red-500': errors.last_name }" placeholder="García López" />
                            <span v-if="errors.last_name" class="text-red-500 text-sm">{{ errors.last_name }}</span>
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Email
                            </label>
                            <input type="email" v-model="personForm.email"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                placeholder="juan@ejemplo.com" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Teléfono
                            </label>
                            <input type="text" v-model="personForm.phone"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                placeholder="987654321" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Fecha de Nacimiento
                            </label>
                            <input type="date" v-model="personForm.birth_date"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" />
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Dirección
                        </label>
                        <textarea v-model="personForm.address" rows="3"
                            class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                            placeholder="Av. Principal 123, Distrito, Provincia"></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <Button type="button" @click="goBack" variant="outline">
                            <ArrowLeft class="h-4 w-4 mr-2" />
                            Cancelar
                        </Button>
                        <Button type="submit" :disabled="personForm.processing"
                            class="bg-green-600 hover:bg-green-500 text-white">
                            <UserPlus class="h-4 w-4 mr-2" />
                            {{ personForm.processing ? 'Creando...' : 'Crear Persona y Continuar' }}
                        </Button>
                    </div>
                </form>
            </div>

            <!-- RESULTADO: PERSONA CREADA -->
            <div v-if="searchCompleted"
                class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-medium text-green-800 dark:text-green-300 mb-2">✓ Persona creada exitosamente
                </h3>
                <p class="text-sm text-green-700 dark:text-green-400">
                    Ahora puedes completar los datos del usuario para la persona: <strong>{{ form.name }}</strong>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
