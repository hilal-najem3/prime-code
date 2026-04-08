import http from "../http";
import type { ApiResponse } from "../types/ApiResponse";

export const permissionsService = {
  /**
   * Get all permissions   * @param params Optional query parameters for filtering, pagination, etc.
   * @returns A promise that resolves to the API response containing the list of permissions.
   */
  getAll(params?: any): Promise<ApiResponse> {
    return http.get("/platform/permissions", {
      params,
      meta: {
        showLoader: true,
      },
    });
  },
};
