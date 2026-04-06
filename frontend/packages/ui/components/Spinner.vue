<script setup lang="ts">
import { computed } from "vue";

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/
type Size = "sm" | "md" | "lg";
type Variant = "primary" | "white" | "neutral";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  size?: Size;
  variant?: Variant;
}>();

/*
|--------------------------------------------------------------------------
| Sizes
|--------------------------------------------------------------------------
*/
const sizes: Record<Size, string> = {
  sm: "w-4 h-4 border-2",
  md: "w-5 h-5 border-2",
  lg: "w-8 h-8 border-3",
};

/*
|--------------------------------------------------------------------------
| Variants
|--------------------------------------------------------------------------
*/
const variants: Record<Variant, string> = {
  primary: "border-border border-t-brand-secondary",
  white: "border-white/30 border-t-white",
  neutral: "border-border border-t-text-primary",
};

/*
|--------------------------------------------------------------------------
| Classes
|--------------------------------------------------------------------------
*/
const classes = computed(() => [
  "rounded-full animate-spin",
  sizes[props.size || "md"],
  variants[props.variant || "primary"],
]);
</script>

<template>
  <div class="flex justify-center py-6">
    <div :class="classes"></div>
  </div>
</template>
