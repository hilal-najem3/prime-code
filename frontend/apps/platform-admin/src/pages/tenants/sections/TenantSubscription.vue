<template>
  <div class="max-w-2xl">
    <TenantSubscriptionCard
      v-if="tenant"
      :tenant="tenant"
      @updated="onUpdated"
    />
  </div>
</template>

<script setup lang="ts">
import TenantSubscriptionCard from "@/pages/tenants/components/TenantSubscriptionCard.vue";

const props = defineProps<{
  tenant: any;
}>();

const emit = defineEmits(["updated"]);

/*
|--------------------------------------------------------------------------
| Handle update
|--------------------------------------------------------------------------
*/
const onUpdated = () => {
  emit("updated"); // bubble up to reload tenant
};
</script>
