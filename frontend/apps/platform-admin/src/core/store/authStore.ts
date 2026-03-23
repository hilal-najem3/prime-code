import { defineStore } from "pinia";
import { authService } from "@core/auth/authService";
import { authStorage } from "@core/auth/authStorage";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null as any,
    token: authStorage.getToken(),
    permissions: [] as string[],
  }),

  actions: {
    async login(payload: { email: string; password: string }) {
      const res = await authService.login(payload);

      this.token = res.data.token;
      this.user = res.data.user;
      this.permissions = res.data.permissions || [];

      authStorage.setToken(this.token);
    },

    logout() {
      this.user = null;
      this.token = null;
      this.permissions = [];

      authStorage.clearToken();

      window.location.href = "/login";
    },

    isAuthenticated() {
      return !!this.token;
    },
  },
});
