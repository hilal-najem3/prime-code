<script setup lang="ts">
import { ref, watch } from "vue";
import { mediaApi } from "@core/api/services/media";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  modelValue: boolean;
  media: any | null;

  translations?: {
    filename?: string;
    type?: string;
    size?: string;
    usage?: string;
    loadingUsage?: string;
    notUsed?: string;

    delete?: string;
    close?: string;

    cannotDelete?: string;
    confirmDelete?: string;
  };
}>();

/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/
const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "deleted"): void;
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/
const loading = ref(false);
const usage = ref<any[]>([]);

/*
|--------------------------------------------------------------------------
| Load Usage
|--------------------------------------------------------------------------
*/
const loadUsage = async () => {
  if (!props.media) return;

  loading.value = true;

  try {
    usage.value = await mediaApi.usage(props.media.id);
  } finally {
    loading.value = false;
  }
};

watch(
  () => props.media,
  () => {
    if (props.media) loadUsage();
  },
  { immediate: true },
);

/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/
const close = () => {
  emit("update:modelValue", false);
};

const handleDelete = async () => {
  if (!props.media) return;

  // Prevent delete if used
  if (usage.value.length > 0) {
    alert(props.translations?.cannotDelete);
    return;
  }

  // Confirm delete
  const confirmed = confirm(props.translations?.confirmDelete || "");
  if (!confirmed) return;

  await mediaApi.delete(props.media.id);

  emit("deleted");
  close();
};
</script>

<template>
  <div v-if="modelValue" class="space-y-4">
    <!-- Image -->
    <div class="rounded-lg overflow-hidden border border-border">
      <img
        :src="media?.url"
        class="w-full max-h-[400px] object-contain bg-black"
      />
    </div>

    <!-- Info -->
    <div class="text-sm space-y-1">
      <p>
        <strong>{{ translations?.filename }}:</strong>
        {{ media?.filename }}
      </p>

      <p>
        <strong>{{ translations?.type }}:</strong>
        {{ media?.mime_type }}
      </p>

      <p>
        <strong>{{ translations?.size }}:</strong>
        {{ media?.size }}
      </p>
    </div>

    <!-- Usage -->
    <div>
      <p class="font-medium mb-2">
        {{ translations?.usage }}
      </p>

      <div v-if="loading" class="text-text-secondary">
        {{ translations?.loadingUsage }}
      </div>

      <div v-else-if="!usage.length" class="text-text-secondary">
        {{ translations?.notUsed }}
      </div>

      <div v-else class="space-y-1 text-sm">
        <div
          v-for="u in usage"
          :key="u.media_id"
          class="border border-border rounded p-2"
        >
          {{ u.model_type }} #{{ u.model_id }} ({{ u.collection }})
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-between">
      <button
        @click="handleDelete"
        class="px-4 py-2 rounded-lg bg-state-danger text-white"
      >
        {{ translations?.delete }}
      </button>

      <button
        @click="close"
        class="px-4 py-2 rounded-lg bg-surface-primary border border-border"
      >
        {{ translations?.close }}
      </button>
    </div>
  </div>
</template>
