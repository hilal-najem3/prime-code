import { defineStore } from "pinia";
import { authService } from "@core/auth/authService";
import { tokenService } from "@core/auth/tokenService";
import type { User } from "@core/auth/authTypes";

interface AuthState {
  user: User | null;
  permissions: string[];
  loading: boolean;
}

export const useAuthStore = defineStore("auth", {
  state: (): AuthState => ({
    user: null,
    permissions: [],
    loading: false,
  }),

  actions: {
    async init() {
      if (!tokenService.has()) return;

      try {
        this.loading = true;

        const user = await authService.me();

        this.user = user;

        // OPTIONAL: if backend returns permissions separately
        // you may need a dedicated endpoint or include in /me
      } catch (e) {
        this.logout(); // invalid token
      } finally {
        this.loading = false;
      }
    },
    async login(payload: { email: string; password: string }) {
      try {
        this.loading = true;

        const response = await authService.login(payload);

        const data = response.data;

        tokenService.set(data.token);
        this.user = data.user;
        this.permissions = data.permissions;
      } catch (error) {
        console.error("Login failed", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      if (tokenService.has()) {
        try {
          await authService.logout();
        } catch {}
      }

      tokenService.remove();
      this.user = null;
      this.permissions = [];

      window.location.href = "/login";
    },

    isAuthenticated(): boolean {
      return tokenService.has();
    },
  },
});
