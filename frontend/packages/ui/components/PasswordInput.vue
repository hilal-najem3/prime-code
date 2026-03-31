<script setup lang="ts">
import { ref } from "vue";

defineProps<{ modelValue: string }>();

const emit = defineEmits<{
  (e: "update:modelValue", value: string): void;
}>();

const show = ref(false);

const onInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  emit("update:modelValue", target.value);
};
</script>

<template>
  <div class="relative w-full">
    <input
      :type="show ? 'text' : 'password'"
      :value="modelValue"
      @input="onInput"
      class="w-full px-4 py-2 rounded-lg bg-bg-primary border border-border text-text-primary placeholder-text-muted focus:ring-2 focus:ring-brand-primary"
    />

    <button
      type="button"
      @click="show = !show"
      class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-gray-700"
    >
      {{ show ? "Hide" : "Show" }}
    </button>
  </div>
</template>
