<template>
  <div class="w-64 border-r bg-white p-4 space-y-2">
    <div class="text-sm text-gray-500 mb-4">
      {{ tenant?.name }}
    </div>

    <nav class="space-y-1">
      <SidebarLink to="general" label="General" />
      <SidebarLink to="subscription" label="Subscription" />
      <SidebarLink to="languages" label="Languages" />
      <SidebarLink to="modules" label="Modules" />
      <SidebarLink to="users" label="Users" />
      <SidebarLink to="roles" label="Roles" />
      <SidebarLink to="settings" label="Settings" />
    </nav>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  tenant: any;
}>();
</script>
