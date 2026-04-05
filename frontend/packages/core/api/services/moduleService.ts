import http from "../http";

export const moduleService = {
  getAll(params?: any) {
    return http.get("/modules", { params });
  },

  create(data: any) {
    return http.post("/modules", data);
  },

  update(id: number, data: any) {
    return http.put(`/modules/${id}`, data);
  },

  delete(id: number) {
    return http.delete(`/modules/${id}`);
  },
};
