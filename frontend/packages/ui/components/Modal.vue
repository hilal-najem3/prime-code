<script setup lang="ts">
import { onMounted, onUnmounted } from "vue";

const props = defineProps<{
  modelValue: boolean;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
}>();

const close = () => {
  emit("update:modelValue", false);
};

/**
|--------------------------------------------------------------------------
| ESC Key Close
|--------------------------------------------------------------------------
*/
const handleKey = (e: KeyboardEvent) => {
  if (e.key === "Escape") close();
};

onMounted(() => {
  window.addEventListener("keydown", handleKey);
});

onUnmounted(() => {
  window.removeEventListener("keydown", handleKey);
});
</script>

<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 flex items-center justify-center"
  >
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="close" />

    <!-- Modal -->
    <div
      class="relative min-w-1/2 min-h-1/2 rounded-2xl bg-surface-primary border border-border shadow-xl p-6 z-10"
    >
      <slot />
    </div>
  </div>
</template>
