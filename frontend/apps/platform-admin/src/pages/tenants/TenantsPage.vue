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
};

const rows = ref<TenantRow[]>([]);
const meta = ref({
  current_page: 1,
  per_page: 10,
  total: 0,
} satisfies PaginationMeta);
const loading = ref(false);
const { can } = usePermissions();

const tableActions = computed(() => {
  const actions: { label: string; event: string }[] = [];

  if (can("tenants.update")) {
    actions.push({ label: "Edit", event: "edit" });
  }

  if (can("tenants.destroy")) {
    actions.push({ label: "Delete", event: "delete" });
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
  loading.value = true;

  try {
    const res = await tenantService.getAll(params);

    console.log("Tenants", res.data);

    const payload = res.data;

    if (Array.isArray(payload)) {
      rows.value = payload;
      meta.value = {
        current_page: 1,
        per_page: payload.length || 10,
        total: payload.length,
      };

      return;
    }

    rows.value = Array.isArray(payload?.data) ? payload.data : [];
    meta.value = {
      current_page: payload?.current_page ?? 1,
      per_page: (payload?.per_page ?? rows.value.length) || 10,
      total: payload?.total ?? rows.value.length,
    };
  } finally {
    loading.value = false;
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
