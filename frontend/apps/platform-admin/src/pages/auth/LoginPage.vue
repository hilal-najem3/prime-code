<template>
  <Card class="space-y-6 bg-white">
    <!-- Logo -->
    <div class="flex flex-col items-center space-y-3">
      <img :src="appConfig.logo" class="h-16" />

      <div class="text-center">
        <h1 class="text-lg font-semibold">
          {{ appConfig.name }}
        </h1>

        <p class="text-sm text-text-secondary">
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

      <PasswordInput
        v-model="password"
        :placeholder="t('auth.password')"
        :translations="{
          show: t('common.show'),
          hide: t('common.hide'),
        }"
      />

      <Button
        fullWidth
        :loading="auth.loading"
        :label="t('auth.login')"
        :loadingLabel="t('common.logging_in')"
        :translations="{
          loading: t('common.logging_in'),
        }"
      >
        {{ t("auth.login") }}
        <Spinner v-if="auth.loading" size="sm" variant="white" />
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
import { useToast } from "@ui";

const { show } = useToast();

const { t } = useI18n();

const email = ref("");
const password = ref("");

const router = useRouter();
const auth = useAuthStore();

const handleLogin = async () => {
  try {
    await auth.login({
      email: email.value,
      password: password.value,
    });
    show(t("auth.login_success"), "success");

    await router.push("/dashboard");
  } catch (error: any) {
    show(error.message || t("auth.login_failed"), "error");
  }
};
</script>
