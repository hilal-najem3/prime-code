<script setup lang="ts">
defineProps<{
  message: string;
  type?: "success" | "error";
}>();
</script>

<template>
  <div
    class="px-4 py-2 rounded-lg shadow-lg text-white"
    :class="{
      'bg-state-success': type === 'success',
      'bg-state-danger': type === 'error',
    }"
  >
    {{ message }}
  </div>
</template>
