<template>
  <TenantFormModal v-model="showForm" :tenant="selectedTenant" @saved="load" />

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

    <!-- Table -->
    <DataTable
      :columns="columns"
      :data="rows"
      :actions="tableActions"
      :loading="loading"
      searchable
      :translations="tableTranslations"
      @view="onView"
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
import { ref, computed } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { tenantService } from "@core/api/services/tenantService";
import { DataTable, Button, Badge } from "@ui";
import { usePermissions } from "@core/permissions/usePermissions";
import TenantFormModal from "./components/TenantFormModal.vue";
import { useI18n } from "vue-i18n";
import { useAction } from "@core/composables/useAction";
import { Eye } from "lucide-vue-next";
import { useRouter } from "vue-router";

const router = useRouter();

const { t } = useI18n();
const { can } = usePermissions();
const { execute } = useAction();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/
const rows = ref<any[]>([]);
const loading = ref(false);

const showForm = ref(false);
const selectedTenant = ref<any | null>(null);

/*
|--------------------------------------------------------------------------
| Columns (UPDATED)
|--------------------------------------------------------------------------
*/
const columns = [
  { key: "name", label: "Name", sortable: true },
  { key: "username", label: "Username" }, // ✅ ADDED
  { key: "slug", label: "Slug" },
  { key: "domain", label: "Domain" },
  { key: "status", label: "Status" },
];

/*
|--------------------------------------------------------------------------
| Table Actions
|--------------------------------------------------------------------------
*/
const tableActions = computed(() => {
  const actions: any[] = [];

  if (can("tenants.view")) {
    actions.push({
      label: "View",
      event: "view",
      icon: Eye,
      title: "View tenant details",
    });
  }

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
| Translations (MATCH PLANS PAGE)
|--------------------------------------------------------------------------
*/
const tableTranslations = {
  search: t("datatable.search"),
  actions: t("datatable.actions"),
  loading: t("datatable.loading"),
  noData: t("datatable.noData"),
  dataTable: {
    page: t("datatable.page"),
    of: t("datatable.of"),
    previous: t("datatable.previous"),
    next: t("datatable.next"),
  },
};

/*
|--------------------------------------------------------------------------
| Load (LOCAL MODE)
|--------------------------------------------------------------------------
*/
const load = async () => {
  loading.value = true;

  try {
    const res = await tenantService.getAll();
    rows.value = Array.isArray(res.data) ? res.data : [];
  } finally {
    loading.value = false;
  }
};

load();

/*
|--------------------------------------------------------------------------
| Handlers
|--------------------------------------------------------------------------
*/
const onView = (row: any) => {
  router.push(`/tenants/${row.id}/general`);
};

const openCreate = () => {
  selectedTenant.value = null;
  showForm.value = true;
};

const onEdit = (row: any) => {
  selectedTenant.value = row;
  showForm.value = true;
};

const onDelete = (row: any) =>
  execute(async () => {
    if (!confirm(t("tenants.messages.confirmDelete") || "Delete tenant?"))
      return;

    await tenantService.delete(row.id);
    load(); // same as Plans page
  });
</script>
