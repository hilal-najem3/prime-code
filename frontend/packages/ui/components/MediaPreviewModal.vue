<script setup lang="ts">
import { ref, watch } from "vue";
import { mediaApi } from "@core/api/services/media";

const props = defineProps<{
  modelValue: boolean;
  media: any | null;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "deleted"): void;
}>();

const loading = ref(false);
const usage = ref<any[]>([]);

/**
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

/**
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/
const handleDelete = async () => {
  if (!props.media) return;

  if (usage.value.length > 0) {
    alert("Cannot delete: media is in use");
    return;
  }

  if (!confirm("Delete this media?")) return;

  await mediaApi.delete(props.media.id);

  emit("deleted");
  emit("update:modelValue", false);
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
      <p><strong>Filename:</strong> {{ media?.filename }}</p>
      <p><strong>Type:</strong> {{ media?.mime_type }}</p>
      <p><strong>Size:</strong> {{ media?.size }}</p>
    </div>

    <!-- Usage -->
    <div>
      <p class="font-medium mb-2">Usage</p>

      <div v-if="loading" class="text-text-secondary">Loading usage...</div>

      <div v-else-if="!usage.length" class="text-text-secondary">
        Not used anywhere
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
        Delete
      </button>

      <button
        @click="emit('update:modelValue', false)"
        class="px-4 py-2 rounded-lg bg-surface-primary border border-border"
      >
        Close
      </button>
    </div>
  </div>
</template>
