<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
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
    person: any;
    errors: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Usuarios', href: '/admin/users' },
    { title: 'Crear Usuario', href: '/admin/users/create' },
];

// Datos de entrada para la búsqueda
// Estados de control
const personFound = ref(true);           // Almacena los datos de la persona encontrada (null si no existe)

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

watch(
    () => props.person,
    (nuevo) => {
        Object.assign(form, nuevo);
        form.user_id = nuevo.user.id;
        console.log('Se reemplazó la lista de personas:', nuevo)
    },
    { immediate: true }
)


const createPerson = () => {
    form.clearErrors();
    //guardar persona y usuario
    form.post(route('admin.users.store-with-person'), {
        onSuccess: () => {

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
        </div>
    </AppLayout>
</template>
