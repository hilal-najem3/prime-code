<template>
  <div class="w-64 border-r bg-white p-4 space-y-2 h-full">
    <div class="text-sm text-gray-500 mb-4">Tenant: {{ tenant?.name }}</div>

    <nav class="space-y-1">
      <SidebarLink to="general" label="General" :icon="LayoutDashboard" />
      <SidebarLink to="languages" label="Languages" :icon="Languages" />
      <SidebarLink to="pages" label="Pages" :icon="FileText" />
      <SidebarLink to="roles" label="Roles" :icon="Shield" />
      <SidebarLink to="settings" label="Settings" :icon="SettingsIcon" />
      <SidebarLink to="subscription" label="Subscription" :icon="CreditCard" />
      <SidebarLink to="users" label="Users" :icon="Users" />
    </nav>
  </div>
</template>

<script setup lang="ts">
import SidebarLink from "./SidebarLink.vue";
import {
  LayoutDashboard,
  Languages,
  FileText,
  Shield,
  Settings as SettingsIcon,
  CreditCard,
  Users,
} from "lucide-vue-next";

defineProps<{
  tenant: any;
}>();
</script>
