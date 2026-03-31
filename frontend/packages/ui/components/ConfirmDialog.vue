<script setup lang="ts">
import { Modal, Button } from "@ui";

const props = defineProps<{
  modelValue: boolean;
  title?: string;
  message?: string;
  confirmText?: string;
  cancelText?: string;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "confirm"): void;
}>();

const close = () => {
  emit("update:modelValue", false);
};

const confirm = () => {
  emit("confirm");
  close();
};
</script>

<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-4">
      <!-- Title -->
      <h2 class="text-lg font-semibold text-text-primary">
        {{ title || "Confirm Action" }}
      </h2>

      <!-- Message -->
      <p class="text-text-secondary">
        {{ message || "Are you sure you want to continue?" }}
      </p>

      <!-- Actions -->
      <div class="flex justify-end gap-3 pt-4">
        <Button variant="secondary" @click="close">
          {{ cancelText || "Cancel" }}
        </Button>

        <Button variant="danger" @click="confirm">
          {{ confirmText || "Confirm" }}
        </Button>
      </div>
    </div>
  </Modal>
</template>
