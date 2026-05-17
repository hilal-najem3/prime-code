<template>
  <PageModal
    v-model="showForm"
    :tenant-id="props.tenant?.id"
    :page="selectedPage"
    @saved="onSaved"
  />

  <div class="w-full max-w-5xl space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-semibold text-text-black">Tenant Pages</h2>

      <Button @click="openCreate">Create Page</Button>
    </div>

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
      <template #cell-title="{ row }">
        {{ row?.title?.en || "-" }}
      </template>

      <template #cell-slug="{ row }">
        {{ row?.slug?.en || "-" }}
      </template>

      <template #cell-status="{ row }">
        <Badge :variant="row.status === 'published' ? 'success' : 'secondary'">
          {{ row.status }}
        </Badge>
      </template>

      <template #cell-is_homepage="{ row }">
        <Badge :variant="row.is_homepage ? 'success' : 'secondary'">
          {{ row.is_homepage ? "Yes" : "No" }}
        </Badge>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { pageService } from "@core/api/services/tenants/pageService";
import { useAction } from "@core/composables/useAction";
import { Badge, Button, DataTable } from "@ui";
import type { Action as DataTableAction } from "@ui/components/datatable/types";
import { useI18n } from "vue-i18n";
import PageModal from "../components/pages/PageModal.vue";

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
const selectedPage = ref<any | null>(null);

const columns = [
  { key: "title", label: "Title", sortable: true },
  { key: "slug", label: "Slug", sortable: true },
  { key: "status", label: "Status", sortable: true },
  { key: "is_homepage", label: "Homepage" },
];

const tableActions = computed<DataTableAction[]>(() => {
  return [
    {
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit page",
    },
    {
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete page",
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

const load = async () => {
  if (!props.tenant?.id) {
    rows.value = [];
    return;
  }

  loading.value = true;

  try {
    const res = await pageService.getAll(props.tenant.id);
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
  selectedPage.value = null;
  showForm.value = true;
};

const onEdit = (row: any) => {
  selectedPage.value = row;
  showForm.value = true;
};

const onDelete = (row: any) =>
  execute(async () => {
    if (!props.tenant?.id) return;
    if (!confirm("Delete this page?")) return;

    await pageService.delete(props.tenant.id, row.id);
    await load();
    emit("updated");
  });

const onSaved = async () => {
  await load();
  emit("updated");
};
</script>
