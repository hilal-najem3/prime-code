<script setup lang="ts">
// path to: frontend/packages/media/components/MediaField.vue

import { ref, computed } from "vue";
import { Modal, Button } from "@ui";
import { MediaPicker } from "@media";
import type { Media } from "../types/media";
import { mediaApi } from "../api/media.api";
import { getMediaUrl } from "../utils/media";

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

  directory?: string;
  disk?: "public" | "private";
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

const fileInput = ref<HTMLInputElement | null>(null);

const uploading = ref(false);

/*
|--------------------------------------------------------------------------
| Normalize Media Object
|--------------------------------------------------------------------------
*/
const normalizeMedia = (media: any): Media => {
  return media?.data ?? media;
};

/*
|--------------------------------------------------------------------------
| Normalize Value
|--------------------------------------------------------------------------
*/
const value = computed<Media[]>({
  get() {
    if (!props.modelValue) {
      return [];
    }

    const arr = Array.isArray(props.modelValue)
      ? props.modelValue
      : [props.modelValue];

    return arr.map(normalizeMedia);
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
| Handle Picker Selection
|--------------------------------------------------------------------------
*/
const handleSelect = (val: any) => {
  if (!val) return;

  const normalized = (Array.isArray(val) ? val : [val]).map(normalizeMedia);

  value.value = normalized;

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
| Upload Files
|--------------------------------------------------------------------------
*/
const onFileChange = async (e: Event) => {
  const target = e.target as HTMLInputElement;

  const files = target.files ? Array.from(target.files) : [];

  if (!files.length) return;

  uploading.value = true;

  try {
    const uploaded: Media[] = [];

    for (const file of files) {
      const media = await mediaApi.upload(
        file,
        props.directory || "media",
        props.collection,
        props.disk || "public",
      );

      uploaded.push(normalizeMedia(media));
    }

    if (props.multiple) {
      value.value = [...value.value, ...uploaded];
    } else {
      value.value = uploaded.slice(0, 1);
    }
  } finally {
    uploading.value = false;

    target.value = "";
  }
};

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/
const isImage = (mime?: string | null) => {
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
          :src="getMediaUrl(item, 'thumb') || item.url"
          class="w-full h-full object-cover"
        />

        <!-- Non-image -->
        <div
          v-else
          class="w-full h-full flex items-center justify-center text-xs text-text-secondary bg-bg-secondary"
        >
          {{ item.extension || "FILE" }}
        </div>

        <!-- Remove -->
        <button
          type="button"
          @click="remove(item.id)"
          class="absolute top-1 right-1 bg-black/70 text-white text-xs px-1 rounded"
        >
          ✕
        </button>
      </div>

      <!-- Upload -->
      <div>
        <input
          ref="fileInput"
          type="file"
          class="hidden"
          :multiple="multiple"
          @change="onFileChange"
        />

        <Button
          type="button"
          variant="outline"
          :loading="uploading"
          @click="fileInput?.click()"
        >
          Upload
        </Button>
      </div>
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
