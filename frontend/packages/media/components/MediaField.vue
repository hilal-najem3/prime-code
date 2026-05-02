<script setup lang="ts">
// path to: frontend/packages/media/components/MediaField.vue
import { ref, computed } from "vue";
import { Modal } from "@ui";
import { MediaPicker } from "@media";
import type { Media } from "../types/media";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  modelValue: Media[] | Media | null;

  multiple?: boolean;
  collection?: string;

  mode?: "select" | "attach";

  attach?: {
    model_type: string;
    model_id: number;
  };

  label?: string;
}>();

/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/
const emit = defineEmits<{
  (e: "update:modelValue", value: any): void;
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/
const open = ref(false);

/*
|--------------------------------------------------------------------------
| Normalize Value
|--------------------------------------------------------------------------
*/
const value = computed<Media[]>({
  get() {
    if (!props.modelValue) return [];

    return Array.isArray(props.modelValue)
      ? props.modelValue
      : [props.modelValue];
  },
  set(val) {
    emit("update:modelValue", props.multiple ? val : val[0] || null);
  },
});

/*
|--------------------------------------------------------------------------
| Open Picker
|--------------------------------------------------------------------------
*/
const openPicker = () => {
  open.value = true;
};

/*
|--------------------------------------------------------------------------
| Handle Selection / Attach
|--------------------------------------------------------------------------
*/
const handleSelect = (val: any) => {
  if (!val) return;

  // Attach mode → already returns Media[]
  if (props.mode === "attach") {
    value.value = Array.isArray(val) ? val : [val];
  } else {
    // Select mode → returns IDs (not recommended here)
    // ignore or fetch later if needed
    value.value = [];
  }

  open.value = false;
};

/*
|--------------------------------------------------------------------------
| Remove Media
|--------------------------------------------------------------------------
*/
const remove = (id: number) => {
  value.value = value.value.filter((m) => m.id !== id);
};

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/
const isImage = (mime: string) => {
  return mime?.startsWith("image/");
};
</script>

<template>
  <div class="space-y-2">
    <!-- Label -->
    <label v-if="label" class="text-sm font-medium">
      {{ label }}
    </label>

    <!-- Preview List -->
    <div class="flex flex-wrap gap-3">
      <div
        v-for="item in value"
        :key="item.id"
        class="relative w-24 h-24 border rounded overflow-hidden"
      >
        <!-- Image -->
        <img
          v-if="isImage(item.mime_type)"
          :src="item?.variants?.thumb || item?.url || ''"
          class="w-full h-full object-cover"
        />

        <!-- Non-image -->
        <div
          v-else
          class="flex items-center justify-center text-xs text-text-secondary"
        >
          {{ item.extension }}
        </div>

        <!-- Remove -->
        <button
          @click="remove(item.id)"
          class="absolute top-1 right-1 bg-black/70 text-white text-xs px-1 rounded"
        >
          ✕
        </button>
      </div>

      <!-- Add Button -->
      <button
        @click="openPicker"
        class="w-24 h-24 border border-dashed rounded flex items-center justify-center text-sm text-text-secondary"
      >
        +
      </button>
    </div>

    <!-- Picker Modal -->
    <Modal v-model="open">
      <MediaPicker
        :modelValue="value"
        :multiple="multiple"
        :collection="collection"
        :mode="mode"
        :attach="attach"
        @update:modelValue="handleSelect"
      />
    </Modal>
  </div>
</template>
