<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <h2 class="text-lg font-semibold text-center text-text-primary">
        {{ t("subscriptions.actions.assign") }}
      </h2>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Tenant -->
        <FormField :error="getFirstError('tenant_id')">
          <SelectInput
            v-model="form.tenant_id"
            :options="tenantOptions"
            :placeholder="t('subscriptions.fields.tenant')"
          />
        </FormField>

        <!-- Plan -->
        <FormField :error="getFirstError('plan_id')">
          <SelectInput
            v-model="form.plan_id"
            :options="planOptions"
            :placeholder="t('subscriptions.fields.plan')"
          />
        </FormField>

        <div class="flex justify-end gap-2">
          <Button variant="secondary" @click="close">
            {{ t("subscriptions.actions.cancel") }}
          </Button>

          <Button :loading="loading" type="submit">
            {{ t("subscriptions.actions.assign") }}
          </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { subscriptionService } from "@core/api/services/subscriptionService";
import { planService } from "@core/api/services/planService";
import { tenantService } from "@core/api/services/tenantService";
import { tenantModuleService } from "@core/api/services/tenantModuleService";
import { useAction } from "@core/composables/useAction";
import { useModuleStore } from "@core/modules/moduleStore";
import { Modal, Button, SelectInput, FormField } from "@ui";
import { useToast } from "@ui";
import { useI18n } from "vue-i18n";

const moduleStore = useModuleStore();

interface Tenant {
  id: number;
  name: string;
}

interface Plan {
  id: number;
  name: string;
}

const { t } = useI18n();
const { show } = useToast();

defineProps<{ modelValue: boolean }>();
const emit = defineEmits(["update:modelValue", "saved"]);

const form = ref<{
  tenant_id: number | null;
  plan_id: number | null;
}>({
  tenant_id: null,
  plan_id: null,
});

const tenants = ref<Tenant[]>([]);
const plans = ref<Plan[]>([]);
const errors = ref<Record<string, string[]>>({});
const { loading, execute } = useAction();

const tenantOptions = computed(() =>
  tenants.value.map((t) => ({ label: t.name, value: t.id })),
);

const planOptions = computed(() =>
  plans.value.map((p) => ({ label: p.name, value: p.id })),
);

const load = async () => {
  const [tRes, pRes] = await Promise.all([
    tenantService.getAll(),
    planService.getAll(),
  ]);

  tenants.value = Array.isArray(tRes.data) ? tRes.data : [];
  plans.value = Array.isArray(pRes.data) ? pRes.data : [];
};

onMounted(load);

const close = () => emit("update:modelValue", false);
const getFirstError = (field: string) => errors.value[field]?.[0] || null;

const submit = async () => {
  errors.value = {};

  if (!form.value.tenant_id || !form.value.plan_id) {
    show(t("subscriptions.messages.selectTenantAndPlan"), "error");
    return;
  }

  const tenantId = form.value.tenant_id;
  const planId = form.value.plan_id;

  await execute(async () => {
    try {
      await subscriptionService.assign(tenantId, planId);

      const res = await tenantModuleService.get(tenantId);
      moduleStore.setModules(res.data);

      show(t("subscriptions.messages.assigned"), "success");

      emit("saved");
      close();
    } catch (e: any) {
      if (e.errors) {
        errors.value = e.errors;
      }
    }
  });
};
</script>
