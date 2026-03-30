<template>
  <Card class="space-y-6">
    <!-- Logo -->
    <div class="flex flex-col items-center space-y-3">
      <img :src="appConfig.logo" class="h-16" />

      <div class="text-center">
        <h1 class="text-lg font-semibold">
          {{ appConfig.name }}
        </h1>

        <p class="text-sm text-gray-500">
          {{ appConfig.tagline }}
        </p>
      </div>
    </div>

    <!-- Title -->
    <h2 class="text-xl font-semibold text-center">
      {{ t("auth.login") }}
    </h2>

    <!-- Form -->
    <form
      @submit.prevent="handleLogin"
      class="space-y-4"
      :class="{ 'opacity-50 pointer-events-none': auth.loading }"
    >
      <TextInput v-model="email" :placeholder="t('auth.email')" autofocus />

      <PasswordInput v-model="password" :placeholder="t('auth.password')" />

      <Button class="w-full" :loading="auth.loading">
        {{ t("auth.login") }}
      </Button>
    </form>
  </Card>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useAuthStore } from "@/core/store/authStore";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import { TextInput, PasswordInput, Button, Card } from "@ui";
import { appConfig } from "@config/appConfig";

const { t } = useI18n();

const email = ref("");
const password = ref("");

const router = useRouter();
const auth = useAuthStore();

const handleLogin = async () => {
  await auth.login({
    email: email.value,
    password: password.value,
  });

  await router.push("/dashboard");
};
</script>
