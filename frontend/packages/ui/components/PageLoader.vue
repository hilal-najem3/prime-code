<template>
  <div
    v-if="isLoading"
    class="fixed inset-0 z-50 flex items-center justify-center bg-bg-primary/60 backdrop-blur-sm"
  >
    <div
      class="w-12 h-12 border-4 border-border border-t-brand-primary rounded-full animate-spin"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { useRequestTracker } from "@core/api/requestTracker";

const { isLoading } = useRequestTracker();
</script>
