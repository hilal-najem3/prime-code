import http from "../http";

export const profileService = {
  get() {
    return http.get("/auth/profile");
  },

  update(data: { name: string; email: string }) {
    return http.put("/auth/profile", data);
  },

  updatePassword(data: {
    current_password: string;
    password: string;
    password_confirmation: string;
  }) {
    return http.put("/auth/password", data);
  },
};
