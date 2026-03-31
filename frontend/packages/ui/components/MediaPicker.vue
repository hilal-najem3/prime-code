<script setup lang="ts">
import { ref, onMounted, computed, watch, onUnmounted } from "vue";
import { mediaApi } from "@core/api/services/media";
import MediaPreviewModal from "./MediaPreviewModal.vue";
import { Modal } from "@ui";

/*
|--------------------------------------------------------------------------
| Props / Emits
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  modelValue: number | number[] | null;
  multiple?: boolean;
  collection?: string;

  translations?: {
    dragDrop?: string;
    search?: string;
    upload?: string;
    clear?: string;
    loadingMore?: string;
    selected?: string;
    cancel?: string;
    select?: string;
  };
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: any): void;
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/
const items = ref<any[]>([]);
const loading = ref(false);
const loadingMore = ref(false);

const search = ref("");

const page = ref(1);
const lastPage = ref(1);

const containerRef = ref<HTMLElement | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const dragActive = ref(false);

/*
|--------------------------------------------------------------------------
| Upload Progress State
|--------------------------------------------------------------------------
*/
const uploads = ref<{ id: string; name: string; progress: number }[]>([]);

/*
|--------------------------------------------------------------------------
| Selection
|--------------------------------------------------------------------------
*/
const selected = ref<number[]>(
  Array.isArray(props.modelValue)
    ? props.modelValue
    : props.modelValue
      ? [props.modelValue]
      : [],
);

const selectedCount = computed(() => selected.value.length);

/*
|--------------------------------------------------------------------------
| Sync external modelValue
|--------------------------------------------------------------------------
*/
watch(
  () => props.modelValue,
  (val) => {
    selected.value = Array.isArray(val) ? val : val ? [val] : [];
  },
);

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

const handleDeleted = () => {
  items.value = items.value.filter((i) => i.id !== previewMedia.value?.id);
};

/*
|--------------------------------------------------------------------------
| Fetch Media
|--------------------------------------------------------------------------
*/
const fetchMedia = async (reset = false) => {
  if (loading.value || loadingMore.value) return;

  if (reset) {
    page.value = 1;
    items.value = [];
  }

  loading.value = reset;
  loadingMore.value = !reset;

  try {
    const res = await mediaApi.search(
      search.value,
      props.collection,
      page.value,
    );

    items.value = [...items.value, ...res.data];
    lastPage.value = res.meta?.last_page || 1;
  } finally {
    loading.value = false;
    loadingMore.value = false;
  }
};

/*
|--------------------------------------------------------------------------
| Infinite Scroll
|--------------------------------------------------------------------------
*/
const onScroll = () => {
  const el = containerRef.value;
  if (!el) return;

  const nearBottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 50;

  if (nearBottom && page.value < lastPage.value) {
    page.value++;
    fetchMedia();
  }
};

/*
|--------------------------------------------------------------------------
| Upload Helpers
|--------------------------------------------------------------------------
*/
const createUpload = (file: File) => {
  const id = `${file.name}-${Date.now()}`;

  uploads.value.push({
    id,
    name: file.name,
    progress: 0,
  });

  return id;
};

const updateUpload = (id: string, progress: number) => {
  const u = uploads.value.find((u) => u.id === id);
  if (u) u.progress = progress;
};

const removeUpload = (id: string) => {
  uploads.value = uploads.value.filter((u) => u.id !== id);
};

/*
|--------------------------------------------------------------------------
| Upload (Single)
|--------------------------------------------------------------------------
*/
const triggerUpload = () => {
  fileInput.value?.click();
};

const handleUpload = async (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (!target.files?.length) return;

  const file = target.files[0];
  const id = createUpload(file);

  try {
    const uploaded = await mediaApi.upload(
      file,
      "media",
      props.collection,
      (progress) => updateUpload(id, progress),
    );

    items.value.unshift(uploaded);
  } finally {
    removeUpload(id);
    target.value = "";
  }
};

/*
|--------------------------------------------------------------------------
| Upload (Multi)
|--------------------------------------------------------------------------
*/
const uploadMultiple = async (files: FileList) => {
  for (const file of Array.from(files)) {
    const id = createUpload(file);

    try {
      const uploaded = await mediaApi.upload(
        file,
        "media",
        props.collection,
        (progress) => updateUpload(id, progress),
      );

      items.value.unshift(uploaded);
    } finally {
      removeUpload(id);
    }
  }
};

/*
|--------------------------------------------------------------------------
| Drag & Drop
|--------------------------------------------------------------------------
*/
const onDragOver = (e: DragEvent) => {
  e.preventDefault();
  dragActive.value = true;
};

const onDragLeave = () => {
  dragActive.value = false;
};

const onDrop = async (e: DragEvent) => {
  e.preventDefault();
  dragActive.value = false;

  const files = e.dataTransfer?.files;
  if (!files?.length) return;

  await uploadMultiple(files);
};

/*
|--------------------------------------------------------------------------
| Selection
|--------------------------------------------------------------------------
*/
const toggle = (id: number) => {
  if (props.multiple) {
    const index = selected.value.indexOf(id);
    if (index > -1) selected.value.splice(index, 1);
    else selected.value.push(id);
  } else {
    selected.value = [id];
  }
};

const confirm = () => {
  emit(
    "update:modelValue",
    props.multiple ? selected.value : selected.value[0] || null,
  );
};

const clearSelection = () => {
  selected.value = [];
};

/*
|--------------------------------------------------------------------------
| Init
|--------------------------------------------------------------------------
*/
onMounted(() => fetchMedia(true));
</script>

<template>
  <div class="space-y-4">
    <!-- Drag Zone -->
    <div
      @dragover="onDragOver"
      @dragleave="onDragLeave"
      @drop="onDrop"
      class="border-2 border-dashed rounded-lg p-6 text-center transition"
      :class="
        dragActive
          ? 'border-brand-primary bg-bg-secondary'
          : 'border-border bg-bg-primary'
      "
    >
      <p class="text-text-secondary">
        {{ translations?.dragDrop }}
      </p>
    </div>

    <!-- Upload Progress -->
    <div v-if="uploads.length" class="space-y-2">
      <div
        v-for="u in uploads"
        :key="u.id"
        class="bg-bg-secondary p-2 rounded-lg"
      >
        <div class="text-xs mb-1">{{ u.name }}</div>

        <div class="w-full h-2 bg-border rounded">
          <div
            class="h-2 bg-brand-primary rounded transition-all"
            :style="{ width: u.progress + '%' }"
          ></div>
        </div>

        <div class="text-xs text-right mt-1">{{ u.progress }}%</div>
      </div>
    </div>

    <!-- Top Bar -->
    <div class="flex gap-2 items-center">
      <input
        v-model="search"
        @input="fetchMedia(true)"
        :placeholder="translations?.search"
        class="flex-1 px-4 py-2 rounded-lg bg-bg-primary border border-border text-text-primary"
      />

      <button
        @click="triggerUpload"
        class="px-4 py-2 rounded-lg bg-brand-primary text-white"
      >
        {{ translations?.upload }}
      </button>

      <button
        v-if="selectedCount"
        @click="clearSelection"
        class="px-3 py-2 rounded-lg border border-border text-text-secondary"
      >
        {{ translations?.clear }} ({{ selectedCount }})
      </button>

      <input
        ref="fileInput"
        type="file"
        class="hidden"
        @change="handleUpload"
      />
    </div>

    <!-- Grid -->
    <div
      ref="containerRef"
      @scroll="onScroll"
      class="grid grid-cols-2 md:grid-cols-4 gap-4 max-h-[400px] overflow-auto"
    >
      <div
        v-for="item in items"
        :key="item.id"
        class="relative rounded-lg overflow-hidden border border-border hover:scale-105 transition cursor-pointer"
        :class="{ 'ring-2 ring-brand-primary': selected.includes(item.id) }"
      >
        <img
          :src="item.url"
          class="w-full h-32 object-cover"
          @click.stop="openPreview(item)"
        />

        <div
          @click.stop="toggle(item.id)"
          class="absolute top-2 right-2 w-6 h-6 rounded-full flex items-center justify-center text-xs"
          :class="
            selected.includes(item.id)
              ? 'bg-brand-primary text-white'
              : 'bg-black/50 text-white'
          "
        >
          ✔
        </div>

        <div
          class="absolute bottom-0 left-0 right-0 bg-black/50 text-xs p-1 truncate"
        >
          {{ item.filename }}
        </div>
      </div>

      <div
        v-if="loadingMore"
        class="col-span-full text-center text-text-secondary py-2"
      >
        {{ translations?.loadingMore }}
      </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
      <div class="text-sm text-text-secondary">
        {{ selectedCount }} {{ translations?.selected }}
      </div>

      <div class="flex gap-2">
        <button
          class="px-4 py-2 rounded-lg bg-surface-primary border border-border"
        >
          {{ translations?.cancel }}
        </button>

        <button
          @click="confirm"
          class="px-4 py-2 rounded-lg bg-brand-primary text-white"
        >
          {{ translations?.select }}
        </button>
      </div>
    </div>

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
