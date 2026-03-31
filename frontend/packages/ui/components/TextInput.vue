<script setup lang="ts">
const props = defineProps<{
  modelValue: string;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: string): void;
}>();

const onInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  emit("update:modelValue", target.value);
};
</script>

<template>
  <div class="w-full">
    <input
      v-bind="$attrs"
      :value="modelValue"
      @input="onInput"
      class="w-full px-4 py-2 rounded-lg bg-bg-primary border border-border text-text-primary placeholder-text-muted focus:outline-none focus:ring-2 focus:ring-brand-primary transition"
    />
  </div>
</template>
