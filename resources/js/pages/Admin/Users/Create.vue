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
    { title: 'Administración', href: '/admin' },
    { title: 'Usuarios', href: route('admin.users.index') },
    { title: 'Crear', href: route('admin.users.create') },
];

// Estados de búsqueda
const searchDocument = ref('');
const searchDocumentType = ref('DNI');
const isSearching = ref(false);
const searchMessage = ref('');
const searchMessageType = ref<'info' | 'success' | 'warning' | 'error'>('info');

// Estados del formulario
const formMode = ref<'search' | 'create-new' | 'create-user' | 'update-person'>('search');
const foundPerson = ref<any>(null);
const canEditPersonData = ref(true);
const form = useForm({
    // Datos de la persona
    id: null as number | null,
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

    // Datos del usuario (sin roles)
    user_id: null as number | null,
    password: '',
    password_confirmation: '',

});


// Reiniciar todo el formulario
const resetForm = () => {
    searchDocument.value = '';
    searchDocumentType.value = 'DNI';
    isSearching.value = false;
    searchMessage.value = '';
    formMode.value = 'search';
    foundPerson.value = null;
    canEditPersonData.value = true;
    form.reset();
    form.clearErrors();
};

// Permitir crear usuario sin buscar primero
// Crear sin búsqueda previa
const createWithoutSearch = () => {
    formMode.value = 'create-new';
    searchMessage.value = 'Registrando nueva persona y usuario';
    searchMessageType.value = 'info';
    canEditPersonData.value = true;
    foundPerson.value = null;
    form.reset();
};

// Nueva búsqueda
const newSearch = () => {
    formMode.value = 'search';
    searchMessage.value = '';
    searchMessageType.value = 'info';
    canEditPersonData.value = false;
    foundPerson.value = null;
    searchDocument.value = '';
    searchDocumentType.value = '';
    form.reset();
};

// Volver a la lista
const goBack = () => {
    router.visit(route('admin.users.index'));
};

// Buscar persona por documento
const searchByDocument = async () => {
    if (!searchDocument.value.trim() || !searchDocumentType.value) {
        searchMessage.value = 'Por favor ingresa el tipo y número de documento';
        searchMessageType.value = 'warning';
        return;
    }

    isSearching.value = true;
    searchMessage.value = 'Buscando persona...';
    searchMessageType.value = 'info';

    try {
        const response = await axios.get(route('admin.users.search-by-document'), {
            params: {
                document_type: searchDocumentType.value,
                document_number: searchDocument.value.trim()
            },
            headers: {
                'Accept': 'application/json',
            }
        });

        const person = response.data.data;

        if (!person) {
            // Persona no existe - crear nueva
            handlePersonNotFound();
        } else if (!person.user) {
            // Persona existe pero no tiene usuario - crear usuario
            handlePersonFoundWithoutUser(person);
        } else {
            // Persona existe y ya tiene usuario - error
            handlePersonWithUserExists(person);
        }

    } catch (error: any) {
        console.error('Error en búsqueda:', error);
        searchMessage.value = 'Error al buscar en el servidor. Intenta nuevamente.';
        searchMessageType.value = 'error';
    } finally {
        isSearching.value = false;
    }
};

// Persona no encontrada - permitir crear nueva
const handlePersonNotFound = () => {
    formMode.value = 'create-new';
    searchMessage.value = 'Persona no encontrada. Se creará una nueva persona y usuario.';
    searchMessageType.value = 'info';
    
    form.reset();
    form.document_type = searchDocumentType.value;
    form.document_number = searchDocument.value.trim();
    canEditPersonData.value = true;
};

// Persona encontrada sin usuario - crear solo usuario
const handlePersonFoundWithoutUser = (person: any) => {
    formMode.value = 'create-user';
    foundPerson.value = person;
    searchMessage.value = `Persona encontrada: ${person.first_name} ${person.last_name_paternal}. Se creará el usuario para esta persona.`;
    searchMessageType.value = 'success';
    
    // Llenar formulario con datos de la persona
    Object.assign(form, person);
    canEditPersonData.value = true; // Permitir editar datos si es necesario
};

// Persona ya tiene usuario - mostrar error
const handlePersonWithUserExists = (person: any) => {
    formMode.value = 'search';
    foundPerson.value = person;
    searchMessage.value = `Esta persona ya tiene un usuario registrado: ${person.user.email}`;
    searchMessageType.value = 'error';
    
    form.reset();
    canEditPersonData.value = false;
};


const createUser = () => {
    form.clearErrors();
    
    // Determinar la ruta según el modo
    const endpoint = formMode.value === 'create-new' 
        ? route('admin.users.store-with-person')  // Crear persona y usuario
        : route('admin.users.store');              // Solo crear usuario

    form.post(endpoint, {
        onSuccess: () => {
            router.visit(route('admin.users.index'));
        },
        onError: (errors) => {
            console.error('Errores al crear usuario:', errors);
        }
    });
};
</script>

<template>

    <Head title="Crear Usuario" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto mt-6 w-full max-w-full rounded-lg border bg-card p-6 text-card-foreground shadow-sm sm:max-w-xl md:max-w-2xl lg:max-w-4xl">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Crear Nuevo Usuario</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Completa el formulario para agregar un nuevo usuario al sistema.
                </p>
            </div>

            <!-- PASO 1: Búsqueda por documento -->
            <div class="mb-6" v-if="formMode === 'search'">
                <h2 class="text-lg font-semibold mb-4">Buscar Persona</h2>
                
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Busca si la persona ya está registrada o 
                        <button @click="createWithoutSearch" type="button" 
                                class="text-blue-600 hover:text-blue-500 underline">
                            crea un nuevo registro directamente
                        </button>
                    </p>
                </div>

                <!-- Grid responsive -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end mb-5">
                    <!-- Tipo de documento -->
                    <div class="sm:col-span-1 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Tipo
                        </label>
                        <select v-model="searchDocumentType" :disabled="isSearching"
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
                        <input type="text" v-model="searchDocument" :disabled="isSearching"
                            @keyup.enter="searchByDocument"
                            class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                            placeholder="Número de documento" />
                    </div>

                    <!-- Botón -->
                    <div class="sm:col-span-2 lg:col-span-2">
                        <Button @click="searchByDocument"
                            :disabled="isSearching || !searchDocument.trim() || !searchDocumentType" size="lg"
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white">
                            <Search class="h-4 w-4 mr-2" />
                            <span class="hidden sm:inline">{{ isSearching ? 'Buscando...' : 'Buscar' }}</span>
                            <span class="sm:hidden">{{ isSearching ? '...' : 'Buscar' }}</span>
                        </Button>
                    </div>
                </div>
            </div>
            <!-- Mensaje de estado -->
            <div v-if="searchMessage" class="mb-4 flex items-center rounded-lg border p-4 text-sm" role="alert"
                 :class="{
                     'bg-blue-50 text-blue-800 border-blue-200 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-400': searchMessageType === 'info',
                     'bg-green-50 text-green-800 border-green-200 dark:border-green-800 dark:bg-gray-800 dark:text-green-400': searchMessageType === 'success',
                     'bg-yellow-50 text-yellow-800 border-yellow-200 dark:border-yellow-800 dark:bg-gray-800 dark:text-yellow-400': searchMessageType === 'warning',
                     'bg-red-50 text-red-800 border-red-200 dark:border-red-800 dark:bg-gray-800 dark:text-red-400': searchMessageType === 'error'
                 }">
                <svg class="me-3 inline h-4 w-4 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <div>
                    <span class="font-medium">{{ searchMessage }}</span>
                </div>
            </div>
            <!-- FORMULARIO PARA CREAR USUARIO -->
            <div class="rounded-lg shadow p-6 mb-6" v-if="formMode !== 'search'">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ formMode === 'create-new' ? 'Crear Nueva Persona y Usuario' : 'Crear Usuario para Persona Existente' }}
                </h2>

                <form @submit.prevent="createUser" class="space-y-6">
                    <!-- ✅ Datos del documento - CORREGIR clases disabled -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Tipo de Documento *
                            </label>
                            <select v-model="form.document_type" :disabled="foundPerson && !canEditPersonData"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': form.errors.document_type }">
                                <option value="DNI">DNI</option>
                                <option value="CE">Carnet de Extranjería</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="RUC">RUC</option>
                            </select>
                            <span v-if="form.errors.document_type" class="text-red-500 text-sm">{{
                                form.errors.document_type
                            }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Número de Documento *
                            </label>
                            <input type="text" v-model="form.document_number"
                                :disabled="foundPerson && !canEditPersonData"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': form.errors.document_number }" placeholder="12345678" />
                            <span v-if="form.errors.document_number" class="text-red-500 text-sm">{{
                                form.errors.document_number
                            }}</span>
                        </div>

                        <!-- ✅ Género - USAR SELECT NATIVO PARA EVITAR PROBLEMAS -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Género
                            </label>
                            <select v-model="form.gender" :disabled="!canEditPersonData"
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
                            <input type="text" v-model="form.first_name" :disabled="!canEditPersonData"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': form.errors.first_name }" placeholder="Juan Carlos" />
                            <span v-if="form.errors.first_name" class="text-red-500 text-sm">{{ form.errors.first_name
                            }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Apellido Paterno *
                            </label>
                            <input type="text" v-model="form.last_name_paternal" :disabled="!canEditPersonData"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': form.errors.last_name_paternal }" placeholder="García" />
                            <span v-if="form.errors.last_name_paternal" class="text-red-500 text-sm">{{
                                form.errors.last_name_paternal }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Apellido Materno *
                            </label>
                            <input type="text" v-model="form.last_name_maternal" :disabled="!canEditPersonData"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': form.errors.last_name_maternal }" placeholder="López" />
                            <span v-if="form.errors.last_name_maternal" class="text-red-500 text-sm">{{
                                form.errors.last_name_maternal }}</span>
                        </div>
                    </div>

                    <!-- ✅ Contacto - AGREGAR clases disabled -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Email *
                            </label>
                            <input type="email" v-model="form.email" :disabled="!canEditPersonData"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                placeholder="juan@ejemplo.com" />
                            <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Teléfono
                            </label>
                            <input type="text" v-model="form.phone" :disabled="!canEditPersonData"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                placeholder="987654321" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Fecha de Nacimiento
                            </label>
                            <input type="date" v-model="form.birth_date" :disabled="!canEditPersonData"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600" />
                        </div>
                    </div>

                    <!-- ✅ Dirección - AGREGAR clases disabled -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Dirección
                        </label>
                        <textarea v-model="form.address" rows="3" :disabled="!canEditPersonData"
                            class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                            placeholder="Av. Principal 123, Distrito, Provincia"></textarea>
                    </div>

                    <!-- ✅ CAMPOS DE CONTRASEÑA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Contraseña *
                            </label>
                            <input type="password" v-model="form.password"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.password }" placeholder="Mínimo 8 caracteres" />
                            <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password
                                }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Confirmar Contraseña *
                            </label>
                            <input type="password" v-model="form.password_confirmation"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.password_confirmation }"
                                placeholder="Repetir contraseña" />
                            <span v-if="form.errors.password_confirmation" class="text-red-500 text-sm">{{
                                form.errors.password_confirmation }}</span>
                        </div>
                    </div>
                    <!-- BOTONES CON ESTILOS MEJORADOS -->
                    <div
                        class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">

                        <!-- Botón Cancelar -->
                        <Button type="button" @click="goBack" variant="outline" size="lg" class="w-full sm:w-auto">
                            <ArrowLeft class="h-4 w-4 mr-2" />
                            Cancelar
                        </Button>
                        <!-- Botón Nueva búsqueda -->
                        <Button type="button" @click="newSearch" variant="outline" size="lg"
                            class="w-full sm:w-auto" v-if="formMode === 'create-new' || formMode === 'create-user'">
                            <Search class="h-4 w-4 mr-2" />
                            Nueva búsqueda
                        </Button>



                        <!-- Botón Crear/Actualizar -->
                        <Button type="submit" :disabled="form.processing" size="lg"
                            class="w-full sm:w-auto bg-green-600 hover:bg-green-500 text-white disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <UserPlus class="h-4 w-4 mr-2" />
                            <span v-if="form.processing">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Procesando...
                            </span>
                            <span v-else-if="formMode === 'create-user'">Crear Usuario</span>
                            <span v-else>Crear Persona y Usuario</span>
                        </Button>


                    </div>
                </form>
            </div>


        </div>
    </AppLayout>
</template>
