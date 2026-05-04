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
        path: "users",
        component: () => import("@/pages/users/UsersPage.vue"),
        meta: { permission: "tenant.users.index" },
      },

      {
        path: "roles",
        component: () => import("@/pages/roles/RolesPage.vue"),
        meta: { permission: "roles.index" },
      },
      {
        path: "patients",
        component: () => import("@/pages/patients/PatientsPage.vue"),
        meta: { permission: "patients.index" },
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
