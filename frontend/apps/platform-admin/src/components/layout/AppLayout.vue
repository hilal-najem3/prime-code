<template>
  <div class="flex h-screen bg-gray-100 overflow-hidden">
    <Sidebar />

    <div class="flex flex-col flex-1 overflow-hidden">
      <Topbar />

      <main class="flex-1 overflow-auto p-6">
        <div class="max-w-7xl mx-auto">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import Sidebar from "./Sidebar.vue";
import Topbar from "./Topbar.vue";
import { onMounted } from "vue";
import { useUIStore } from "@/core/store/uiStore";

const ui = useUIStore();

onMounted(() => {
  ui.closeSidebar();
});
</script>
