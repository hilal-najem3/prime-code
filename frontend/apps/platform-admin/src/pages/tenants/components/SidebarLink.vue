<template>
  <router-link
    :to="link"
    class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-100"
    :class="isActive ? 'bg-gray-200 font-medium' : ''"
  >
    {{ label }}
  </router-link>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";

const props = defineProps<{
  to: string;
  label: string;
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
