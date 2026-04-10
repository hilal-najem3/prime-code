<template>
  <div class="h-16 border-b flex items-center justify-between px-6 bg-white">
    <div>
      <!-- CONTEXT -->
      <p class="text-xs text-gray-400 uppercase">Tenant Management</p>

      <!-- NAME -->
      <h1 class="text-lg font-semibold">
        {{ tenant?.name }}
      </h1>

      <!-- DOMAIN -->
      <p class="text-sm text-gray-500">
        {{ tenant?.domain }}
      </p>
    </div>

    <div>
      <Badge :variant="tenant?.status === 'active' ? 'success' : 'warning'">
        {{ tenant?.status }}
      </Badge>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Badge } from "@ui";

defineProps<{
  tenant: any;
}>();
</script>
