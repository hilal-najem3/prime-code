<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
  page: number;
  perPage: number;
  total: number;
}>();

const emit = defineEmits<{
  (e: "change", page: number): void;
}>();

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(props.total / props.perPage));
});

const changePage = (page: number) => {
  if (page < 1 || page > totalPages.value) return;
  emit("change", page);
};
</script>

<template>
  <div class="flex justify-between items-center text-sm text-text-secondary">
    <div>Page {{ page }} of {{ totalPages }}</div>

    <div class="flex gap-2">
      <button
        @click="changePage(page - 1)"
        :disabled="page <= 1"
        class="px-3 py-1 rounded border border-border disabled:opacity-50"
      >
        Prev
      </button>

      <button
        @click="changePage(page + 1)"
        :disabled="page >= totalPages"
        class="px-3 py-1 rounded border border-border disabled:opacity-50"
      >
        Next
      </button>
    </div>
  </div>
</template>
