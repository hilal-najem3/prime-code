<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useMedia } from "@media";
import { DataTable, Modal } from "@ui";
import MediaPreviewModal from "../components/MediaPreviewModal.vue";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  mode?: "tenant" | "platform";
  collection?: string;
}>();

/*
|--------------------------------------------------------------------------
| Media Composable
|--------------------------------------------------------------------------
*/
const { items, loading, search, collection, fetchMedia, remove, upload } =
  useMedia();

collection.value = props.collection;

/*
|--------------------------------------------------------------------------
| View Mode
|--------------------------------------------------------------------------
*/
const viewMode = ref<"grid" | "table">("grid");

/*
|--------------------------------------------------------------------------
| Preview Modal
|--------------------------------------------------------------------------
*/
const previewOpen = ref(false);
const previewMedia = ref<any | null>(null);

const openPreview = (item: any) => {
  previewMedia.value = item;
  previewOpen.value = true;
};

const handleDeleted = async () => {
  if (!previewMedia.value) return;
  await remove(previewMedia.value.id);
};

/*
|--------------------------------------------------------------------------
| Upload
|--------------------------------------------------------------------------
*/
const fileInput = ref<HTMLInputElement | null>(null);

const triggerUpload = () => {
  fileInput.value?.click();
};

const handleUpload = async (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (!target.files?.length) return;

  const file = target.files[0];

  await upload(file, "media", props.collection);

  target.value = "";
};

/*
|--------------------------------------------------------------------------
| DataTable Config
|--------------------------------------------------------------------------
*/
const columns = [
  { key: "id", label: "#", sortable: true },
  { key: "filename", label: "Filename", sortable: true },
  { key: "mime_type", label: "Type" },
  { key: "size", label: "Size" },
  { key: "collection", label: "Collection" },
];

const formatSize = (size: number) => {
  if (!size) return "0 B";

  const i = Math.floor(Math.log(size) / Math.log(1024));
  const sizes = ["B", "KB", "MB", "GB"];

  return (size / Math.pow(1024, i)).toFixed(2) + " " + sizes[i];
};

/*
|--------------------------------------------------------------------------
| Table Events
|--------------------------------------------------------------------------
*/
const handleChange = async (params: any) => {
  search.value = params.search || "";
  await fetchMedia(true);
};

/*
|--------------------------------------------------------------------------
| Search Debounce
|--------------------------------------------------------------------------
*/
let debounce: any;

watch(search, () => {
  clearTimeout(debounce);

  debounce = setTimeout(() => {
    fetchMedia(true);
  }, 400);
});

/*
|--------------------------------------------------------------------------
| Init
|--------------------------------------------------------------------------
*/
fetchMedia(true);
</script>

<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex justify-between items-center gap-3">
      <!-- Search -->
      <input
        v-model="search"
        placeholder="Search media..."
        class="px-4 py-2 rounded-lg border border-border bg-bg-primary text-text-primary"
      />

      <!-- Actions -->
      <div class="flex gap-2">
        <button
          @click="triggerUpload"
          class="px-4 py-2 rounded-lg bg-brand-primary text-white"
        >
          Upload
        </button>

        <button
          @click="viewMode = 'grid'"
          class="px-3 py-2 rounded border"
          :class="viewMode === 'grid' ? 'bg-brand-primary text-white' : ''"
        >
          Grid
        </button>

        <button
          @click="viewMode = 'table'"
          class="px-3 py-2 rounded border"
          :class="viewMode === 'table' ? 'bg-brand-primary text-white' : ''"
        >
          Table
        </button>

        <input
          ref="fileInput"
          type="file"
          class="hidden"
          @change="handleUpload"
        />
      </div>
    </div>

    <!-- GRID VIEW -->
    <div
      v-if="viewMode === 'grid'"
      class="grid grid-cols-2 md:grid-cols-4 gap-4"
    >
      <div
        v-for="item in items"
        :key="item.id"
        class="rounded-lg border border-border overflow-hidden cursor-pointer hover:scale-105 transition"
        @click="openPreview(item)"
      >
        <img
          :src="item.variants?.thumb || item.url"
          class="w-full h-32 object-cover bg-black"
        />

        <div class="p-2 text-xs truncate">
          {{ item.filename }}
        </div>
      </div>
    </div>

    <!-- TABLE VIEW -->
    <DataTable
      v-else
      :columns="columns"
      :data="items"
      :loading="loading"
      searchable
      remote
      @change="handleChange"
      @delete="(row) => openPreview(row)"
    >
      <!-- Size -->
      <template #cell-size="{ row }">
        {{ formatSize(row.size) }}
      </template>

      <!-- Image -->
      <template #cell-filename="{ row }">
        <div class="flex items-center gap-2">
          <img
            :src="row.variants?.thumb || row.url"
            class="w-8 h-8 object-cover rounded"
          />
          {{ row.filename }}
        </div>
      </template>
    </DataTable>

    <!-- Preview Modal -->
    <Modal v-model="previewOpen">
      <MediaPreviewModal
        v-model="previewOpen"
        :media="previewMedia"
        @deleted="handleDeleted"
      />
    </Modal>
  </div>
</template>
