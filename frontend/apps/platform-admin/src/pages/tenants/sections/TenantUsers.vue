<template>
  <TenantUserModal
    v-model="showForm"
    :tenant-id="props.tenant?.id"
    :user="selectedUser"
    @saved="onSaved"
  />

  <div class="w-full max-w-5xl space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-semibold text-text-black">Tenant Users</h2>

      <Button @click="openCreate">Create User</Button>
    </div>

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
      <template #cell-enabled="{ row }">
        <Badge :variant="row.enabled ? 'success' : 'danger'">
          {{ row.enabled ? "Active" : "Disabled" }}
        </Badge>
      </template>

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
import { ref, computed, watch } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import {
  tenantUsersService,
  type TenantUser,
  type TenantUsersQuery,
} from "@core/api/services/tenants/usersService";
import { DataTable, Button, Badge } from "@ui";
import type {
  Action as DataTableAction,
  Meta as DataTableMeta,
} from "@ui/components/datatable/types";
import { useI18n } from "vue-i18n";
import { useAction } from "@core/composables/useAction";
import TenantUserModal from "../components/users/TenantUserModal.vue";

const props = defineProps<{
  tenant: any;
}>();

const emit = defineEmits<{
  (e: "updated"): void;
}>();

const { t } = useI18n();
const { execute } = useAction();

const rows = ref<TenantUser[]>([]);
const meta = ref<DataTableMeta | null>(null);
const loading = ref(false);
const query = ref<TenantUsersQuery>({
  page: 1,
  per_page: 10,
  search: "",
  sort: "id",
  direction: "asc",
});

const showForm = ref(false);
const selectedUser = ref<any | null>(null);

const columns = [
  { key: "name", label: "Name", sortable: true },
  { key: "email", label: "Email", sortable: true },
  { key: "roles", label: "Roles" },
  { key: "enabled", label: "Status" },
];

const tableActions = computed<DataTableAction[]>(() => {
  return [
    {
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit user",
    },
    {
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete user",
    },
  ];
});

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

const load = async (params: Partial<TenantUsersQuery> = {}) => {
  if (!props.tenant?.id) {
    rows.value = [];
    meta.value = null;
    return;
  }

  loading.value = true;

  try {
    query.value = { ...query.value, ...params };
    const res = await tenantUsersService.getAll(props.tenant.id, query.value);

    rows.value = Array.isArray(res?.data) ? res.data : [];
    meta.value = res.meta ?? null;
  } catch {
    rows.value = [];
    meta.value = null;
  } finally {
    loading.value = false;
  }
};

watch(
  () => props.tenant?.id,
  () => {
    query.value.page = 1;
    load(query.value);
  },
  { immediate: true },
);

const onChange = (...args: unknown[]) => {
  const params = args[0];

  if (params && typeof params === "object") {
    load(params as TenantUsersQuery);
    return;
  }

  load();
};

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
    if (!props.tenant?.id) return;
    if (!confirm("Delete this user?")) return;

    await tenantUsersService.delete(props.tenant.id, row.id);
    await load(query.value);
    emit("updated");
  });

const onSaved = async () => {
  await load(query.value);
  emit("updated");
};
</script>
