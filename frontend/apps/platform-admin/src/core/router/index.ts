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
