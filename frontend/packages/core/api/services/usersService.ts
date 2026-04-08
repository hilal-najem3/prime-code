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
    const res = await http.get("/platform/users", { params });

    return {
      data: res.data.data,
      meta: res.data.meta,
    };
  },

  /*
  |--------------------------------------------------------------------------
  | Get Single User
  |--------------------------------------------------------------------------
  */
  async getById(id: number) {
    const res = await http.get(`/platform/users/${id}`);

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
    const res = await http.post("/platform/users", payload);

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
    const res = await http.put(`/platform/users/${id}`, payload);

    return res.data.data;
  },

  /*
  |--------------------------------------------------------------------------
  | Delete User
  |--------------------------------------------------------------------------
  */
  async delete(id: number) {
    const res = await http.delete(`/platform/users/${id}`);

    return res.data.data;
  },
};
