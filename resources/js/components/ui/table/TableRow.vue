<script setup lang="ts">
defineProps<{
    striped?: boolean;
    hover?: boolean;
    selected?: boolean;
}>();
</script>

<template>
    <tr :class="[
        striped ? 'even:bg-gray-50 even:dark:bg-gray-800' : '',
        hover ? 'hover:bg-gray-100 dark:hover:bg-gray-700' : '',
        selected ? 'bg-blue-50 dark:bg-blue-900' : ''
    ]">
        <slot></slot>
    </tr>
</template>
