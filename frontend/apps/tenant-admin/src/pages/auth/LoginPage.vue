<template>
  <Card variant="white" class="space-y-6">
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
      <FormField :error="errors.email">
        <TextInput
          v-model="form.email"
          autocomplete="email"
          :placeholder="t('auth.email')"
          autofocus
        />
      </FormField>

      <FormField :error="errors.password">
        <PasswordInput
          v-model="form.password"
          autocomplete="current-password"
          :placeholder="t('auth.password')"
          :translations="{
            show: t('common.show'),
            hide: t('common.hide'),
          }"
        />
      </FormField>

      <Button
        fullWidth
        :loading="loading"
        :label="t('auth.login')"
        :loadingLabel="t('common.logging_in')"
        :translations="{
          loading: t('common.logging_in'),
        }"
      >
        {{ t("auth.login") }}
      </Button>
    </form>
  </Card>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/core/store/authStore";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import { TextInput, PasswordInput, Button, Card, FormField } from "@ui";
import { appConfig } from "@config/appConfig";
import { useToast } from "@ui";
import { useForm } from "@core/composables/useForm";

const { form, errors, loading, submit, clearError } = useForm({
  email: "",
  password: "",
});

const { show } = useToast();

const { t } = useI18n();

const router = useRouter();
const auth = useAuthStore();

const handleLogin = async () => {
  try {
    console.log("Submitting form with values:", form);
    await auth.login(form);
    show(t("auth.login_success"), "success");

    await router.push("/dashboard");
  } catch (error: any) {
    show(error.message || t("auth.login_failed"), "error");
  }
};
</script>
