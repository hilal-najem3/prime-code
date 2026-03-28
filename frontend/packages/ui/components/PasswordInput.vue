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
      class="w-full px-4 py-2 border rounded-lg pr-10"
    />

    <button
      type="button"
      @click="show = !show"
      class="absolute right-2 top-2 text-sm"
    >
      {{ show ? "Hide" : "Show" }}
    </button>
  </div>
</template>
