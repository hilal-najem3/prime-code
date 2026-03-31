<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

const props = defineProps<{
  modelValue: string | null;
  placeholder?: string;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: string | null): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);
let instance: flatpickr.Instance | null = null;

onMounted(() => {
  if (!inputRef.value) return;

  instance = flatpickr(inputRef.value, {
    dateFormat: "Y-m-d",
    defaultDate: props.modelValue ?? undefined,

    onChange: (selectedDates, dateStr) => {
      emit("update:modelValue", dateStr || null);
    },
  });
});

watch(
  () => props.modelValue,
  (newVal) => {
    if (instance) {
      instance.setDate(newVal || "", true);
    }
  },
);
</script>

<template>
  <input
    ref="inputRef"
    type="text"
    :placeholder="placeholder"
    class="w-full px-4 py-2 rounded-lg bg-bg-primary border border-border text-text-primary placeholder-text-muted focus:outline-none focus:ring-2 focus:ring-brand-primary transition"
  />
</template>
