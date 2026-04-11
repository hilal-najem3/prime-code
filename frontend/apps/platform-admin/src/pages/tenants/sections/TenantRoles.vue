<template>
  <TenantRoleModal
    v-model="showForm"
    :tenant-id="props.tenant?.id"
    :role="selectedRole"
    @saved="onSaved"
  />

  <div class="w-full max-w-5xl space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-semibold text-text-black">Tenant Roles</h2>

      <Button @click="openCreate">Create Role</Button>
    </div>

    <DataTable
      :columns="columns"
      :data="rows"
      :actions="tableActions"
      :loading="loading"
      searchable
      expandable
      :translations="tableTranslations"
      @edit="onEdit"
      @delete="onDelete"
    >
      <template #cell-permissions="{ row }">
        <Badge variant="secondary">
          {{ row.permissions?.length || 0 }}
        </Badge>
      </template>

      <template #expand="{ row }">
        <div class="space-y-4 p-5">
          <div
            v-for="(group, module) in groupPermissions(row.permissions)"
            :key="module"
            class="space-y-2"
          >
            <p class="text-xs font-semibold text-text-secondary uppercase">
              {{ module }}
            </p>

            <div class="flex flex-wrap gap-2">
              <Badge v-for="perm in group" :key="perm.id" variant="secondary">
                {{ perm.slug }}
              </Badge>
            </div>
          </div>

          <p
            v-if="!row.permissions?.length"
            class="text-sm text-text-secondary"
          >
            No permissions assigned
          </p>
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { tenantRolesService } from "@core/api/services/tenants/rolesService";
import { DataTable, Button, Badge } from "@ui";
import { useI18n } from "vue-i18n";
import { useAction } from "@core/composables/useAction";
import TenantRoleModal from "../components/roles/TenantRoleModal.vue";

const props = defineProps<{
  tenant: any;
}>();

const emit = defineEmits<{
  (e: "updated"): void;
}>();

const { t } = useI18n();
const { execute } = useAction();

const rows = ref<any[]>([]);
const loading = ref(false);
const showForm = ref(false);
const selectedRole = ref<any | null>(null);

const columns = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: "Name", sortable: true },
  { key: "slug", label: "Slug", sortable: true },
  { key: "permissions", label: "Permissions" },
];

const tableActions = computed(() => {
  return [
    {
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit role",
    },
    {
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete role",
    },
  ];
});

const groupPermissions = (permissions: any[] = []) => {
  const groups: Record<string, any[]> = {};

  permissions.forEach((permission) => {
    if (!groups[permission.module]) {
      groups[permission.module] = [];
    }

    groups[permission.module].push(permission);
  });

  return groups;
};

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

const load = async () => {
  if (!props.tenant?.id) {
    rows.value = [];
    return;
  }

  loading.value = true;

  try {
    const res = await tenantRolesService.getAll(props.tenant.id);
    rows.value = Array.isArray(res.data) ? res.data : [];
  } finally {
    loading.value = false;
  }
};

watch(
  () => props.tenant?.id,
  () => {
    load();
  },
  { immediate: true },
);

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
    if (!props.tenant?.id) return;
    if (!confirm("Delete this role?")) return;

    await tenantRolesService.delete(props.tenant.id, row.id);
    await load();
    emit("updated");
  });

const onSaved = async () => {
  await load();
  emit("updated");
};
</script>
