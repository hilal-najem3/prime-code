<script setup lang="ts">
import { computed } from "vue";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
type Variant = "default" | "elevated" | "flat" | "white";
type Padding = "none" | "sm" | "md" | "lg";

const props = defineProps<{
  variant?: Variant;
  padding?: Padding;
}>();

/*
|--------------------------------------------------------------------------
| Variant Styles
|--------------------------------------------------------------------------
*/
const variants: Record<Variant, string> = {
  default: "bg-surface-primary border border-border",
  elevated:
    "bg-surface-primary border border-border shadow-[0_10px_40px_rgba(0,0,0,0.3)]",
  flat: "bg-transparent border border-border",
  white: "bg-white border border-border",
};

/*
|--------------------------------------------------------------------------
| Padding Styles
|--------------------------------------------------------------------------
*/
const paddings: Record<Padding, string> = {
  none: "p-0",
  sm: "p-4",
  md: "p-6",
  lg: "p-8",
};

/*
|--------------------------------------------------------------------------
| Classes
|--------------------------------------------------------------------------
*/
const classes = computed(() => [
  "rounded-2xl transition",
  variants[props.variant || "default"],
  paddings[props.padding || "md"],
]);
</script>

<template>
  <div :class="classes">
    <slot />
  </div>
</template>
