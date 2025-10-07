<script setup lang="ts">
defineProps<{
  header?: boolean;
  nowrap?: boolean;
  align?: 'left' | 'center' | 'right';
  colspan?: number;
  rowspan?: number;
}>();
</script>

<template>
  <th
    v-if="header"
    :class="[
      'p-3 border-b dark:border-gray-700',
      nowrap ? 'whitespace-nowrap' : '',
      align === 'center' ? 'text-center' : '',
      align === 'right' ? 'text-right' : '',
    ]"
    :colspan="colspan"
    :rowspan="rowspan"
  >
    <slot></slot>
  </th>
  <td
    v-else
    :class="[
      'p-3 border-b dark:border-gray-700',
      nowrap ? 'whitespace-nowrap' : '',
      align === 'center' ? 'text-center' : '',
      align === 'right' ? 'text-right' : '',
    ]"
    :colspan="colspan"
    :rowspan="rowspan"
  >
    <slot></slot>
  </td>
</template>
