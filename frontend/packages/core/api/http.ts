import axios from "axios";
import { tokenService } from "../auth/tokenService";
import { getEnv } from "../config/env";
import { useRequestTracker } from "./requestTracker";
import { useToast } from "../../ui";

type ApiResponse<T = any> = {
  success: boolean;
  message: string;
  data: T;
  meta?: any;
  errors?: any;
};

function resolveApiBaseUrl(): string {
  const configuredBaseUrl = getEnv("VITE_API_URL");

  if (configuredBaseUrl) {
    return String(configuredBaseUrl).replace(/\/$/, "");
  }

  if (typeof window !== "undefined") {
    return `${window.location.origin}/api`;
  }

  return "/api";
}

/*
|--------------------------------------------------------------------------
| Axios Instance
|--------------------------------------------------------------------------
*/

const http = axios.create({
  baseURL: resolveApiBaseUrl(),
  headers: {
    Accept: "application/json",
  },
});

const tracker = useRequestTracker();

/*
|--------------------------------------------------------------------------
| Request Interceptor
|--------------------------------------------------------------------------
*/

http.interceptors.request.use((config) => {
  const token = tokenService.get();

  if (token && !config?.url?.includes("/auth/login")) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  // ✅ Track loading (optional)
  if (config.meta?.showLoader) {
    tracker.start();
  }

  return config;
});

/*
|--------------------------------------------------------------------------
| Response Success
|--------------------------------------------------------------------------
*/

http.interceptors.response.use(
  (response) => {
    const api = response.data;

    if (response.config.meta?.showLoader) {
      tracker.end();
    }

    if (!api.success) {
      return Promise.reject(api);
    }

    // ✅ Optional success toast
    if (response.config.meta?.showSuccessToast) {
      const { show } = useToast();
      show(api.message || "Success", "success");
    }

    return api;
  },

  /*
  |--------------------------------------------------------------------------
  | Response Error
  |--------------------------------------------------------------------------
  */

  (error) => {
    const status = error.response?.status;

    const config = error.config || {};

    if (config.meta?.showLoader) {
      tracker.end();
    }

    /*
    |--------------------------------------------------------------------------
    | Unauthorized
    |--------------------------------------------------------------------------
    */

    if (status === 401) {
      tokenService.remove();
      window.location.href = "/login";
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Error
    |--------------------------------------------------------------------------
    */

    const normalizedError = {
      message: error.response?.data?.message || "Something went wrong",
      errors: error.response?.data?.errors || null,
      status,
    };

    /*
    |--------------------------------------------------------------------------
    | Optional Error Toast
    |--------------------------------------------------------------------------
    */

    if (config.meta?.showErrorToast !== false) {
      const { show } = useToast();
      show(normalizedError.message, "error");
    }

    return Promise.reject(normalizedError);
  },
);

export default http;
