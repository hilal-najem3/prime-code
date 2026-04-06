<template>
  <TenantFormModal
    v-model="showForm"
    :tenant="selectedTenant"
    @saved="handleSaved"
  />

  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-semibold text-text-primary">
        {{ t("tenants.title") || "Tenants" }}
      </h1>

      <Button v-can="'tenants.store'" @click="openCreate">
        {{ t("tenants.actions.create") }}
      </Button>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && rows.length === 0">
      <div class="text-center py-10 text-text-secondary">
        <p class="text-lg font-medium">
          {{ t("tenants.empty.title") || "No tenants yet" }}
        </p>
        <p class="text-sm mt-1">
          {{ t("tenants.empty.description") || "Create your first tenant" }}
        </p>

        <div class="mt-4">
          <Button @click="openCreate">
            {{ t("tenants.actions.create") }}
          </Button>
        </div>
      </div>
    </div>

    <!-- Table -->
    <DataTable
      v-else
      :columns="columns"
      :data="rows"
      :actions="tableActions"
      :meta="useRemoteTable ? meta : undefined"
      :remote="useRemoteTable"
      :loading="loading"
      :per-page-options="[1, 5, 10, 15, 25, 50]"
      searchable
      @change="onTableChange"
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
import TenantFormModal from "./components/TenantFormModal.vue";
import { useI18n } from "vue-i18n";
import { useAction } from "@core/composables/useAction";

const { t } = useI18n();

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/
const useRemoteTable = false;
const rows = ref<TenantRow[]>([]);
const loading = ref(false);
const { can } = usePermissions();
const { execute } = useAction();

const meta = ref<PaginationMeta>({
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1,
});

const query = ref({
  page: 1,
  per_page: 10,
  search: "",
  sort: "id",
  direction: "asc",
});

let latestRequestId = 0;

const showForm = ref(false);
const selectedTenant = ref<any | null>(null);

/*
|--------------------------------------------------------------------------
| Table Actions (permission-aware)
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Columns
|--------------------------------------------------------------------------
*/
const columns = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: "Name", sortable: true },
  { key: "slug", label: "Slug" },
  { key: "domain", label: "Domain" },
  { key: "status", label: "Status" },
];

/*
|--------------------------------------------------------------------------
| Load Data (safe + cancellable)
|--------------------------------------------------------------------------
*/
const load = async (params: Partial<typeof query.value> = {}) => {
  query.value = {
    ...query.value,
    ...params,
  };

  const requestId = ++latestRequestId;

  loading.value = true;

  try {
    const res = await tenantService.getAll(
      useRemoteTable ? query.value : undefined,
    );

    if (requestId !== latestRequestId) return;

    rows.value = Array.isArray(res.data) ? res.data : [];

    if (useRemoteTable) {
      meta.value = {
        current_page: res.meta?.current_page ?? query.value.page,
        per_page: res.meta?.per_page ?? query.value.per_page,
        total: res.meta?.total ?? rows.value.length,
        last_page: res.meta?.last_page ?? 1,
      };
    } else {
      meta.value = {
        current_page: 1,
        per_page: query.value.per_page,
        total: rows.value.length,
        last_page: 1,
      };
    }
  } finally {
    if (requestId === latestRequestId) {
      loading.value = false;
    }
  }
};

/*
|--------------------------------------------------------------------------
| Handlers
|--------------------------------------------------------------------------
*/
const onTableChange = (params: any) => {
  if (!useRemoteTable) return;
  load(params);
};

const openCreate = () => {
  selectedTenant.value = null;
  showForm.value = true;
};

const onEdit = (row: any) => {
  selectedTenant.value = row;
  showForm.value = true;
};

/*
|--------------------------------------------------------------------------
| DELETE (improved UX)
|--------------------------------------------------------------------------
*/
const onDelete = (row: any) =>
  execute(async () => {
    if (!confirm("Delete tenant?")) return;

    await tenantService.delete(row.id, {
      meta: { showSuccessToast: true },
    });

    // 🔥 No reload → remove locally
    rows.value = rows.value.filter((t) => t.id !== row.id);
  });

/*
|--------------------------------------------------------------------------
| After Save (create/update)
|--------------------------------------------------------------------------
*/
const handleSaved = () => {
  // Simple approach (safe)
  load();

  // 🔥 Later: replace with optimistic add/update
};

/*
|--------------------------------------------------------------------------
| Init
|--------------------------------------------------------------------------
*/
load();
</script>
