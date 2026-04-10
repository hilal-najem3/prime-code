<template>
  <div class="flex h-full">
    <!-- Sidebar -->
    <TenantSidebar :tenant="tenant" />

    <!-- Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <TenantHeader :tenant="tenant" />

      <!-- Page Content -->
      <div class="p-6">
        <router-view :tenant="tenant" @updated="reloadTenant" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { tenantService } from "@core/api/services/tenantService";

import TenantSidebar from "./components/TenantSidebar.vue";
import TenantHeader from "./components/TenantHeader.vue";

const route = useRoute();
const tenant = ref<any>(null);

onMounted(async () => {
  const res = await tenantService.get(Number(route.params.id));
  tenant.value = res.data;
});

const reloadTenant = async () => {
  const res = await tenantService.get(Number(route.params.id));
  tenant.value = res.data;
};
</script>
