<template>
  <Card class="space-y-6">
    <!-- Title -->
    <h2 class="text-lg font-semibold text-text-primary">
      {{ t("subscriptions.title") }}
    </h2>

    <!-- Current Plan -->
    <div v-if="currentPlan" class="text-sm text-text-secondary">
      {{ t("subscriptions.current") }}:
      <span class="text-text-primary font-medium">
        {{ currentPlan.name }}
      </span>
    </div>

    <!-- Plan Select -->
    <div>
      <SelectInput
        v-model="selectedPlan"
        :options="planOptions"
        :placeholder="t('subscriptions.selectPlan')"
      />
    </div>

    <!-- Actions -->
    <div class="flex justify-end">
      <Button :loading="loading" @click="assignPlan">
        {{ t("subscriptions.actions.assign") }}
      </Button>
    </div>
  </Card>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { Card, Button, SelectInput } from "@ui";
import { useToast } from "@ui";
import { useI18n } from "vue-i18n";
import { planService } from "@core/api/services/planService";
import { subscriptionService } from "@core/api/services/subscriptionService";

/*
|--------------------------------------------------------------------------
| i18n / Toast
|--------------------------------------------------------------------------
*/
const { t } = useI18n();
const { show } = useToast();

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  tenant: any;
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/
const plans = ref<any[]>([]);
const selectedPlan = ref<number | null>(null);
const loading = ref(false);

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/
const planOptions = computed(() =>
  plans.value.map((p) => ({
    label: `${p.name} ($${p.price})`,
    value: p.id,
  })),
);

const currentPlan = computed(() => props.tenant?.subscription?.plan);

/*
|--------------------------------------------------------------------------
| Load Plans
|--------------------------------------------------------------------------
*/
const load = async () => {
  const res = await planService.getAll();

  plans.value = Array.isArray(res.data) ? res.data : [];

  if (currentPlan.value) {
    selectedPlan.value = currentPlan.value.id;
  }
};

/*
|--------------------------------------------------------------------------
| Assign Plan
|--------------------------------------------------------------------------
*/
const assignPlan = async () => {
  if (!selectedPlan.value) return;

  loading.value = true;

  try {
    await subscriptionService.assign(props.tenant.id, selectedPlan.value);

    show(t("subscriptions.messages.assigned"), "success");
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
};

/*
|--------------------------------------------------------------------------
| Init
|--------------------------------------------------------------------------
*/
onMounted(load);
</script>
