import http from "../http";
import type { ApiResponse } from "../types/ApiResponse";

export const tenantService = {
  getAll(params?: any): Promise<ApiResponse> {
    return http.get("/platform/tenants", {
      params,
      meta: {
        showLoader: true,
      },
    });
  },

  get(id: string): Promise<ApiResponse> {
    return http.get(`/platform/tenants/${id}`, {
      meta: {
        showLoader: true,
      },
    });
  },

  create(data: any): Promise<ApiResponse> {
    return http.post("/platform/tenants", data, {
      meta: {
        showLoader: true,
      },
    });
  },

  update(id: string, data: any): Promise<ApiResponse> {
    return http.put(`/platform/tenants/${id}`, data, {
      meta: {
        showLoader: true,
      },
    });
  },

  delete(id: string, options?: any): Promise<ApiResponse> {
    return http.delete(`/platform/tenants/${id}`, {
      meta: {
        showLoader: true,
      },
      ...options,
    });
  },
};
