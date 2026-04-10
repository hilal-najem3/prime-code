<template>
  <TenantLanguageModal
    v-model="showForm"
    :tenant-id="props.tenant?.id"
    :language="selectedLanguage"
    @saved="load"
  />

  <div class="w-full max-w-5xl space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-semibold text-text-primary">Tenant Languages</h2>

      <Button @click="openCreate">Create Language</Button>
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
      <template #cell-is_default="{ row }">
        <Badge :variant="row.is_default ? 'success' : 'secondary'">
          {{ row.is_default ? "Yes" : "No" }}
        </Badge>
      </template>

      <template #cell-is_active="{ row }">
        <Badge :variant="row.is_active ? 'success' : 'danger'">
          {{ row.is_active ? "Active" : "Inactive" }}
        </Badge>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { languageService } from "@core/api/services/languageService";
import { useAction } from "@core/composables/useAction";
import { Badge, Button, DataTable } from "@ui";
import { useI18n } from "vue-i18n";
import TenantLanguageModal from "../components/TenantLanguageModal.vue";

const props = defineProps<{
  tenant: any;
}>();

const { t } = useI18n();
const { execute } = useAction();

const rows = ref<any[]>([]);
const loading = ref(false);
const showForm = ref(false);
const selectedLanguage = ref<any | null>(null);

const columns = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: "Name", sortable: true },
  { key: "slug", label: "Slug", sortable: true },
  { key: "direction", label: "Direction", sortable: true },
  { key: "is_default", label: "Default" },
  { key: "is_active", label: "Status" },
];

const tableActions = computed(() => {
  return [
    {
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit language",
    },
    {
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete language",
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
    const res = await languageService.getAll(props.tenant.id);
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
  selectedLanguage.value = null;
  showForm.value = true;
};

const onEdit = (row: any) => {
  selectedLanguage.value = row;
  showForm.value = true;
};

const onDelete = (row: any) =>
  execute(async () => {
    if (!props.tenant?.id) return;
    if (!confirm("Delete this language?")) return;

    await languageService.delete(props.tenant.id, row.id);
    await load();
  });
</script>
