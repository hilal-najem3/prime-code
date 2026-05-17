<script setup lang="ts">
// path to: frontend/apps/tenant-admin/src/pages/patients/PatientsPage.vue
import { ref, computed } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import {
  patientService,
  type Patient,
  type PatientsQuery,
} from "@core/api/services/patientService";
import { DataTable, Button, Badge } from "@ui";
import { usePermissions } from "@core/permissions/usePermissions";
import { useI18n } from "vue-i18n";
import { useAction } from "@core/composables/useAction";
import PatientFormModal from "./components/PatientFormModal.vue";

const { t } = useI18n();
const { can } = usePermissions();
const { execute } = useAction();

const rows = ref<Patient[]>([]);
const meta = ref<{
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
} | null>(null);
const loading = ref(false);
const query = ref<PatientsQuery>({
  page: 1,
  per_page: 10,
  search: "",
  sort: "id",
  direction: "asc",
});

const showForm = ref(false);
const selectedPatient = ref<Patient | null>(null);

const columns = [
  { key: "full_name", label: "Patient", sortable: true },
  { key: "email", label: "Email", sortable: true },
  { key: "phone", label: "Phone", sortable: true },
  { key: "blood_type", label: "Blood", sortable: true },
  { key: "status", label: "Status", sortable: true },
];

const tableActions = computed(() => {
  const actions: Array<{
    label: string;
    event: "edit" | "delete";
    icon: typeof Pencil;
    title: string;
  }> = [];

  if (can("patients.update")) {
    actions.push({
      label: "Edit",
      event: "edit",
      icon: Pencil,
      title: "Edit patient",
    });
  }

  if (can("patients.destroy")) {
    actions.push({
      label: "Delete",
      event: "delete",
      icon: Trash2,
      title: "Delete patient",
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

const load = async (params: Partial<PatientsQuery> = {}) => {
  loading.value = true;

  try {
    query.value = { ...query.value, ...params };

    const res = await patientService.getAll(query.value);

    rows.value = Array.isArray(res?.data) ? res.data : [];
    meta.value = res?.meta ?? null;
  } catch {
    rows.value = [];
    meta.value = null;
  } finally {
    loading.value = false;
  }
};

const onChange = (params: unknown) => {
  load(params as PatientsQuery);
};

const openCreate = () => {
  selectedPatient.value = null;
  showForm.value = true;
};

const onEdit = (row: Patient) => {
  selectedPatient.value = row;
  showForm.value = true;
};

const onDelete = (row: Patient) =>
  execute(async () => {
    if (!confirm("Delete this patient and related identity records?")) return;

    await patientService.delete(row.id);
    load(query.value);
  });

load(query.value);
</script>

<template>
  <PatientFormModal
    v-model="showForm"
    :patient="selectedPatient"
    @saved="load"
  />

  <div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-3">
      <h1 class="text-xl font-semibold text-text-primary">Patients</h1>

      <Button v-if="can('patients.store')" @click="openCreate">
        Register patient
      </Button>
    </div>

    <DataTable
      :columns="columns"
      :data="rows"
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
      <template #cell-status="{ row }">
        <Badge :variant="row.status === 'inactive' ? 'danger' : 'success'">
          {{ row.status === "inactive" ? "Inactive" : "Active" }}
        </Badge>
      </template>

      <template #cell-full_name="{ row }">
        <span>{{
          row.full_name || `${row.first_name} ${row.last_name}`.trim()
        }}</span>
      </template>

      <template #cell-phone="{ row }">
        <span>{{ row.phone || "—" }}</span>
      </template>

      <template #cell-email="{ row }">
        <span>{{ row.email || "—" }}</span>
      </template>

      <template #cell-blood_type="{ row }">
        <span>{{ row.blood_type || "—" }}</span>
      </template>
    </DataTable>
  </div>
</template>
