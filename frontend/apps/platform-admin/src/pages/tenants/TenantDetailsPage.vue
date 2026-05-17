<template>
  <div class="flex h-screen bg-bg-base">
    <!-- Sidebar -->
    <TenantSidebar v-if="sidebarOpen" :tenant="tenant" />

    <!-- Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <TenantHeader :tenant="tenant" @toggle-sidebar="toggleSidebar" />

      <div class="px-6 pt-4">
        <RouterLink
          to="/tenants"
          class="inline-flex items-center justify-center rounded-lg border border-border bg-surface-primary px-4 py-2 font-medium text-text-primary transition hover:bg-bg-primary"
        >
          Back to Tenants
        </RouterLink>
      </div>

      <!-- Page Content -->
      <div class="p-6 overflow-auto flex justify-center items-center">
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
const sidebarOpen = ref(true);

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

onMounted(async () => {
  const res = await tenantService.get(route.params.id);
  tenant.value = res.data;
});

const reloadTenant = async () => {
  const res = await tenantService.get(route.params.id);
  tenant.value = res.data;
};
</script>
