<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <h2 class="text-lg font-semibold text-center text-text-primary">
        {{ t("subscriptions.actions.assign") }}
      </h2>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Tenant -->
        <SelectInput
          v-model="form.tenant_id"
          :options="tenantOptions"
          :placeholder="t('subscriptions.fields.tenant')"
        />

        <!-- Plan -->
        <SelectInput
          v-model="form.plan_id"
          :options="planOptions"
          :placeholder="t('subscriptions.fields.plan')"
        />

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
import { Modal, Button, SelectInput } from "@ui";
import { useToast } from "@ui";
import { useI18n } from "vue-i18n";

interface Tenant {
  id: string;
  name: string;
}

interface Plan {
  id: string;
  name: string;
}

const { t } = useI18n();
const { show } = useToast();

const props = defineProps<{ modelValue: boolean }>();
const emit = defineEmits(["update:modelValue", "saved"]);

const form = ref({
  tenant_id: null,
  plan_id: null,
});

const tenants = ref<Tenant[]>([]);
const plans = ref<Plan[]>([]);
const loading = ref(false);

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

const submit = async () => {
  if (!form.value.tenant_id || !form.value.plan_id) {
    show(t("subscriptions.messages.selectBoth"), "error");
    return;
  }

  loading.value = true;

  try {
    await subscriptionService.assign(
      Number(form.value.tenant_id),
      Number(form.value.plan_id),
    );

    show(t("subscriptions.messages.assigned"), "success");

    emit("saved");
    close();
  } finally {
    loading.value = false;
  }
};
</script>
