<script setup lang="ts">
import { computed, reactive, watch } from "vue";

/**
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

type Row = Record<string, any>;

type Column = {
  key: string;
  label: string;
  sortable?: boolean;
  type?: "text" | "image" | "badge";
};

type Action = {
  label: string;
  event: string;
};

type BulkAction = {
  label: string;
  event: string;
};

type Meta = {
  current_page: number;
  per_page: number;
  total: number;
};

/**
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps<{
  columns: Column[];
  data: Row[];

  loading?: boolean;
  actions?: Action[];
  bulkActions?: BulkAction[];

  meta?: Meta;
  searchable?: boolean;
  selectable?: boolean;
  expandable?: boolean;

  translations?: {
    search?: string;
    actions?: string;
    loading?: string;
    noData?: string;
    expand?: string;
    collapse?: string;
    dataTable?: {
      page?: string;
      of?: string;
      previous?: string;
      next?: string;
    };
  };
}>();

const emit = defineEmits<{
  (e: "change", params: any): void;
  (e: string, payload: any): void;
}>();

/**
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const state = reactive({
  page: 1,
  perPage: 10,
  search: "",
  sort: "",
  direction: "asc",
  selected: [] as Row[],
  expanded: [] as number[],
});

let debounceTimer: any = null;

/**
|--------------------------------------------------------------------------
| Meta Sync
|--------------------------------------------------------------------------
*/

watch(
  () => props.meta,
  (meta) => {
    if (!meta) return;
    state.page = meta.current_page;
    state.perPage = meta.per_page;
  },
  { immediate: true },
);

/**
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const hasActions = computed(() => !!props.actions?.length);

const totalPages = computed(() => {
  if (!props.meta) return 1;
  return Math.ceil(props.meta.total / props.meta.per_page);
});

const allSelected = computed(() => {
  return (
    props.data.length > 0 &&
    props.data.every((row) => state.selected.some((r) => r.id === row.id))
  );
});

/**
|--------------------------------------------------------------------------
| Emit Change
|--------------------------------------------------------------------------
*/

const emitChange = () => {
  emit("change", {
    page: state.page,
    per_page: state.perPage,
    search: state.search,
    sort: state.sort,
    direction: state.direction,
  });
};

/**
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const changePage = (page: number) => {
  if (page < 1 || page > totalPages.value) return;
  state.page = page;
  emitChange();
};

/**
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

watch(
  () => state.search,
  () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      state.page = 1;
      emitChange();
    }, 400);
  },
);

/**
|--------------------------------------------------------------------------
| Sorting
|--------------------------------------------------------------------------
*/

const sortBy = (col: Column) => {
  if (!col.sortable) return;

  if (state.sort === col.key) {
    state.direction = state.direction === "asc" ? "desc" : "asc";
  } else {
    state.sort = col.key;
    state.direction = "asc";
  }

  emitChange();
};

/**
|--------------------------------------------------------------------------
| Selection
|--------------------------------------------------------------------------
*/

const toggleRow = (row: Row) => {
  const exists = state.selected.find((r) => r.id === row.id);

  if (exists) {
    state.selected = state.selected.filter((r) => r.id !== row.id);
  } else {
    state.selected.push(row);
  }

  emit("selection-change", state.selected);
};

const toggleAll = () => {
  state.selected = allSelected.value ? [] : [...props.data];
  emit("selection-change", state.selected);
};

/**
|--------------------------------------------------------------------------
| Expandable
|--------------------------------------------------------------------------
*/

const toggleExpand = (id: number) => {
  if (state.expanded.includes(id)) {
    state.expanded = state.expanded.filter((i) => i !== id);
  } else {
    state.expanded.push(id);
  }
};

const isExpanded = (id: number) => state.expanded.includes(id);

/**
|--------------------------------------------------------------------------
| Cell Renderer
|--------------------------------------------------------------------------
*/

const renderCell = (col: Column, value: any) => {
  if (col.type === "image") {
    return `<img src="${value}" class="h-8 w-8 rounded-full object-cover" />`;
  }

  if (col.type === "badge") {
    return `<Badge variant="success">{{ value }}</Badge>`;
  }

  return value;
};
</script>

<template>
  <div class="w-full space-y-4">
    <!-- TOP BAR -->
    <div class="flex justify-between items-center">
      <input
        v-if="searchable"
        v-model="state.search"
        placeholder="translations?.search || 'Search...'"
        class="px-4 py-2 rounded-lg bg-bg-primary border border-border text-text-primary"
      />

      <div v-if="bulkActions && state.selected.length" class="flex gap-2">
        <button
          v-for="action in bulkActions"
          :key="action.event"
          @click="$emit(action.event, state.selected)"
          class="px-3 py-1 bg-brand-secondary text-white rounded"
        >
          {{ action.label }} ({{ state.selected.length }})
        </button>
      </div>
    </div>

    <!-- TABLE -->
    <div class="border border-border rounded-xl overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-bg-secondary text-text-secondary">
          <tr>
            <th v-if="selectable">
              <input
                type="checkbox"
                :checked="allSelected"
                @change="toggleAll"
              />
            </th>
            <th v-if="expandable"></th>

            <th
              v-for="col in columns"
              :key="col.key"
              @click="sortBy(col)"
              class="px-4 py-3 cursor-pointer"
            >
              {{ col.label }}
            </th>

            <th v-if="hasActions" class="text-right px-4">
              {{ translations?.actions || "Actions" }}
            </th>
          </tr>
        </thead>

        <tbody>
          <template v-for="row in data" :key="row.id">
            <!-- MAIN ROW -->
            <tr class="hover:bg-bg-secondary">
              <td v-if="selectable">
                <input
                  type="checkbox"
                  :checked="state.selected.some((r) => r.id === row.id)"
                  @change="toggleRow(row)"
                />
              </td>

              <!-- EXPAND -->
              <td v-if="expandable">
                <button @click="toggleExpand(row.id)">
                  {{
                    isExpanded(row.id)
                      ? translations?.collapse || "Collapse"
                      : translations?.expand || "Expand"
                  }}
                </button>
              </td>

              <!-- CELLS -->
              <td v-for="col in columns" :key="col.key" class="px-4 py-3">
                <slot :name="`cell-${col.key}`" :row="row">
                  <span v-html="renderCell(col, row[col.key])" />
                </slot>
              </td>

              <!-- ACTIONS -->
              <td v-if="hasActions" class="text-right px-4">
                <button
                  v-for="action in actions"
                  :key="action.event"
                  @click="$emit(action.event, row)"
                  class="text-brand-secondary"
                >
                  {{ action.label }}
                </button>
              </td>
            </tr>

            <!-- EXPANDED -->
            <tr v-if="expandable && isExpanded(row.id)">
              <td :colspan="columns.length + 3" class="bg-bg-primary p-4">
                <slot name="expand" :row="row">
                  <!-- default fallback -->
                  <pre class="text-xs text-text-secondary"
                    >{{ row }}
                  </pre>
                </slot>
              </td>
            </tr>
          </template>
        </tbody>

        <tbody v-if="loading">
          <tr>
            <td
              :colspan="
                columns.length +
                (selectable ? 1 : 0) +
                (expandable ? 1 : 0) +
                (hasActions ? 1 : 0)
              "
              class="text-center py-8"
            >
              {{ translations?.loading || "Loading..." }}
            </td>
          </tr>
        </tbody>
        <tbody v-else-if="!data.length">
          <tr>
            <td
              :colspan="
                columns.length +
                (selectable ? 1 : 0) +
                (expandable ? 1 : 0) +
                (hasActions ? 1 : 0)
              "
              class="text-center py-8"
            >
              {{ translations?.noData || "No data available" }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <Pagination
      v-if="meta"
      :page="state.page"
      :perPage="state.perPage"
      :total="meta.total"
      @change="changePage"
      :translations="{
        page: translations?.dataTable?.page,
        of: translations?.dataTable?.of,
        prev: translations?.dataTable?.previous,
        next: translations?.dataTable?.next,
      }"
    />
  </div>
</template>
