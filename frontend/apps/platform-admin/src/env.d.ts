/// <reference types="vite/client" />

import "vue-router";

declare module "*.vue" {
  import { DefineComponent } from "vue";
  const component: DefineComponent<{}, {}, any>;
  export default component;
}

declare module "vue-router" {
  interface RouteMeta {
    requiresAuth?: boolean;
    permission?: string;
  }
}
