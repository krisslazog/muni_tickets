<script setup lang="ts">
import { ref, provide } from 'vue';

defineProps<{
    striped?: boolean;
    hover?: boolean;
    bordered?: boolean;
    small?: boolean;
    responsive?: boolean;
    verticalAlign?: 'top' | 'middle' | 'bottom';
}>();

const tableId = ref(`table-${Math.random().toString(36).substring(2, 9)}`);

// Provide context to child components
provide('tableId', tableId);
</script>

<template>
    <div :class="[
        'w-full',
        responsive ? 'overflow-x-auto' : '',
    ]">
        <table :id="tableId" class="w-full text-sm text-left" :class="[
            'border-collapse',
            bordered ? 'border border-gray-200 dark:border-gray-700' : '',
            small ? 'text-xs' : '',
            {
                'table-fixed': $slots.colgroup,
                'align-top': verticalAlign === 'top',
                'align-middle': verticalAlign === 'middle',
                'align-bottom': verticalAlign === 'bottom',
            }
        ]">
            <!-- Cambia los slots nombrados por un slot por defecto -->
            <slot></slot>
        </table>
    </div>
</template>
