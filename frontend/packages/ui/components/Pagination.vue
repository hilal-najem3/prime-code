<script setup lang="ts">
import { computed } from "vue";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  page: number;
  perPage: number;
  total: number;

  translations?: {
    page?: string;
    of?: string;
    prev?: string;
    next?: string;
  };
}>();

/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/
const emit = defineEmits<{
  (e: "change", page: number): void;
}>();

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/
const totalPages = computed(() => {
  return Math.max(1, Math.ceil(props.total / props.perPage));
});

const visiblePages = computed(() => {
  const total = totalPages.value;

  if (total <= 7) {
    return Array.from({ length: total }, (_, index) => index + 1);
  }

  const pages = new Set<number>([1, total, props.page - 1, props.page, props.page + 1]);

  return Array.from(pages)
    .filter((page) => page >= 1 && page <= total)
    .sort((a, b) => a - b);
});

/*
|--------------------------------------------------------------------------
| Methods
|--------------------------------------------------------------------------
*/
const changePage = (page: number) => {
  if (page < 1 || page > totalPages.value) return;
  emit("change", page);
};
</script>

<template>
  <div class="flex items-center justify-between text-sm text-text-secondary">
    <!-- Info -->
    <div>
      {{ translations?.page || "Page" }} {{ page }} {{ translations?.of || "of" }}
      {{ totalPages }}
    </div>

    <!-- Controls -->
    <div class="flex items-center gap-2">
      <button
        @click="changePage(page - 1)"
        :disabled="page <= 1"
        class="px-3 py-1 rounded border border-border disabled:opacity-50"
      >
        {{ translations?.prev || "Previous" }}
      </button>

      <div class="flex items-center gap-2">
        <button
          v-for="pageNumber in visiblePages"
          :key="pageNumber"
          @click="changePage(pageNumber)"
          :class="[
            'min-w-9 rounded border px-3 py-1 transition-colors',
            pageNumber === page
              ? 'border-brand-secondary bg-brand-secondary text-white'
              : 'border-border text-text-secondary hover:bg-bg-secondary',
          ]"
        >
          {{ pageNumber }}
        </button>
      </div>

      <button
        @click="changePage(page + 1)"
        :disabled="page >= totalPages"
        class="px-3 py-1 rounded border border-border disabled:opacity-50"
      >
        {{ translations?.next || "Next" }}
      </button>
    </div>
  </div>
</template>
