import http from "../http";

export const planService = {
  getAll() {
    return http.get("/plans");
  },

  create(data: any) {
    return http.post("/plans", data);
  },

  update(id: number, data: any) {
    return http.put(`/plans/${id}`, data);
  },

  delete(id: number) {
    return http.delete(`/plans/${id}`);
  },
};
