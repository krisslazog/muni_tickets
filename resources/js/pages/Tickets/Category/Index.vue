<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps, ref, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Table, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';

// Definir las props que recibimos desde el controlador
const props = defineProps<{

    categories: any

}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categorias',
        href: '/tickets/category',
    },
];
</script>

<template>

    <Head title="Categorias" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Table hover bordered responsive>
            <TableHead sticky>
                <TableRow>
                    <TableCell header>ID</TableCell>
                    <TableCell header>Nombre</TableCell>
                    <TableCell header>Descripción</TableCell>
                    <TableCell header>Estado</TableCell>
                </TableRow>
            </TableHead>

            <TableBody>
                <TableRow v-for="category in props.categories" :key="category.id" striped hover>
                    <TableCell>{{ category.id }}</TableCell>
                    <TableCell nowrap>{{ category.name }}</TableCell>
                    <TableCell>{{ category.description }}</TableCell>
                    <TableCell>{{ category.status }}</TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </AppLayout>

</template>
