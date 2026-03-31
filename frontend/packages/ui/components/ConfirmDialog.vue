<script setup lang="ts">
import { Modal, Button } from "@ui";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  modelValue: boolean;
  title?: string;
  message?: string;
  confirmText?: string;
  cancelText?: string;
  loading?: boolean;
}>();

/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/
const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "confirm"): void;
}>();

/*
|--------------------------------------------------------------------------
| Methods
|--------------------------------------------------------------------------
*/
const close = () => {
  emit("update:modelValue", false);
};

const confirm = () => {
  emit("confirm");
};
</script>

<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-4">
      <!-- Title -->
      <h2 v-if="title" class="text-lg font-semibold text-text-primary">
        {{ title }}
      </h2>

      <!-- Message -->
      <p v-if="message" class="text-text-secondary">
        {{ message }}
      </p>

      <!-- Actions -->
      <div class="flex justify-end gap-3 pt-4">
        <Button variant="secondary" @click="close">
          {{ cancelText }}
        </Button>

        <Button variant="danger" :loading="loading" @click="confirm">
          {{ confirmText }}
        </Button>
      </div>
    </div>
  </Modal>
</template>
