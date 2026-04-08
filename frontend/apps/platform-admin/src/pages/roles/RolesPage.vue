<template>
  <RoleFormModal v-model="showForm" :role="selectedRole" @saved="load" />

  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-semibold text-text-primary">
        {{ t("roles.title") || "Roles" }}
      </h1>

      <Button v-can="'platform.roles.store'" @click="openCreate">
        {{ t("roles.actions.create") }}
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
      @edit="onEdit"
      @delete="onDelete"
    >
      <!-- PERMISSIONS COUNT -->
      <template #cell-permissions="{ row }">
        <Badge variant="secondary">
          {{ row.permissions?.length || 0 }}
        </Badge>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { rolesService } from "@core/api/services/rolesService";
import { DataTable, Button, Badge } from "@ui";
import { usePermissions } from "@core/permissions/usePermissions";
import { useI18n } from "vue-i18n";
import { useAction } from "@core/composables/useAction";
import RoleFormModal from "./components/RoleFormModal.vue";

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
const selectedRole = ref<any | null>(null);

/*
|--------------------------------------------------------------------------
| Columns
|--------------------------------------------------------------------------
*/
const columns = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: "Name", sortable: true },
  { key: "slug", label: "Slug", sortable: true },
  { key: "permissions", label: "Permissions" }, // custom slot
];

/*
|--------------------------------------------------------------------------
| Table Actions
|--------------------------------------------------------------------------
*/
const tableActions = computed(() => {
  const actions: any[] = [];

  if (can("platform.roles.update")) {
    actions.push({
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit role",
    });
  }

  if (can("platform.roles.destroy")) {
    actions.push({
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete role",
    });
  }

  return actions;
});

/*
|--------------------------------------------------------------------------
| Translations
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
    const res = await rolesService.getAll();

    // ApiResponse standard
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
const openCreate = () => {
  selectedRole.value = null;
  showForm.value = true;
};

const onEdit = (row: any) => {
  selectedRole.value = row;
  showForm.value = true;
};

const onDelete = (row: any) =>
  execute(async () => {
    if (!confirm(t("roles.messages.confirmDelete") || "Delete role?")) return;

    await rolesService.delete(row.id);
    load();
  });
</script>
