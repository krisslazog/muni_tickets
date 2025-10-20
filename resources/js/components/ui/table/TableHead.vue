<script setup lang="ts">
import { inject } from 'vue';

defineProps<{
    sticky?: boolean;
}>();

const tableId = inject('tableId', '');
</script>

<template>
    <thead :class="[
        sticky ? 'sticky top-0 z-10' : '',
    ]">
        <slot></slot>
    </thead>
</template>
