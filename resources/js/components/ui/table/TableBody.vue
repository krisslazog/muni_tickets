<script setup lang="ts">
defineProps<{
  loading?: boolean;
  loadingRows?: number;
}>();
</script>

<template>
  <tbody>
    <template v-if="loading">
      <tr v-for="i in loadingRows || 5" :key="`loading-${i}`" class="animate-pulse">
        <td
          v-for="j in 5"
          :key="`loading-cell-${j}`"
          class="px-6 py-4"
        >
          <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-full"></div>
        </td>
      </tr>
    </template>
    <slot v-else></slot>
  </tbody>
</template>
