import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/core/store/authStore";

const routes = [
  {
    path: "/",
    redirect: "/dashboard",
  },
  {
    path: "/dashboard",
    name: "dashboard",
    component: () => import("@/pages/dashboard/DashboardPage.vue"),
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: "/login",
    name: "login",
    component: () => import("@/pages/auth/LoginPage.vue"),
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
router.beforeEach((to, from, next) => {
  const auth = useAuthStore();

  if (auth.loading) {
    return next(false);
  }

  if (to.path === "/login" && auth.isAuthenticated()) {
    return next("/dashboard");
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated()) {
    return next("/login");
  }

  if (to.meta.permission) {
    const hasPermission = auth.permissions.includes(
      to.meta.permission as string,
    );

    if (!hasPermission) {
      return next("/dashboard");
    }
  }

  next();
});
