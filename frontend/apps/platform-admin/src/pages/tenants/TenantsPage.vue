<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-semibold text-text-primary">Tenants</h1>

      <Button v-can="'tenants.store'"> Create Tenant </Button>
    </div>

    <!-- Table -->
    <DataTable
      :columns="columns"
      :data="rows"
      :actions="tableActions"
      :meta="meta"
      :loading="loading"
      :per-page-options="[10, 15, 25, 50]"
      searchable
      @change="load"
      @edit="onEdit"
      @delete="onDelete"
    >
      <!-- STATUS BADGE -->
      <template #cell-status="{ row }">
        <Badge
          :variant="
            row.status === 'active'
              ? 'success'
              : row.status === 'suspended'
                ? 'danger'
                : 'warning'
          "
        >
          {{ row.status }}
        </Badge>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { tenantService } from "@core/api/services/tenantService";
import { DataTable, Button, Badge } from "@ui";
import { usePermissions } from "@core/permissions/usePermissions";

type TenantRow = {
  id: number;
  name: string;
  slug: string;
  domain: string;
  status: string;
};

type PaginationMeta = {
  current_page: number;
  per_page: number;
  total: number;
  last_page?: number;
};

const rows = ref<TenantRow[]>([]);
const meta = ref({
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1,
} satisfies PaginationMeta);
const loading = ref(false);
const { can } = usePermissions();
const query = ref({
  page: 1,
  per_page: 10,
  search: "",
  sort: "id",
  direction: "asc",
});
let latestRequestId = 0;

const tableActions = computed(() => {
  const actions: { label: string; event: string; icon: any; title: string }[] =
    [];

  if (can("tenants.update")) {
    actions.push({
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit tenant",
    });
  }

  if (can("tenants.destroy")) {
    actions.push({
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete tenant",
    });
  }

  return actions;
});

const columns = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: "Name", sortable: true },
  { key: "slug", label: "Slug" },
  { key: "domain", label: "Domain" },
  { key: "status", label: "Status" },
];

const load = async (params: any = {}) => {
  query.value = {
    ...query.value,
    ...params,
  };

  const requestId = ++latestRequestId;

  loading.value = true;
  rows.value = [];

  try {
    const res = await tenantService.getAll(query.value);

    if (requestId !== latestRequestId) {
      return;
    }

    rows.value = Array.isArray(res.data) ? res.data : [];
    meta.value = {
      current_page: res.meta?.current_page ?? query.value.page,
      per_page: res.meta?.per_page ?? query.value.per_page,
      total: res.meta?.total ?? rows.value.length,
      last_page: res.meta?.last_page ?? 1,
    };
  } finally {
    if (requestId === latestRequestId) {
      loading.value = false;
    }
  }
};

// initial load
load();

const onEdit = (row: any) => {
  console.log("Edit", row);
};

const onDelete = async (row: any) => {
  if (!confirm("Delete tenant?")) return;

  await tenantService.delete(row.id);

  load();
};
</script>
