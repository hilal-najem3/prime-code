<template>
  <SubscriptionFormModal v-model="showForm" @saved="load" />

  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-semibold text-text-primary">
        {{ t("subscriptions.title") }}
      </h1>

      <Button v-can="'subscriptions.assign'" @click="showForm = true">
        {{ t("subscriptions.actions.assign") }}
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
      @delete="onDelete"
    >
      <!-- Status -->
      <template #cell-status="{ row }">
        <Badge
          :variant="
            row.status === 'active'
              ? 'success'
              : row.status === 'expired'
                ? 'warning'
                : 'danger'
          "
        >
          {{ row.status }}
        </Badge>
      </template>

      <!-- Plan -->
      <template #cell-plan="{ row }">
        {{ row.plan?.name }}
      </template>

      <!-- Tenant -->
      <template #cell-tenant="{ row }">
        {{ row.tenant?.name }}
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { Trash2 } from "lucide-vue-next";
import { subscriptionService } from "@core/api/services/subscriptionService";
import { useAction } from "@core/composables/useAction";
import { DataTable, Button, Badge } from "@ui";
import { usePermissions } from "@core/permissions/usePermissions";
import { useI18n } from "vue-i18n";
import SubscriptionFormModal from "./components/SubscriptionFormModal.vue";

const { t } = useI18n();
const { can } = usePermissions();

const rows = ref<any[]>([]);
const loading = ref(false);
const showForm = ref(false);
const { execute } = useAction();

const columns = [
  { key: "tenant", label: t("subscriptions.fields.tenant") },
  { key: "plan", label: t("subscriptions.fields.plan") },
  { key: "status", label: t("subscriptions.fields.status") },
  { key: "start_date", label: t("subscriptions.fields.start") },
  { key: "end_date", label: t("subscriptions.fields.end") },
];

const tableActions = computed(() => {
  const actions: any[] = [];

  if (can("subscriptions.cancel")) {
    actions.push({
      label: "Cancel",
      event: "delete",
      icon: Trash2,
      title: "Cancel subscription",
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
    const res = await subscriptionService.getAll();
    rows.value = Array.isArray(res.data) ? res.data : [];
  } finally {
    loading.value = false;
  }
};

load();

const onDelete = (row: any) =>
  execute(async () => {
    if (!confirm(t("subscriptions.messages.confirmCancel"))) return;

    await subscriptionService.cancel(row.id);
    load();
  });
</script>
