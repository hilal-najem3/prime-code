<template>
  <Card class="p-8 bg-white text-gray-900">
    <div class="flex justify-center mb-6">
      <img :src="Logo" class="h-25" />
    </div>

    <h2 class="text-2xl font-semibold text-center mb-6 mt-2">
      {{ t("auth.login") }}
    </h2>

    <form @submit.prevent="handleLogin" class="space-y-4">
      <TextInput v-model="email" :placeholder="t('auth.email')" />

      <PasswordInput v-model="password" :placeholder="t('auth.password')" />

      <Button class="w-full">
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
import { Logo } from "@ui/assets";

const { t } = useI18n();

const email = ref("");
const password = ref("");

const auth = useAuthStore();
const handleLogin = async () => {
  const router = useRouter();

  await auth.login({
    email: email.value,
    password: password.value,
  });

  await router.push("/dashboard");
};
</script>
