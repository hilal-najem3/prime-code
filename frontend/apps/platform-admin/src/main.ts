import { createApp } from "vue";
import App from "./App.vue";

import { i18n } from "./i18n";

import { router } from "@/core/router";
import { store } from "@/core/store";

import { canDirective } from "@core/permissions/canDirective";

import { useAuthStore } from "@/core/store/authStore";

import "./assets/main.css";

const app = createApp(App);

app.use(router);
app.use(store);
app.use(i18n);

const auth = useAuthStore();
await auth.init();

app.directive("can", canDirective);

app.mount("#app");
