<script setup lang="ts">
import { useToast } from "../composables/useToast";

const { toasts } = useToast();
</script>

<template>
  <div class="fixed top-4 right-4 space-y-2 z-50">
    <Toast
      v-for="t in toasts"
      :key="t.id"
      :message="t.message"
      :type="t.type"
    />
  </div>
</template>
