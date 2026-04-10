<template>
  <div
    class="h-16 border-b flex items-center justify-between px-6 bg-white shrink-0"
  >
    <div class="p-5">
      <!-- NAME -->
      <h1 class="text-lg font-semibold">
        {{ tenant?.name }} - {{ tenant?.domain }}
      </h1>
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
