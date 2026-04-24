import http from "../http";

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

export interface User {
  id: number;
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
    const res = await http.get(`${basePrefix}/users`, { params });

    return {
      data: res.data,
      meta: res.data.meta,
    };
  },

  /*
  |--------------------------------------------------------------------------
  | Get Single User
  |--------------------------------------------------------------------------
  */
  async getById(id: number) {
    const res = await http.get(`${basePrefix}/users/${id}`);

    return res.data.data;
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
    const res = await http.post(`${basePrefix}/users`, payload);

    return res.data.data;
  },

  /*
  |--------------------------------------------------------------------------
  | Update User
  |--------------------------------------------------------------------------
  */
  async update(
    id: number,
    payload: {
      name: string;
      email: string;
      password?: string;
      enabled?: boolean;
      roles?: number[];
    },
  ) {
    const res = await http.put(`${basePrefix}/users/${id}`, payload);

    return res.data.data;
  },

  /*
  |--------------------------------------------------------------------------
  | Delete User
  |--------------------------------------------------------------------------
  */
  async delete(id: number) {
    const res = await http.delete(`${basePrefix}/users/${id}`);

    return res.data.data;
  },
};
