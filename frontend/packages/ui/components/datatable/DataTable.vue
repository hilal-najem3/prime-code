<script setup lang="ts">
// path to: frontend/packages/ui/components/datatable/DataTable.vue
import { computed, reactive, watch } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import Pagination from "../Pagination.vue";
import type {
  Action,
  BulkAction,
  Column,
  DataTableActionEvent,
  DataTableBulkActionEvent,
  DataTableChangeParams,
  Meta,
  Row,
} from "./types";

const actions: Action[] = [
  { label: "Edit", event: "edit", icon: Pencil, title: "Edit" },
  { label: "Delete", event: "delete", icon: Trash2, title: "Delete" },
];

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

  meta?: Meta | null;
  remote?: boolean;
  searchable?: boolean;
  perPageOptions?: number[];
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
  (e: "change", params: DataTableChangeParams): void;
  (e: "edit", row: any): void;
  (e: "delete", row: any): void;
  (e: "view", row: any): void;
  (e: "bulk-edit", rows: any[]): void;
  (e: "bulk-delete", rows: any[]): void;
  (e: "bulk-view", rows: any[]): void;
  (e: "selection-change", rows: any[]): void;
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
const isRemote = computed(() => props.remote ?? !!props.meta);

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

const rows = computed(() => props.data ?? []);

const filteredRows = computed(() => {
  const search = state.search.trim().toLowerCase();

  if (!search) {
    return rows.value;
  }

  return rows.value.filter((row) =>
    props.columns.some((column) => {
      const value = row[column.key];

      return String(value ?? "")
        .toLowerCase()
        .includes(search);
    }),
  );
});

const sortedRows = computed(() => {
  const items = [...filteredRows.value];

  if (!state.sort) {
    return items;
  }

  return items.sort((left, right) => {
    const leftValue = left[state.sort];
    const rightValue = right[state.sort];

    if (leftValue == null && rightValue == null) return 0;
    if (leftValue == null) return state.direction === "asc" ? -1 : 1;
    if (rightValue == null) return state.direction === "asc" ? 1 : -1;

    if (leftValue === rightValue) return 0;

    if (leftValue > rightValue) {
      return state.direction === "asc" ? 1 : -1;
    }

    return state.direction === "asc" ? -1 : 1;
  });
});

const localTotal = computed(() => sortedRows.value.length);

const effectivePerPage = computed(() => props.meta?.per_page ?? state.perPage);

const totalPages = computed(() => {
  const totalItems = props.meta?.total ?? localTotal.value;

  return Math.max(1, Math.ceil(totalItems / effectivePerPage.value));
});

const paginatedRows = computed(() => {
  if (isRemote.value) {
    return rows.value;
  }

  const start = (state.page - 1) * state.perPage;
  const end = start + state.perPage;

  return sortedRows.value.slice(start, end);
});

const displayedRows = computed(() => {
  return isRemote.value ? rows.value : paginatedRows.value;
});

const rowActions = computed(() => props.actions ?? actions);

const expandedColspan = computed(() => {
  return (
    props.columns.length +
    (props.selectable ? 1 : 0) +
    (props.expandable ? 1 : 0) +
    (hasActions.value ? 1 : 0)
  );
});

const availablePerPageOptions = computed(() => {
  return props.perPageOptions?.length
    ? props.perPageOptions
    : [1, 5, 10, 15, 25, 50, 75, 100, 250, 500, 1000];
});

const allSelected = computed(() => {
  return (
    displayedRows.value.length > 0 &&
    displayedRows.value.every((row) =>
      state.selected.some((selectedRow) => selectedRow.id === row.id),
    )
  );
});

/**
|--------------------------------------------------------------------------
| Emit Change
|--------------------------------------------------------------------------
*/

const emitChange = () => {
  if (!isRemote.value) {
    return;
  }

  const params: DataTableChangeParams = {
    page: state.page,
    per_page: state.perPage,
    search: state.search,
  };

  if (state.sort) {
    params.sort = state.sort;
    params.direction = state.direction;
  }

  emit("change", params);
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

const changePerPage = () => {
  state.page = 1;
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

watch(totalPages, (pages) => {
  if (state.page > pages) {
    state.page = pages;
  }
});

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
  state.selected = allSelected.value ? [] : [...displayedRows.value];
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

const emitRowAction = (event: DataTableActionEvent, row: Row) => {
  if (event === "edit") {
    emit("edit", row);
  } else if (event === "delete") {
    emit("delete", row);
  } else {
    emit("view", row);
  }
};

const emitBulkAction = (event: DataTableBulkActionEvent) => {
  if (event === "bulk-edit") {
    emit("bulk-edit", state.selected);
  } else if (event === "bulk-delete") {
    emit("bulk-delete", state.selected);
  } else {
    emit("bulk-view", state.selected);
  }
};

/**
|--------------------------------------------------------------------------
| Cell Renderer
|--------------------------------------------------------------------------
*/

const renderCell = (col: Column, value: any) => {
  if (col.type === "image") {
    return `<img src="${value}" class="h-8 w-8 rounded-full object-cover" />`;
  }

  // if (col.type === "badge") {
  //   return `<Badge variant="success">{{ value }}</Badge>`;
  // }

  return value;
};
</script>

<template>
  <div class="w-full space-y-4">
    <!-- TOP BAR -->
    <div class="flex justify-between items-center gap-4">
      <div class="flex items-center gap-3">
        <input
          v-if="searchable"
          v-model="state.search"
          :placeholder="translations?.search || 'Search...'"
          class="px-4 py-2 rounded-lg bg-bg-primary border border-border text-text-primary"
        />

        <div class="flex items-center gap-2 text-sm text-text-secondary">
          <span>Per page</span>
          <select
            v-model.number="state.perPage"
            class="rounded-lg border border-border bg-bg-primary px-3 py-2 text-text-primary"
            @change="changePerPage"
          >
            <option
              v-for="option in availablePerPageOptions"
              :key="option"
              :value="option"
            >
              {{ option }}
            </option>
          </select>
        </div>
      </div>

      <div v-if="bulkActions && state.selected.length" class="flex gap-2">
        <button
          v-for="action in bulkActions"
          :key="action.event"
          @click="emitBulkAction(action.event)"
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

            <th v-if="expandable">{{ translations?.expand || "Expand" }}</th>
          </tr>
        </thead>

        <tbody>
          <template v-for="row in displayedRows" :key="row.id">
            <!-- MAIN ROW -->
            <tr class="datatable-row">
              <td v-if="selectable">
                <input
                  type="checkbox"
                  :checked="state.selected.some((r) => r.id === row.id)"
                  @change="toggleRow(row)"
                />
              </td>

              <!-- CELLS -->
              <td
                v-for="col in columns"
                :key="col.key"
                class="px-4 py-3 text-center"
              >
                <slot :name="`cell-${col.key}`" :row="row">
                  <span v-html="renderCell(col, row[col.key])" />
                </slot>
              </td>

              <!-- ACTIONS -->
              <td v-if="hasActions" class="text-right px-4">
                <div class="flex items-center justify-end gap-3">
                  <button
                    v-for="action in rowActions"
                    :key="action.event"
                    :title="action.title || action.label"
                    :aria-label="action.title || action.label"
                    @click="emitRowAction(action.event, row)"
                    class="text-brand-secondary transition-opacity hover:opacity-80"
                  >
                    <component :is="action.icon" class="h-4 w-4" />
                  </button>
                </div>
              </td>

              <!-- EXPAND -->
              <td v-if="expandable" class="text-center">
                <button @click="toggleExpand(row.id)" class="px-2 py-1">
                  {{
                    isExpanded(row.id)
                      ? translations?.collapse || "-"
                      : translations?.expand || "+"
                  }}
                </button>
              </td>
            </tr>

            <!-- EXPANDED -->
            <tr v-if="expandable && isExpanded(row.id)">
              <td :colspan="expandedColspan" class="bg-bg-primary p-4">
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
        <tbody v-else-if="!displayedRows.length">
          <tr>
            <td
              :colspan="
                columns.length +
                (selectable ? 1 : 0) +
                (expandable ? 1 : 0) +
                (hasActions ? 1 : 0)
              "
              class="text-center p-5"
            >
              {{ translations?.noData || "No data available" }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <Pagination
      v-if="loading || totalPages > 1 || displayedRows.length > 0"
      :page="state.page"
      :perPage="effectivePerPage"
      :total="meta?.total ?? localTotal"
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

<style scoped>
.datatable-row {
  transition:
    background-color 0.2s ease,
    color 0.2s ease;
}

.datatable-row:hover {
  background-color: var(--color-bg-secondary);
  color: var(--color-text-primary);
}
</style>
