import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/",
    redirect: "/dashboard",
  },
  {
    path: "/dashboard",
    name: "dashboard",
    component: () => import("@/pages/dashboard/DashboardPage.vue"),
  },
];

export const router = createRouter({
  history: createWebHistory(),
  routes,
});
