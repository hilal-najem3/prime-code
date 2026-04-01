import http from "../http";

export const tenantService = {
  getAll(params?: any) {
    return http.get("/platform/tenants", { params });
  },

  get(id: number) {
    return http.get(`/platform/tenants/${id}`);
  },

  create(data: any) {
    return http.post("/platform/tenants", data);
  },

  update(id: number, data: any) {
    return http.put(`/platform/tenants/${id}`, data);
  },

  delete(id: number) {
    return http.delete(`/platform/tenants/${id}`);
  },
};
