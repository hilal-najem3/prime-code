// path : frontend/apps/platform-admin/src/main.ts
import App from "./App.vue";

import { createApp } from "vue";

import { i18n } from "./i18n";

import { router } from "@/core/router";
import { store } from "@/core/store";

import { canDirective } from "@core/permissions/canDirective";

import { useAuthStore } from "@/core/store/authStore";

import "./assets/main.css";
import { theme } from "@config/theme";

const root = document.documentElement;

root.style.setProperty("--color-bg-primary", theme.colors.background.primary);
root.style.setProperty(
  "--color-bg-secondary",
  theme.colors.background.secondary,
);

root.style.setProperty("--color-text-primary", theme.colors.text.primary);
root.style.setProperty("--color-text-secondary", theme.colors.text.secondary);

root.style.setProperty("--color-border", theme.colors.border);

root.style.setProperty("--color-brand-primary", theme.colors.brand.primary);
root.style.setProperty("--color-brand-secondary", theme.colors.brand.secondary);

const app = createApp(App);

app.use(router);
app.use(store);
app.use(i18n);

const auth = useAuthStore();
auth.init();

app.directive("can", canDirective);

app.mount("#app");
