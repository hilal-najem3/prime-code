import http from "../api/http";
import type { LoginPayload, ApiResponse, AuthData } from "./authTypes";

export const authService = {
  async login(payload: LoginPayload): Promise<ApiResponse<AuthData>> {
    return http.post("/auth/login", payload);
  },

  async me(): Promise<ApiResponse<AuthData>> {
    return http.get("/auth/me");
  },

  async logout(): Promise<void> {
    await http.post("/auth/logout");
  },

  async refresh(refresh_token: string) {
    return http.post<ApiResponse<AuthData>>(
      "/auth/refresh",
      { refresh_token },
      { meta: { skipAuth: true, showErrorToast: false } },
    );
  },
};
