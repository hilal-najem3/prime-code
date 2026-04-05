import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/core/store/authStore";
import AppLayout from "@/components/layout/AppLayout.vue";

const routes = [
  {
    path: "/",
    redirect: "/dashboard",
  },
  {
    path: "/",
    component: AppLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: "dashboard",
        component: () => import("@/pages/dashboard/DashboardPage.vue"),
      },
      {
        path: "tenants",
        component: () => import("@/pages/tenants/TenantsPage.vue"),
        meta: { permission: "tenants.index" },
      },
      {
        path: "modules",
        component: () => import("@/pages/modules/ModulesPage.vue"),
        meta: { permission: "modules.index" },
      },
    ],
  },
  {
    path: "/login",
    component: () => import("@/layouts/AuthLayout.vue"),
    children: [
      {
        path: "",
        component: () => import("@/pages/auth/LoginPage.vue"),
      },
    ],
  },
];

export const router = createRouter({
  history: createWebHistory(),
  routes,
});

/**
|--------------------------------------------------------------------------
| Route Guard
|--------------------------------------------------------------------------
*/
router.beforeEach((to) => {
  const auth = useAuthStore();

  if (to.path === "/login" && auth.isAuthenticated) {
    return "/dashboard";
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return "/login";
  }

  if (to.meta.permission && !auth.can(to.meta.permission as string)) {
    return "/dashboard";
  }
});
