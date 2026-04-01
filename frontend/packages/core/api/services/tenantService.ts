import http from "../http";
import type { ApiResponse } from "../types/ApiResponse";

export const tenantService = {
  getAll(params?: any): Promise<ApiResponse> {
    return http.get("/platform/tenants", { params });
  },

  get(id: number): Promise<ApiResponse> {
    return http.get(`/platform/tenants/${id}`);
  },

  create(data: any): Promise<ApiResponse> {
    return http.post("/platform/tenants", data);
  },

  update(id: number, data: any): Promise<ApiResponse> {
    return http.put(`/platform/tenants/${id}`, data);
  },

  delete(id: number): Promise<ApiResponse> {
    return http.delete(`/platform/tenants/${id}`);
  },
};
