<template>
  <ModuleFormModal v-model="showForm" :module="selectedModule" @saved="load" />

  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-semibold text-text-primary">
        {{ t("modules.title") }}
      </h1>

      <Button
        v-can="'modules.store'"
        @click="
          selectedModule = null;
          showForm = true;
        "
      >
        {{ t("modules.actions.create") }}
      </Button>
    </div>

    <!-- Table -->
    <DataTable
      :columns="columns"
      :data="rows"
      :actions="tableActions"
      :loading="loading"
      searchable
      @edit="onEdit"
      @delete="onDelete"
      :translations="tableTranslations"
    >
      <!-- Enabled Badge -->
      <template #cell-enabled="{ row }">
        <Badge :variant="row.enabled ? 'success' : 'danger'">
          {{
            row.enabled
              ? t("modules.status.enabled")
              : t("modules.status.disabled")
          }}
        </Badge>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { moduleService } from "@core/api/services/moduleService";
import { DataTable, Button, Badge } from "@ui";
import { usePermissions } from "@core/permissions/usePermissions";
import { useI18n } from "vue-i18n";
import ModuleFormModal from "./components/ModuleFormModal.vue";

const { t } = useI18n();
const { can } = usePermissions();

const rows = ref<any[]>([]);
const loading = ref(false);

const showForm = ref(false);
const selectedModule = ref<any | null>(null);

const columns = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: t("modules.fields.name"), sortable: true },
  { key: "slug", label: t("modules.fields.slug") },
  { key: "enabled", label: t("modules.fields.enabled") },
];

const tableActions = computed(() => {
  const actions: any[] = [];

  if (can("modules.update")) {
    actions.push({
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit module",
    });
  }

  if (can("modules.destroy")) {
    actions.push({
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete module",
    });
  }

  return actions;
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

const load = async () => {
  loading.value = true;

  try {
    const res = await moduleService.getAll();
    rows.value = Array.isArray(res.data) ? res.data : [];
  } finally {
    loading.value = false;
  }
};

load();

const onEdit = (row: any) => {
  selectedModule.value = row;
  showForm.value = true;
};

const onDelete = async (row: any) => {
  if (!confirm(t("modules.messages.confirmDelete"))) return;

  await moduleService.delete(row.id);

  load();
};
</script>
