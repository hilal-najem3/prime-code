<template>
  <PlanFormModal v-model="showForm" :plan="selectedPlan" @saved="load" />

  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-semibold text-text-primary">
        {{ t("plans.title") }}
      </h1>

      <Button
        v-can="'plans.store'"
        @click="
          selectedPlan = null;
          showForm = true;
        "
      >
        {{ t("plans.actions.create") }}
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
      :remote="false"
      :meta="undefined"
      @edit="onEdit"
      @delete="onDelete"
    >
      <!-- Price -->
      <template #cell-price="{ row }"> ${{ row.price }} </template>

      <!-- Modules -->
      <template #cell-modules="{ row }">
        <div class="flex flex-wrap gap-1">
          <Badge v-for="m in row.modules" :key="m.id" variant="default">
            {{ m.name }}
          </Badge>
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { planService } from "@core/api/services/planService";
import { useAction } from "@core/composables/useAction";
import { DataTable, Button, Badge } from "@ui";
import { usePermissions } from "@core/permissions/usePermissions";
import { useI18n } from "vue-i18n";
import PlanFormModal from "./components/PlanFormModal.vue";

const { t } = useI18n();
const { can } = usePermissions();

const rows = ref<any[]>([]);
const loading = ref(false);
const { execute } = useAction();

const showForm = ref(false);
const selectedPlan = ref<any | null>(null);

const columns = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: t("plans.fields.name"), sortable: true },
  { key: "slug", label: t("plans.fields.slug") },
  { key: "price", label: t("plans.fields.price") },
  { key: "modules", label: t("plans.fields.modules") },
];

const tableActions = computed(() => {
  const actions: any[] = [];

  if (can("plans.update")) {
    actions.push({
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit plan",
    });
  }

  if (can("plans.destroy")) {
    actions.push({
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete plan",
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

let loadingLock = false;

const load = async () => {
  if (loadingLock) return;

  loadingLock = true;

  try {
    loading.value = true;
    const res = await planService.getAll();
    console.log("API response:", res);
    rows.value = Array.isArray(res.data?.data) ? res.data.data : [];
    console.log("Loaded plans:", rows.value);
  } finally {
    loading.value = false;
    loadingLock = false;
  }
};

import { onMounted } from "vue";

onMounted(load);

const onEdit = (row: any) => {
  selectedPlan.value = row;
  showForm.value = true;
};

const onDelete = (row: any) =>
  execute(async () => {
    if (!confirm(t("plans.messages.confirmDelete"))) return;

    await planService.delete(row.id);
    load();
  });
</script>
