<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <Card class="w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center">
        {{ t("auth.login") }}
      </h2>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <TextInput v-model="email" :placeholder="t('auth.email')" />

        <PasswordInput v-model="password" :placeholder="t('auth.password')" />

        <Button>
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
