import { defineStore } from "pinia";
import { authService } from "@core/auth/authService";
import { tokenService } from "@core/auth/tokenService";
import type { User } from "@core/auth/authTypes";
import type { LoginPayload } from "@core/auth/authTypes";
import { permissionService } from "@core/permissions/permissionService";

interface AuthState {
  user: User | null;
  permissions: string[];
  loading: boolean;
  role: string | null;
  token: string | null;
}

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null as User | null,
    permissions: [] as string[],
    role: null as string | null,
    loading: false,
    token: tokenService.get() as string | null, // ✅ ADD THIS
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,

    userName: (state) => state.user?.name || "",

    userEmail: (state) => state.user?.email || "",

    roles: (state) => state.user?.roles || [],

    roleSlugs: (state) => state.user?.roles.map((r) => r.slug) || [],

    hasRole: (state) => (role: string) => {
      return (
        state.role === role || state.user?.roles.some((r) => r.slug === role)
      );
    },

    can: (state) => (permission: string) => {
      return permissionService.can(state.permissions, permission);
    },
  },

  actions: {
    async init() {
      const token = tokenService.get();

      if (!token) return;

      try {
        this.loading = true;

        const response = await authService.me();
        const data = response.data;

        this.token = token;

        this.user = {
          ...data.user,
          roles: data.user.roles || [],
          permissions: data.permissions || [],
        };

        this.permissions = data.permissions || [];
        this.role = data.role || null;
      } catch (e) {
        // token invalid
        this.logout();
      } finally {
        this.loading = false;
      }
    },

    async login(payload: LoginPayload) {
      try {
        this.loading = true;

        const response = await authService.login(payload);
        const data = response.data;
        const accessToken = data.token ?? data.access_token;

        if (!accessToken) {
          throw new Error("Login response is missing an access token.");
        }

        tokenService.set(accessToken, data.refresh_token);
        this.token = accessToken;

        this.user = {
          ...data.user,
          roles: data.user.roles || [],
          permissions: data.permissions || [],
        };

        this.permissions = data.permissions || [];
        this.role = data.role || null;
      } catch (error: any) {
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async me() {
      try {
        this.loading = true;
        const response = await authService.me();
        const data = response.data;

        this.user = {
          ...data.user,
          roles: data.user.roles || [],
          permissions: data.permissions || [],
        };

        this.permissions = data.permissions || [];
        this.role = data.role || null;
      } catch (error: any) {
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      if (tokenService.get()) {
        try {
          await authService.logout();
        } catch {}
      }
      tokenService.remove();

      this.token = null; // ✅ IMPORTANT
      this.user = null;
      this.permissions = [];
      this.role = null;

      window.location.href = "/login";
    },
  },
});
