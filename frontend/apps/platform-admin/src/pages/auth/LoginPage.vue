<template>
  <div>
    <h2>{{ t("auth.login") }}</h2>

    <form @submit.prevent="handleLogin">
      <input v-model="email" :placeholder="t('auth.email')" />
      <input
        v-model="password"
        type="password"
        :placeholder="t('auth.password')"
      />

      <button type="submit">{{ t("auth.login") }}</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useAuthStore } from "@/core/store/authStore";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

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
