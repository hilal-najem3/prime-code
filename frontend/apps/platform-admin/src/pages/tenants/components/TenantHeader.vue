<template>
  <div
    class="h-16 border-b flex items-center justify-between px-6 bg-white shrink-0"
  >
    <div class="flex items-center gap-3">
      <button
        type="button"
        class="p-2 rounded-lg hover:bg-gray-100"
        aria-label="Toggle sidebar"
        @click="$emit('toggle-sidebar')"
      >
        <Menu class="w-5 h-5" />
      </button>

      <!-- NAME -->
      <h1 class="text-lg font-semibold p-3">
        Tenant {{ tenant?.name }} - Domain {{ tenant?.domain }}
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
import { Menu } from "lucide-vue-next";

defineProps<{
  tenant: any;
}>();

defineEmits<{
  (e: "toggle-sidebar"): void;
}>();
</script>
