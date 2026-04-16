<template>
  <Card class="space-y-6 max-w-2xl w-3/4" :variant="'elevated'">
    <h2 class="text-lg font-semibold text-text-primary">
      {{ t("subscriptions.title") || "Subscription" }}
    </h2>

    <div class="rounded-lg border border-border bg-bg-primary p-4 space-y-2">
      <p class="text-sm text-text-secondary">
        {{ t("subscriptions.current") || "Current Plan" }}:
        <span class="font-medium text-text-primary">
          {{ currentPlanName }}
        </span>
      </p>

      <p class="text-sm text-text-secondary">
        {{ t("subscriptions.fields.status") || "Status" }}:
        <span class="font-medium text-text-primary">
          {{ currentStatus }}
        </span>
      </p>
    </div>

    <form @submit.prevent="submit" class="space-y-4">
      <FormField :error="getFirstError('plan_id')">
        <SelectInput
          v-model="form.plan_id"
          :options="planOptions"
          :placeholder="t('subscriptions.fields.plan') || 'Select Plan'"
        />
      </FormField>

      <div class="flex justify-end">
        <Button :loading="loading" type="submit">
          {{ t("subscriptions.actions.assign") || "Assign Plan" }}
        </Button>
      </div>
    </form>
  </Card>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from "vue";
import { Card, Button, SelectInput, FormField } from "@ui";
import { useI18n } from "vue-i18n";
import { useToast } from "@ui";
import { useAction } from "@core/composables/useAction";
import { planService } from "@core/api/services/planService";
import { tenantSubscriptionService } from "@core/api/services/tenants/subscriptionService";
import { tenantService } from "@core/api/services/tenantService";

const props = defineProps<{
  tenant: any;
}>();

const emit = defineEmits<{
  (e: "updated"): void;
}>();

const { t } = useI18n();
const { show } = useToast();
const { loading, execute } = useAction();

const plans = ref<any[]>([]);
const subscription = ref<any>(null);
const errors = ref<Record<string, string[]>>({});

const form = reactive<{
  plan_id: number | string | null;
}>({
  plan_id: null,
});

const planOptions = computed(() =>
  plans.value.map((plan) => ({
    label: `${plan.name} ($${plan.price ?? 0})`,
    value: plan.id,
  })),
);

const currentPlanName = computed(() => {
  return (
    subscription.value?.plan?.name ||
    props.tenant?.subscription?.plan?.name ||
    "No plan assigned"
  );
});

const currentStatus = computed(() => {
  return (
    subscription.value?.status || props.tenant?.subscription?.status || "No subscription"
  );
});

watch(
  () => props.tenant?.subscription?.plan?.id,
  (planId) => {
    if (!subscription.value?.plan?.id) {
      form.plan_id = planId ?? null;
    }
  },
  { immediate: true },
);

const getFirstError = (field: string) => errors.value[field]?.[0] || null;

const loadPlans = async () => {
  const res = await planService.getAll();
  plans.value = Array.isArray(res.data) ? res.data : [];
};

const loadTenantSubscription = async () => {
  if (!props.tenant?.id) return;

  const res = await tenantService.get(Number(props.tenant.id));
  subscription.value = res?.data?.subscription ?? null;
  form.plan_id = subscription.value?.plan?.id ?? props.tenant?.subscription?.plan?.id ?? null;
};

watch(
  () => props.tenant?.id,
  () => {
    loadTenantSubscription();
  },
  { immediate: true },
);

onMounted(async () => {
  await loadPlans();
});

const submit = async () => {
  if (!props.tenant?.id) return;

  errors.value = {};

  if (!form.plan_id) {
    errors.value = {
      plan_id: [t("subscriptions.selectPlan") || "Please select a plan"],
    };
    return;
  }

  await execute(async () => {
    try {
      await tenantSubscriptionService.assign(props.tenant.id, {
        plan_id: form.plan_id as string | number,
      });
      await loadTenantSubscription();

      show(t("subscriptions.messages.assigned") || "Plan assigned", "success");
      emit("updated");
    } catch (error: any) {
      if (error.errors) {
        errors.value = error.errors;
      }
    }
  });
};
</script>
