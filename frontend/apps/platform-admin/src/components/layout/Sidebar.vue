<template>
  <aside
    class="w-64 h-screen bg-gray-950 text-gray-300 flex flex-col border-r border-gray-800"
  >
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 border-b border-gray-800">
      <span class="text-white font-semibold text-lg tracking-wide">
        Prime Codes
      </span>
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
      © Prime Codes
    </div>
  </aside>
</template>

<script setup lang="ts">
import { menu } from "@/core/config/menu";
import { useMenu } from "@core/permissions/useMenu";

const { menu: filteredMenu } = useMenu(menu);
</script>
