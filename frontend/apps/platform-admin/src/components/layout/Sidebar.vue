<template>
  <aside class="w-64 h-screen bg-gray-900 text-white">
    <ul class="p-4 space-y-2">
      <li v-for="item in filteredMenu" :key="item.route">
        <router-link
          :to="item.route"
          class="block px-4 py-2 rounded hover:bg-gray-700"
        >
          {{ item.label }}
        </router-link>
      </li>
    </ul>
  </aside>
</template>

<script setup lang="ts">
import { menu } from "@/core/config/menu";
import { useMenu } from "@core/permissions/useMenu";

const { menu: filteredMenu } = useMenu(menu);
</script>
