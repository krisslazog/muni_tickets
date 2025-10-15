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
const personFound = ref(true);           // Almacena los datos de la persona encontrada (null si no existe)
const searchCompleted = ref(false);      // Indica si se completó el proceso de búsqueda exitosamente
const messageSearch = ref('');           // Mensaje informativo para mostrar al usuario

// Estados de bloqueo de campos
const fieldsLocked = ref(false);         // Bloquea TODOS los campos del formulario (cuando persona existe)
const documentFieldsLocked = ref(false); // Bloquea SOLO tipo y número de documento (cuando persona no existe)
const errors = ref({});                 // Errores de validación del formulario
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


const newSearch = () => {
    // Reiniciar estados y formularios
    searchDocument.value = '';
    searchDocumentType.value = 'DNI';
    searchLoading.value = false;
    personFound.value = false;
    searchCompleted.value = false;
    messageSearch.value = '';
    fieldsLocked.value = false;
    documentFieldsLocked.value = false;
    form.user_id = null;
    form.id = null;
    form.reset();
    form.clearErrors();
};

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

        //si no trae persona ni usuario
        if (response.data.success && !response.data.data?.person && !response.data.data?.person.user) {
            messageSearch.value = `No se encontro la persona, por favor registre los campos`;
            form.reset();
            form.document_type = searchDocumentType.value;
            form.document_number = searchDocument.value.trim();
            documentFieldsLocked.value = true;
            fieldsLocked.value = false;
            personFound.value = false;
        }
        if (response.data.success && response.data.data?.person && !response.data.data?.person.user) {
            personFound.value = response.data.data;
            // Llenar el formulario de persona con los datos encontrados
            Object.assign(form, response.data.data.person);
            messageSearch.value = `Persona encontrada, si desea puede actualizar los datos o continuar con el registro del usuario.`;
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

        //personFound.value = null;
    } finally {
        //searchLoading.value = false;
    }
};


const createPerson = () => {
    form.clearErrors();
    //guardar persona y usuario
    form.post(route('admin.users.store-with-person'), {
        onSuccess: () => {
            router.visit(route('admin.users.index'));
        },
        onError: (errors) => {
            console.error('Errores:', errors);
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
            <div class="rounded-lg shadow p-6 mb-6" v-if="!searchLoading">
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
            <div class="rounded-lg shadow p-6 mb-6" v-if="searchLoading">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Crear Nueva Persona</h2>

                <form @submit.prevent="createPerson" class="space-y-6">
                    <!-- ✅ Datos del documento - CORREGIR clases disabled -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Tipo de Documento *
                            </label>
                            <select v-model="form.document_type" :disabled="fieldsLocked || documentFieldsLocked"
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
                                :disabled="fieldsLocked || documentFieldsLocked"
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
                            <select v-model="form.gender" :disabled="fieldsLocked"
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
                            <input type="text" v-model="form.first_name" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': form.errors.first_name }" placeholder="Juan Carlos" />
                            <span v-if="form.errors.first_name" class="text-red-500 text-sm">{{ form.errors.first_name
                            }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Apellido Paterno *
                            </label>
                            <input type="text" v-model="form.last_name_paternal" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                :class="{ 'border-red-500': form.errors.last_name_paternal }" placeholder="García" />
                            <span v-if="form.errors.last_name_paternal" class="text-red-500 text-sm">{{
                                form.errors.last_name_paternal }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Apellido Materno *
                            </label>
                            <input type="text" v-model="form.last_name_maternal" :disabled="fieldsLocked"
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
                            <input type="email" v-model="form.email" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                placeholder="juan@ejemplo.com" />
                            <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Teléfono
                            </label>
                            <input type="text" v-model="form.phone" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600"
                                placeholder="987654321" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Fecha de Nacimiento
                            </label>
                            <input type="date" v-model="form.birth_date" :disabled="fieldsLocked"
                                class="dark:shadow-xs-light block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-200 dark:disabled:bg-gray-600" />
                        </div>
                    </div>

                    <!-- ✅ Dirección - AGREGAR clases disabled -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Dirección
                        </label>
                        <textarea v-model="form.address" rows="3" :disabled="fieldsLocked"
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
                            v-if="fieldsLocked || documentFieldsLocked" class="w-full sm:w-auto">
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
                            <span v-else-if="personFound">Actualizar y Continuar</span>
                            <span v-else>Crear Persona y Continuar</span>
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
                    Ahora puedes completar los datos del usuario para la persona: <strong>{{ form.first_name }}</strong>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
