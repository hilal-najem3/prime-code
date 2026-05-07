// path : frontend/packages/core/api/services/tenants/usersService.ts
import http from "../../http";
import type { ApiResponse } from "../../types/ApiResponse";

export interface TenantUsersQuery {
  page?: number;
  per_page?: number;
  search?: string;
  sort?: string;
  direction?: "asc" | "desc";
}

export interface TenantUserRole {
  id: number;
  name: string;
}

export interface TenantUser {
  id: number;
  name: string;
  email: string;
  enabled: boolean;
  roles?: TenantUserRole[];
}

export const tenantUsersService = {
  getAll(
    tenantId: number | string,
    params: TenantUsersQuery = {},
  ): Promise<ApiResponse<TenantUser[]>> {
    return http.get(`/platform/tenant/${tenantId}/users`, {
      params,
    }) as unknown as Promise<ApiResponse<TenantUser[]>>;
  },

  async getById(
    tenantId: number | string,
    id: number | string,
  ): Promise<TenantUser> {
    const res = (await http.get(
      `/platform/tenant/${tenantId}/users/${id}`,
    )) as unknown as ApiResponse<TenantUser>;

    return res.data;
  },

  async create(
    tenantId: number | string,
    payload: {
      name: string;
      email: string;
      password: string;
      enabled?: boolean;
      roles?: number[];
    },
  ): Promise<TenantUser> {
    const res = (await http.post(
      `/platform/tenant/${tenantId}/users`,
      payload,
    )) as unknown as ApiResponse<TenantUser>;

    return res.data;
  },

  async update(
    tenantId: number | string,
    id: number | string,
    payload: {
      name: string;
      email: string;
      password?: string;
      enabled?: boolean;
      roles?: number[];
    },
  ): Promise<TenantUser> {
    const res = (await http.put(
      `/platform/tenant/${tenantId}/users/${id}`,
      payload,
    )) as unknown as ApiResponse<TenantUser>;

    return res.data;
  },

  async delete(
    tenantId: number | string,
    id: number | string,
  ): Promise<null> {
    const res = (await http.delete(
      `/platform/tenant/${tenantId}/users/${id}`,
    )) as unknown as ApiResponse<null>;

    return res.data;
  },
};
