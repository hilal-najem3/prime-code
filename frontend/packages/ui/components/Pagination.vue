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
  <div class="flex justify-between items-center text-sm text-text-secondary">
    <!-- Info -->
    <div>
      {{ translations?.page }} {{ page }} {{ translations?.of }}
      {{ totalPages }}
    </div>

    <!-- Controls -->
    <div class="flex gap-2">
      <button
        @click="changePage(page - 1)"
        :disabled="page <= 1"
        class="px-3 py-1 rounded border border-border disabled:opacity-50"
      >
        {{ translations?.prev || "Previous" }}
      </button>

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
