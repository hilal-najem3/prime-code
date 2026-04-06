<script setup>
import { ToastContainer } from "@ui";
import PageLoader from "@ui/components/PageLoader.vue";
</script>

<template>
  <router-view />
  <ToastContainer />
  <PageLoader />
</template>
