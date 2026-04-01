<script setup lang="ts">
import { computed } from "vue";

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/
type Variant = "primary" | "secondary" | "outline" | "danger";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  variant?: Variant;
  loading?: boolean;
  label?: string;
  loadingLabel?: string;
  fullWidth?: boolean;
}>();

/*
|--------------------------------------------------------------------------
| Base Styles
|--------------------------------------------------------------------------
*/
const base =
  "py-2 rounded-lg font-medium transition flex items-center justify-center";

/*
|--------------------------------------------------------------------------
| Variants
|--------------------------------------------------------------------------
*/
const variants: Record<Variant, string> = {
  primary:
    "bg-gradient-to-r from-brand-primary to-brand-secondary text-white hover:opacity-90",

  secondary:
    "bg-surface-primary text-text-primary border border-border hover:bg-bg-primary",

  outline: "border border-border text-text-primary hover:bg-bg-primary",

  danger: "bg-state-danger text-white hover:opacity-90",
};

/*
|--------------------------------------------------------------------------
| Classes
|--------------------------------------------------------------------------
*/
const classes = computed(() => [
  base,
  variants[props.variant || "primary"],
  props.fullWidth ? "w-full" : "",
  props.loading ? "opacity-70 cursor-not-allowed" : "",
  "px-4",
]);
</script>

<template>
  <button :class="classes" :disabled="loading">
    <!-- LOADING -->
    <span v-if="loading">
      {{ loadingLabel || "Loading..." }}
    </span>

    <!-- NORMAL -->
    <span v-else>
      <slot>
        {{ label }}
      </slot>
    </span>
  </button>
</template>
