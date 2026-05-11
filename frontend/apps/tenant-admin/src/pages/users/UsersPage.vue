<template>
  <UserFormModal v-model="showForm" :user="selectedUser" @saved="load" />

  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-semibold text-text-primary">
        {{ t("users.title") || "Users" }}
      </h1>

      <Button v-can="'platform.users.store'" @click="openCreate">
        {{ t("users.actions.create") || "Create User" }}
      </Button>
    </div>

    <!-- Table -->
    <DataTable
      :columns="columns"
      :data="rows || []"
      :meta="meta"
      :actions="tableActions"
      :loading="loading"
      searchable
      remote
      :translations="tableTranslations"
      @change="onChange"
      @edit="onEdit"
      @delete="onDelete"
    >
      <!-- STATUS BADGE -->
      <template #cell-enabled="{ row }">
        <Badge :variant="row.enabled ? 'success' : 'danger'">
          {{ row.enabled ? "Active" : "Disabled" }}
        </Badge>
      </template>

      <!-- ROLES -->
      <template #cell-roles="{ row }">
        <div class="flex gap-1 flex-wrap justify-center">
          <Badge
            v-for="role in row.roles || []"
            :key="role.id"
            variant="secondary"
          >
            {{ role.name }}
          </Badge>
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { usersService, type UsersQuery } from "@core/api/services/usersService";
import { DataTable, Button, Badge } from "@ui";
import { usePermissions } from "@core/permissions/usePermissions";
import { useI18n } from "vue-i18n";
import { useAction } from "@core/composables/useAction";
import UserFormModal from "./components/UserFormModal.vue";

const { t } = useI18n();
const { can } = usePermissions();
const { execute } = useAction();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const rows = ref<any[]>([]);
const meta = ref<any | null>(null);
const loading = ref(false);
const query = ref<UsersQuery>({
  page: 1,
  per_page: 10,
  search: "",
  sort: "id",
  direction: "asc",
});

const showForm = ref(false);
const selectedUser = ref<any | null>(null);

/*
|--------------------------------------------------------------------------
| Columns (IMPORTANT)
|--------------------------------------------------------------------------
*/

const columns = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: "Name", sortable: true },
  { key: "email", label: "Email", sortable: true },
  { key: "roles", label: "Roles" },
  { key: "enabled", label: "Status" },
];

/*
|--------------------------------------------------------------------------
| Table Actions
|--------------------------------------------------------------------------
*/

const tableActions = computed(() => {
  const actions: any[] = [];

  if (can("platform.users.update")) {
    actions.push({
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit user",
    });
  }

  if (can("platform.users.destroy")) {
    actions.push({
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete user",
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
| Load (REMOTE MODE)
|--------------------------------------------------------------------------
*/

const load = async (params: Partial<UsersQuery> = {}) => {
  loading.value = true;

  try {
    query.value = { ...query.value, ...params };

    const res = await usersService.getAll(query.value);

    rows.value = Array.isArray(res?.data) ? res.data : [];
    meta.value = res?.meta ?? null;
    console.log("Loaded users:", rows.value);
  } catch {
    rows.value = [];
    meta.value = null;
  } finally {
    loading.value = false;
  }
};

/*
|--------------------------------------------------------------------------
| DataTable Change (CRITICAL)
|--------------------------------------------------------------------------
*/

const onChange = (params: unknown) => {
  load(params as UsersQuery);
};

/*
|--------------------------------------------------------------------------
| Handlers
|--------------------------------------------------------------------------
*/

const openCreate = () => {
  selectedUser.value = null;
  showForm.value = true;
};

const onEdit = (row: any) => {
  selectedUser.value = row;
  showForm.value = true;
};

const onDelete = (row: any) =>
  execute(async () => {
    if (!confirm(t("users.messages.confirmDelete") || "Delete user?")) return;

    await usersService.delete(row.id);
    load(query.value);
  });

/*
|--------------------------------------------------------------------------
| Init
|--------------------------------------------------------------------------
*/

load(query.value);
</script>
