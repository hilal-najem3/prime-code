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
    async login(payload: { email: string; password: string }) {
      try {
        this.loading = true;

        const response = await authService.login(payload);

        tokenService.set(response.token);

        this.user = response.user;
        this.permissions = response.permissions;
      } catch (error) {
        console.error("Login failed", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      try {
        await authService.logout();
      } catch (e) {
        // ignore API failure
      }

      tokenService.remove();

      this.user = null;
      this.permissions = [];
    },

    isAuthenticated(): boolean {
      return tokenService.has();
    },
  },
});
