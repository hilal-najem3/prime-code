import { defineStore } from "pinia";
import { authService } from "@core/auth/authService";
import { tokenService } from "@core/auth/tokenService";
import type { User } from "@core/auth/authTypes";
import type { LoginPayload } from "@core/auth/authTypes";

interface AuthState {
  user: User | null;
  permissions: string[];
  loading: boolean;
  role: string | null;
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
      return state.permissions.includes(permission);
    },
  },

  actions: {
    async init() {
      if (!tokenService.get()) return;

      try {
        this.loading = true;
      } catch (e) {
        this.logout(); // invalid token
      } finally {
        this.loading = false;
      }
    },

    async login(payload: LoginPayload) {
      try {
        this.loading = true;

        const response = await authService.login(payload);

        const data = response.data;

        /*
        |--------------------------------------------------------------------------
        | Tokens
        |--------------------------------------------------------------------------
        */
        tokenService.set(data.token, data.refresh_token);
        // ✅ IMPORTANT
        this.token = data.token;

        /*
        |--------------------------------------------------------------------------
        | User State
        |--------------------------------------------------------------------------
        */
        this.user = {
          ...data.user,
          roles: data.user.roles || [],
          permissions: data.permissions || [],
        };

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */
        this.permissions = data.permissions || [];

        /*
        |--------------------------------------------------------------------------
        | Role (PRIMARY ROLE)
        |--------------------------------------------------------------------------
        */
        this.role = data.role || null;
      } catch (error) {
        console.error("Login failed", error);
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
