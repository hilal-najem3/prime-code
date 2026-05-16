import http from "../http";
import type { ApiResponse } from "../types/ApiResponse";

export const rolesService = {
  /**
   * Get all roles
   */
  getAll(params?: any): Promise<ApiResponse> {
    return http.get("/platform/roles", {
      params,
      meta: {
        showLoader: true,
      },
    });
  },

  /**
   * Get single role
   */
  get(id: string): Promise<ApiResponse> {
    return http.get(`/platform/roles/${id}`, {
      meta: {
        showLoader: true,
      },
    });
  },

  /**
   * Create role
   */
  create(data: any): Promise<ApiResponse> {
    return http.post("/platform/roles", data, {
      meta: {
        showLoader: true,
      },
    });
  },

  /**
   * Update role
   */
  update(id: string, data: any): Promise<ApiResponse> {
    return http.put(`/platform/roles/${id}`, data, {
      meta: {
        showLoader: true,
      },
    });
  },

  /**
   * Delete role
   */
  delete(id: number, options?: any): Promise<ApiResponse> {
    return http.delete(`/platform/roles/${id}`, {
      meta: {
        showLoader: true,
      },
      ...options,
    });
  },
};
