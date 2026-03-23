import { createApp } from "vue";
import App from "./App.vue";

import { i18n } from "./i18n";

import { router } from "@/core/router";
import { store } from "@/core/store";

const app = createApp(App);

app.use(router);
app.use(store);
app.use(i18n);

app.mount("#app");
