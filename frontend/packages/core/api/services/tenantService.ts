import http from "../http";

export const tenantService = {
  getAll() {
    return http.get("/tenants");
  },

  create(data: any) {
    return http.post("/tenants", data);
  },

  update(id: number, data: any) {
    return http.put(`/tenants/${id}`, data);
  },

  delete(id: number) {
    return http.delete(`/tenants/${id}`);
  },
};
