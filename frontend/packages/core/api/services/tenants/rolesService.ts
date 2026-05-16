import http from "../../http";
import type { ApiResponse } from "../../types/ApiResponse";

export const tenantRolesService = {
  getAll(tenantId: number | string, params?: any): Promise<ApiResponse> {
    return http.get(`/platform/tenant/${tenantId}/roles`, {
      params,
      meta: {
        showLoader: true,
      },
    });
  },

  get(tenantId: number | string, id: string): Promise<ApiResponse> {
    return http.get(`/platform/tenant/${tenantId}/roles/${id}`, {
      meta: {
        showLoader: true,
      },
    });
  },

  create(tenantId: number | string, data: any): Promise<ApiResponse> {
    return http.post(`/platform/tenant/${tenantId}/roles`, data, {
      meta: {
        showLoader: true,
      },
    });
  },

  update(
    tenantId: number | string,
    id: string,
    data: any,
  ): Promise<ApiResponse> {
    return http.put(`/platform/tenant/${tenantId}/roles/${id}`, data, {
      meta: {
        showLoader: true,
      },
    });
  },

  delete(
    tenantId: number | string,
    id: string,
    options?: any,
  ): Promise<ApiResponse> {
    return http.delete(`/platform/tenant/${tenantId}/roles/${id}`, {
      meta: {
        showLoader: true,
      },
      ...options,
    });
  },
};
