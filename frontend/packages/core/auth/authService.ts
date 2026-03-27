import http from "../api/http";
import type { LoginPayload, AuthResponse } from "./authTypes";

export const authService = {
  async login(payload: LoginPayload): Promise<AuthResponse> {
    return http.post("/login", payload);
  },

  async logout(): Promise<void> {
    await http.post("/logout");
  },

  async me(): Promise<AuthResponse["user"]> {
    return http.get("/me");
  },
};
