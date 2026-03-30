import axios from "axios";
import { tokenService } from "../auth/tokenService";
import { getEnv } from "../config/env";

/*
|--------------------------------------------------------------------------
| Axios Instance
|--------------------------------------------------------------------------
*/

const http = axios.create({
  baseURL: getEnv("VITE_API_URL", "http://localhost:8000/api"),
  headers: {
    Accept: "application/json",
  },
});

/*
|--------------------------------------------------------------------------
| Request Interceptor
|--------------------------------------------------------------------------
*/

http.interceptors.request.use((config) => {
  const token = tokenService.get();

  // 🚫 Skip auth header for login
  if (token && !config?.url?.includes("/auth/login")) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

/*
|--------------------------------------------------------------------------
| Response Interceptor
|--------------------------------------------------------------------------
*/

http.interceptors.response.use(
  (response) => {
    const api = response.data;

    if (!api.success) {
      return Promise.reject(api);
    }

    return api; // return full API structure
  },
  (error) => {
    const status = error.response?.status;

    /*
    |--------------------------------------------------------------------------
    | Unauthorized (401)
    |--------------------------------------------------------------------------
    */

    if (status === 401) {
      tokenService.remove();

      // IMPORTANT: do NOT use store here
      window.location.href = "/login";
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Errors (422)
    |--------------------------------------------------------------------------
    */

    if (status === 422) {
      return Promise.reject(error.response.data);
    }

    /*
    |--------------------------------------------------------------------------
    | General Errors
    |--------------------------------------------------------------------------
    */

    return Promise.reject({
      message: error.response?.data?.message || "Something went wrong",
      errors: error.response?.data?.errors || null,
    });
  },
);

export default http;
