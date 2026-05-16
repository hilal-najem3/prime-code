// path : frontend/packages/core/api/services/usersService.ts
import http from "../http";

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

export interface User {
  id: string;
  name: string;
  email: string;
  enabled: boolean;
  roles?: {
    id: number;
    name: string;
    slug: string;
  }[];
}

export interface UsersQuery {
  page?: number;
  per_page?: number;
  search?: string;
  sort?: string;
  direction?: "asc" | "desc";
}

const isPlatform = window.location.hostname.includes("platform");

const basePrefix = isPlatform ? "/platform" : "";

/*
|--------------------------------------------------------------------------
| Service
|--------------------------------------------------------------------------
*/

export const usersService = {
  /*
  |--------------------------------------------------------------------------
  | Get Users (REMOTE DATATABLE)
  |--------------------------------------------------------------------------
  */
  async getAll(params: UsersQuery = {}) {
    const res = (await http.get(`${basePrefix}/users`, { params })) as any;

    console.log("API Response for getAll users:", res);

    return {
      data: res.data,
      meta: res.meta,
    };
  },

  /*
  |--------------------------------------------------------------------------
  | Get Single User
  |--------------------------------------------------------------------------
  */
  async getById(id: string) {
    const res = (await http.get(`${basePrefix}/users/${id}`)) as any;

    return res.data;
  },

  /*
  |--------------------------------------------------------------------------
  | Create User
  |--------------------------------------------------------------------------
  */
  async create(payload: {
    name: string;
    email: string;
    password: string;
    enabled?: boolean;
    roles?: number[];
  }) {
    const res = (await http.post(`${basePrefix}/users`, payload)) as any;

    return res.data;
  },

  /*
  |--------------------------------------------------------------------------
  | Update User
  |--------------------------------------------------------------------------
  */
  async update(
    id: string,
    payload: {
      name: string;
      email: string;
      password?: string;
      enabled?: boolean;
      roles?: number[];
    },
  ) {
    const res = (await http.put(`${basePrefix}/users/${id}`, payload)) as any;

    return res.data;
  },

  /*
  |--------------------------------------------------------------------------
  | Delete User
  |--------------------------------------------------------------------------
  */
  async delete(id: string) {
    const res = (await http.delete(`${basePrefix}/users/${id}`)) as any;

    return res.data;
  },
};
