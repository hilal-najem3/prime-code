<template>
  <!-- Overlay -->
  <div
    v-if="ui.sidebarOpen"
    @click="ui.closeSidebar()"
    class="fixed inset-0 bg-black/50 z-30 md:hidden"
  />

  <!-- Sidebar -->
  <aside
    :class="[
      'fixed md:static z-40 h-screen w-64 bg-gray-950 text-gray-300 flex flex-col border-r border-gray-800 transition-transform duration-300',
      ui.sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
    ]"
  >
    <!-- Logo -->
    <div class="h-16 flex items-center px-4 border-b border-gray-800 gap-3">
      <img :src="appConfig.logo" class="h-8 w-auto" />

      <!-- <span class="text-white font-semibold text-lg"> </span> -->
    </div>

    <!-- Menu -->
    <nav class="flex-1 p-3 space-y-1">
      <router-link
        v-for="item in filteredMenu"
        :key="item.route"
        :to="item.route"
        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200"
        active-class="bg-gray-800 text-white shadow-inner"
      >
        <!-- Icon -->
        <component
          :is="item.icon"
          class="w-5 h-5 opacity-70 group-hover:opacity-100 transition"
        />

        <!-- Label -->
        <span class="text-sm font-medium">
          {{ item.label }}
        </span>
      </router-link>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-800 text-xs text-gray-500">
      © {{ appConfig.name }}
    </div>
  </aside>
</template>

<script setup lang="ts">
import { menu } from "@/core/config/menu";
import { useMenu } from "@core/permissions/useMenu";
import { useUIStore } from "@/core/store/uiStore";
import { appConfig } from "@config/appConfig";

const { menu: filteredMenu } = useMenu(menu);
const ui = useUIStore();
</script>
