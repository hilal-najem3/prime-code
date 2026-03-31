<script setup lang="ts">
const props = defineProps<{
  variant?: "default" | "success" | "danger" | "warning";
}>();

const variants: Record<string, string> = {
  default: "bg-surface-primary text-text-primary",
  success: "bg-state-success text-white",
  danger: "bg-state-danger text-white",
  warning: "bg-state-warning text-black",
};
</script>

<template>
  <span
    class="px-2 py-1 text-xs rounded font-medium"
    :class="variants[variant || 'default']"
  >
    <slot />
  </span>
</template>
