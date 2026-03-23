import http from "../api/http";

export const authService = {
  login(data: { email: string; password: string }) {
    return http.post("/login", data);
  },

  logout() {
    return http.post("/logout");
  },

  me() {
    return http.get("/me");
  },
};
