<script setup lang="ts">
type Variant = "default" | "success" | "danger" | "warning";
type Size = "sm" | "md" | "lg";

const props = defineProps<{
  variant?: Variant;
  size?: Size;
}>();

const sizes: Record<Size, string> = {
  sm: "text-xs px-2 py-1",
  md: "text-sm px-3 py-1.5",
  lg: "text-base px-4 py-2",
};

const variants: Record<Variant, string> = {
  default: "bg-surface-primary text-text-primary",
  success: "bg-state-success text-white",
  danger: "bg-state-danger text-white",
  warning: "bg-state-warning text-black",
};
</script>

<template>
  <span
    class="px-2 py-1 text-xs rounded font-medium"
    :class="[
      'rounded font-medium',
      variants[props.variant || 'default'],
      sizes[props.size || 'sm'],
    ]"
  >
    <slot />
  </span>
</template>
