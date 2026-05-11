<template>
  <div class="space-y-3" :class="{ 'form-field--error': hasError }">
    <slot />

    <div v-if="errorMessages.length > 0" class="space-y-1">
      <p
        v-for="(message, index) in errorMessages"
        :key="index"
        class="text-sm text-state-danger"
      >
        {{ message }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
  error?: string | string[] | null;
}>();

const errorMessages = computed(() => {
  if (!props.error) {
    return [];
  }

  if (Array.isArray(props.error)) {
    return props.error.filter(Boolean);
  }

  return [String(props.error)];
});

const hasError = computed(() => errorMessages.value.length > 0);
</script>

<style scoped>
.form-field--error :deep(input),
.form-field--error :deep(textarea),
.form-field--error :deep(select) {
  border-color: #ef4444;
}

.form-field--error :deep(input:focus),
.form-field--error :deep(textarea:focus),
.form-field--error :deep(select:focus) {
  border-color: #ef4444;
  --tw-ring-color: #ef4444;
}
</style>
