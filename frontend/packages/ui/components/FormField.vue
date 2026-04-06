<template>
  <div class="space-y-3">
    <slot />

    <p v-if="error" class="text-sm text-state-danger">
      {{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  error?: string | null;
}>();
</script>
