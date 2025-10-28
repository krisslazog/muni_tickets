// ...existing code...
<script setup lang="ts">
import { computed } from 'vue';
import {
    Pagination,
    PaginationNext,
    PaginationPrevious,
    PaginationContent,
    PaginationItem,
} from '@/components/ui/pagination';

type Link = {
    url: string | null;
    label: string;
    page: number | null;
    active: boolean;
};

// Props tipadas con TypeScript
const props = defineProps<{
    links?: Link[];                 // puede venir undefined temporalmente
    prevPageUrl?: string | null;    // datos del backend pueden traer null
    nextPageUrl?: string | null;
    itemsPerPage?: number;
    totalItems?: number;
}>();

// Emit tipado
const emit = defineEmits<{ (e: 'change-page', url?: string | null): void }>();

const goToPage = (url?: string | null) => {
    emit('change-page', url);
};

// Computed que filtra los links para mostrar solo numeración (sin previous/next)
const pageLinks = computed<Link[]>(() => {
    const arr = props.links ?? [];
    return arr.filter(link => {
        if (!link || link.label == null) return false;
        const lbl = String(link.label).toLowerCase();
        // excluir etiquetas que indiquen previous/next o entidades &laquo; &raquo; / « »
        return !(/previous|next|laquo|raquo|«|»/.test(lbl));
    });
});
</script>

<template>
    <Pagination :items-per-page="props.itemsPerPage ?? 10" :total="props.totalItems ?? 0">
        <PaginationContent>
            <PaginationPrevious :disabled="!props.prevPageUrl"
                @click="props.prevPageUrl ? goToPage(props.prevPageUrl) : null" aria-label="Página anterior">
                « Anterior
            </PaginationPrevious>

            <template v-for="(link, index) in pageLinks" :key="index">
                <!-- si page == null mostramos ellipsis, si no mostramos el item con value numérico -->
                <PaginationEllipsis v-if="link.page == null">
                    {{ link.label }}
                </PaginationEllipsis>

                <PaginationItem v-else :value="link.page" :is-active="link.active" :disabled="!link.url"
                    @click="link.url ? goToPage(link.url) : null">
                    {{ link.label }}
                </PaginationItem>
            </template>

            <PaginationNext :disabled="!props.nextPageUrl"
                @click="props.nextPageUrl ? goToPage(props.nextPageUrl) : null" aria-label="Página siguiente">
                Siguiente »
            </PaginationNext>
        </PaginationContent>
    </Pagination>
</template>
