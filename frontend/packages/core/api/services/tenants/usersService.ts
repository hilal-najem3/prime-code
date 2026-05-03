// path : frontend/packages/core/api/services/tenants/usersService.ts
import http from "../../http";

export interface TenantUsersQuery {
  page?: number;
  per_page?: number;
  search?: string;
  sort?: string;
  direction?: "asc" | "desc";
}

export const tenantUsersService = {
  async getAll(tenantId: number | string, params: TenantUsersQuery = {}) {
    const res = (await http.get(`/platform/tenant/${tenantId}/users`, {
      params,
    })) as any;

    return {
      data: res.data.data,
      meta: res.data.meta,
    };
  },

  async getById(tenantId: number | string, id: number | string) {
    const res = (await http.get(
      `/platform/tenant/${tenantId}/users/${id}`,
    )) as any;

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
  ) {
    const res = (await http.post(
      `/platform/tenant/${tenantId}/users`,
      payload,
    )) as any;

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
  ) {
    const res = (await http.put(
      `/platform/tenant/${tenantId}/users/${id}`,
      payload,
    )) as any;

    return res.data;
  },

  async delete(tenantId: number | string, id: number | string) {
    const res = (await http.delete(
      `/platform/tenant/${tenantId}/users/${id}`,
    )) as any;

    return res.data;
  },
};
