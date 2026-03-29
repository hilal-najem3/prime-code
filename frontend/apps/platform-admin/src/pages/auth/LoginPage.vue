<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <Card class="w-full max-w-md p-8">
      <div class="flex justify-center mb-6">
        <img :src="Logo" alt="Prime Codes" class="h-12" />
      </div>

      <h2 class="text-2xl font-bold text-center mb-6">
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
  </div>
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
