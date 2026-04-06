<script setup lang="ts">
import { ref } from "vue";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  modelValue: string;
  placeholder?: string;

  translations?: {
    show?: string;
    hide?: string;
  };
}>();

/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/
const emit = defineEmits<{
  (e: "update:modelValue", value: string): void;
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/
const show = ref(false);

/*
|--------------------------------------------------------------------------
| Handlers
|--------------------------------------------------------------------------
*/
const onInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  emit("update:modelValue", target.value);
};
</script>

<template>
  <div class="relative w-full">
    <input
      v-bind="$attrs"
      :type="show ? 'text' : 'password'"
      :value="modelValue"
      @input="onInput"
      :placeholder="placeholder"
      class="w-full px-4 py-2 rounded-lg bg-bg-primary border border-border text-text-primary placeholder-text-muted focus:ring-2 focus:ring-brand-primary"
    />

    <button
      type="button"
      @click="show = !show"
      class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-gray-700"
    >
      {{ show ? translations?.hide : translations?.show }}
    </button>
  </div>
</template>
