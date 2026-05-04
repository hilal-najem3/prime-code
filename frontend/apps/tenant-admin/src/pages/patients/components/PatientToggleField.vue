<script setup lang="ts">
const props = defineProps<{
  modelValue: boolean;
  label: string;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
}>();

function toggle() {
  emit("update:modelValue", !props.modelValue);
}
</script>

<template>
  <button
    type="button"
    role="switch"
    :aria-checked="modelValue ? 'true' : 'false'"
    class="w-full px-4 py-2 rounded-lg bg-bg-primary border border-border flex items-center gap-3 text-left hover:border-brand-primary transition"
    @click="toggle"
  >
    <span
      class="inline-flex h-5 w-9 shrink-0 items-center rounded-full p-0.5 transition"
      :class="modelValue ? 'bg-brand-primary' : 'bg-border'"
    >
      <span
        class="h-4 w-4 rounded-full bg-white shadow transition-transform"
        :class="modelValue ? 'translate-x-[18px]' : 'translate-x-0'"
      />
    </span>
    <span class="text-sm text-text-primary">{{ label }}</span>
  </button>
</template>
