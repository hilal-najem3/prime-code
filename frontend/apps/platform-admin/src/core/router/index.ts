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
        children: [
          {
            path: "",
            component: () => import("@/pages/tenants/TenantsPage.vue"),
            meta: { permission: "tenants.index" },
          },
          {
            path: ":id",
            component: () => import("@/pages/tenants/TenantDetailsPage.vue"),
            meta: { permission: "tenants.update" },

            children: [
              {
                path: "",
                redirect: "general",
              },
              {
                path: "general",
                component: () =>
                  import("@/pages/tenants/sections/TenantGeneral.vue"),
              },
              {
                path: "subscription",
                component: () =>
                  import("@/pages/tenants/sections/TenantSubscription.vue"),
              },
              {
                path: "languages",
                component: () =>
                  import("@/pages/tenants/sections/TenantLanguages.vue"),
              },
              {
                path: "pages",
                component: () =>
                  import("@/pages/tenants/sections/TenantPage.vue"),
              },
              {
                path: "users",
                component: () =>
                  import("@/pages/tenants/sections/TenantUsers.vue"),
              },
              {
                path: "roles",
                component: () =>
                  import("@/pages/tenants/sections/TenantRoles.vue"),
              },
              {
                path: "settings",
                component: () =>
                  import("@/pages/tenants/sections/TenantSettings.vue"),
              },
            ],
          },
        ],
      },
      {
        path: "modules",
        component: () => import("@/pages/modules/ModulesPage.vue"),
        meta: { permission: "modules.index" },
      },

      {
        path: "plans",
        component: () => import("@/pages/plans/PlansPage.vue"),
        meta: { permission: "plans.index" },
      },

      {
        path: "subscriptions",
        component: () => import("@/pages/subscriptions/SubscriptionsPage.vue"),
        meta: { permission: "subscriptions.index" },
      },

      {
        path: "users",
        component: () => import("@/pages/users/UsersPage.vue"),
        meta: { permission: "platform.users.index" },
      },

      {
        path: "roles",
        component: () => import("@/pages/roles/RolesPage.vue"),
        meta: { permission: "platform.roles.index" },
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
