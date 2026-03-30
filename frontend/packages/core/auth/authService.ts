import http from "../api/http";
import type { LoginPayload, ApiResponse, AuthData } from "./authTypes";

export const authService = {
  async login(payload: LoginPayload): Promise<ApiResponse<AuthData>> {
    return http.post("/auth/login", payload);
  },

  async logout(): Promise<void> {
    await http.post("/auth/logout");
  },

  async me(): Promise<ApiResponse["user"]> {
    return http.get("/auth/me");
  },
};
