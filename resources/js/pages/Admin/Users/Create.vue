<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Save, Search, UserPlus } from 'lucide-vue-next';
import axios from 'axios';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

const props = defineProps<{
    roles: any[];
    persons: any[];
    errors: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Usuarios', href: '/admin/users' },
    { title: 'Crear Usuario', href: '/admin/users/create' },
];

// Datos de entrada para la búsqueda
const searchDocument = ref('');           // Número de documento a buscar (DNI, CE, etc.)
const searchDocumentType = ref('DNI');    // Tipo de documento seleccionado (DNI, CE, Pasaporte, RUC)

// Estados de control
const searchLoading = ref(false);         // Indica si está ejecutándose una búsqueda
const personFound = ref(null);           // Almacena los datos de la persona encontrada (null si no existe)
const searchCompleted = ref(false);      // Indica si se completó el proceso de búsqueda exitosamente
const messageSearch = ref('');           // Mensaje informativo para mostrar al usuario

// Estados de bloqueo de campos
const fieldsLocked = ref(false);         // Bloquea TODOS los campos del formulario (cuando persona existe)
const documentFieldsLocked = ref(false); // Bloquea SOLO tipo y número de documento (cuando persona no existe)

// Formulario para crear persona
const personForm = useForm({
    document_type: '',
    document_number: '',
    first_name: '',
    last_name_paternal: '',
    last_name_maternal: '',
    email: '',
    phone: '',
    address: '',
    birth_date: '',
    gender: '',
    status: true
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

const searchByDocument = async () => {
    if (!searchDocument.value.trim() || !searchDocumentType.value) {
        alert('Selecciona el tipo y número de documento');
        return;
    }

    searchLoading.value = true;
    messageSearch.value = '';

    try {
        const response = await axios.get(route('admin.users.search-by-document'), {
            params: {
                document_type: searchDocumentType.value,
                document_number: searchDocument.value.trim()
            },
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        console.log('Respuesta de búsqueda:', !response.data.data?.person.user);


        if (!response.data.data?.person && !response.data.data?.person.user) {
            messageSearch.value = `No se encontro la persona, por favor registre los campos`;
            personForm.reset();
            personForm.document_type = searchDocumentType.value;
            personForm.document_number = searchDocument.value.trim();
            documentFieldsLocked.value = true;
            fieldsLocked.value = false;
        }
        if (response.data.success && response.data.data?.person && !response.data.data?.person.user) {
            personFound.value = response.data.data;
            // Llenar el formulario de persona con los datos encontrados
            Object.assign(personForm, response.data.data.person);
            messageSearch.value = `Persona encontrada, si desea puede actualizar los datos o continuar con el registro del usuario.`;
        }


        return
        if (response.data.success && response.data.data?.person) {
            // Persona encontrada
            personFound.value = response.data.data.person;

            // Llenar el formulario de usuario con los datos de la persona encontrada
            form.name = response.data.data.person.first_name + ' ' + response.data.data.person.last_name;
            form.email = response.data.data.person.email || '';
            form.person_id = response.data.data.person.id;

            // Mostrar mensaje de éxito
            alert(`✓ Persona encontrada: ${response.data.data.person.first_name} ${response.data.data.person.last_name}`);

        } else {
            // No se encontró la persona
            personFound.value = null;
            alert(response.data.message || 'Persona no encontrada');
        }

    } catch (error: any) {
        console.error('Error al buscar:', error);

        if (error.response?.status === 404) {
            alert('Persona no encontrada con esos datos');
        } else if (error.response?.status === 422) {
            // Errores de validación
            const errors = error.response.data.errors;
            let errorMsg = 'Errores de validación:\n';
            Object.keys(errors).forEach(key => {
                errorMsg += `- ${errors[key][0]}\n`;
            });
            alert(errorMsg);
        } else {
            alert('Error al buscar la persona. Verifica tu conexión.');
        }

        personFound.value = null;
    } finally {
        searchLoading.value = false;
    }
};


const createPerson = () => {
    personForm.post(route('admin.persons.store'), {
        onSuccess: (response) => {
            // Llenar datos del usuario con la persona creada
            //form.name = personForm.first_name + ' ' + personForm.last_name;
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
                        <select v-model="searchDocumentType" :disabled="searchLoading"
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
                        <input type="text" v-model="searchDocument" :disabled="searchLoading"
                            @keyup.enter="searchByDocument"
                            class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                            placeholder="Número de documento" />
                    </div>

                    <!-- Botón -->
                    <div class="sm:col-span-2 lg:col-span-2">
                        <Button @click="searchByDocument"
                            :disabled="searchLoading || !searchDocument.trim() || !searchDocumentType" size="lg"
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white">
                            <Search class="h-4 w-4 mr-2" />
                            <span class="hidden sm:inline">{{ searchLoading ? 'Buscando...' : 'Buscar' }}</span>
                            <span class="sm:hidden">{{ searchLoading ? '...' : 'Buscar' }}</span>
                        </Button>
                    </div>
                </div>
            </div>
            <!-- mensaje flash -->
            <div v-if="!!messageSearch"
                class="mb-4 flex items-center rounded-lg border  bg-blue-50 p-4 text-sm text-blue-800 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-400"
                role="alert">
                <svg class="me-3 inline h-4 w-4 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div>
                    <span class="font-medium">{{
                        messageSearch
                    }}</span>
                </div>
            </div>
            <!--fin mensaje flash-->
            <!-- FORMULARIO PARA CREAR PERSONA -->
            <div class="rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Crear Nueva Persona</h2>

                <form @submit.prevent="createPerson" class="space-y-6">
                    <!-- ✅ Datos del documento - CORREGIR clases disabled -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Tipo de Documento *
                            </label>
                            <select v-model="personForm.document_type" :disabled="fieldsLocked || documentFieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
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
                                :disabled="fieldsLocked || documentFieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': errors.document_number }" placeholder="12345678" />
                            <span v-if="errors.document_number" class="text-red-500 text-sm">{{ errors.document_number
                                }}</span>
                        </div>

                        <!-- ✅ Género - USAR SELECT NATIVO PARA EVITAR PROBLEMAS -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Género
                            </label>
                            <select v-model="personForm.gender" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600">
                                <option value="">Seleccionar género...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="O">Otro</option>
                            </select>
                        </div>
                    </div>

                    <!-- ✅ Nombres - AGREGAR clases disabled -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Nombres *
                            </label>
                            <input type="text" v-model="personForm.first_name" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': errors.first_name }" placeholder="Juan Carlos" />
                            <span v-if="errors.first_name" class="text-red-500 text-sm">{{ errors.first_name }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Apellido Paterno *
                            </label>
                            <input type="text" v-model="personForm.last_name_paternal" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': errors.last_name_paternal }" placeholder="García" />
                            <span v-if="errors.last_name_paternal" class="text-red-500 text-sm">{{
                                errors.last_name_paternal }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Apellido Materno
                            </label>
                            <input type="text" v-model="personForm.last_name_maternal" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': errors.last_name_maternal }"
                                placeholder="López (opcional)" />
                            <span v-if="errors.last_name_maternal" class="text-red-500 text-sm">{{
                                errors.last_name_maternal }}</span>
                        </div>
                    </div>

                    <!-- ✅ Contacto - AGREGAR clases disabled -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Email
                            </label>
                            <input type="email" v-model="personForm.email" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                placeholder="juan@ejemplo.com" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Teléfono
                            </label>
                            <input type="text" v-model="personForm.phone" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                placeholder="987654321" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Fecha de Nacimiento
                            </label>
                            <input type="date" v-model="personForm.birth_date" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600" />
                        </div>
                    </div>

                    <!-- ✅ Dirección - AGREGAR clases disabled -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Dirección
                        </label>
                        <textarea v-model="personForm.address" rows="3" :disabled="fieldsLocked"
                            class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                            placeholder="Av. Principal 123, Distrito, Provincia"></textarea>
                    </div>

                    <!-- ... botones igual ... -->
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
