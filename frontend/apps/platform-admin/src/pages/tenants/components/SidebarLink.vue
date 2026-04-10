<template>
  <router-link
    :to="link"
    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100"
    :class="isActive ? 'bg-gray-200 font-medium' : ''"
  >
    <component v-if="icon" :is="icon" class="h-4 w-4" />
    <span>{{ label }}</span>
  </router-link>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";

const props = defineProps<{
  to: string;
  label: string;
  icon?: any;
}>();

const route = useRoute();

/*
|--------------------------------------------------------------------------
| Build Link
|--------------------------------------------------------------------------
*/
const link = computed(() => ({
  path: `/tenants/${route.params.id}/${props.to}`,
}));

/*
|--------------------------------------------------------------------------
| Active State (FIXED)
|--------------------------------------------------------------------------
*/
const isActive = computed(() => {
  return route.path.endsWith(`/${props.to}`);
});
</script>
