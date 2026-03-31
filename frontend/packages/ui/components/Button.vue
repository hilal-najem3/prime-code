<template>
  <button :class="classes" :disabled="loading">
    <span v-if="loading" class="animate-pulse">...</span>
    <span v-else>
      <slot />
    </span>
  </button>
</template>

<script setup lang="ts">
import { computed } from "vue";

const props = defineProps({
  variant: {
    type: String,
    default: "primary",
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const base =
  "w-full py-2 rounded-lg font-medium transition flex items-center justify-center";

const variants: Record<string, string> = {
  primary:
    "bg-gradient-to-r from-brand-primary to-brand-secondary text-white hover:opacity-90",

  secondary:
    "bg-surface-primary text-text-primary border border-border hover:bg-bg-primary",

  outline: "border border-border text-text-primary hover:bg-bg-primary",

  danger: "bg-state-danger text-white hover:opacity-90",
};

const classes = computed(() => {
  return `${base} ${variants[props.variant]}`;
});
</script>
