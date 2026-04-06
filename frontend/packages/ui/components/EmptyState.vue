<template>
  <div class="text-center py-10 text-text-secondary">
    <p class="text-lg font-medium">{{ title }}</p>
    <p class="text-sm mt-1">{{ description }}</p>

    <div v-if="$slots.action" class="mt-4">
      <slot name="action" />
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string;
  description?: string;
}>();
</script>
