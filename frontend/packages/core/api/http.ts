import axios from "axios";
import { tokenService } from "../auth/tokenService";
import { getEnv } from "../config/env";
import { useRequestTracker } from "./requestTracker";
import { useToast } from "../../ui";
import type { ApiResponse, AuthData } from "../auth/authTypes";

function resolveApiBaseUrl(): string {
  const baseUrl = new URL(window.location.origin);
  baseUrl.port = "";
  return `${baseUrl.origin}/api`;

  // const configuredBaseUrl = getEnv("VITE_API_URL");

  // if (configuredBaseUrl) {
  //   return String(configuredBaseUrl).replace(/\/$/, "");
  // }

  // if (typeof import.meta !== "undefined" && import.meta.env?.DEV) {
  //   return "http://localhost:8000/api";
  // }

  // if (typeof window !== "undefined") {
  //   return `${window.location.origin}/api`;
  // }

  // return "/api";
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
let refreshPromise: Promise<string | null> | null = null;

function redirectToLogin() {
  tokenService.remove();

  if (typeof window !== "undefined" && window.location.pathname !== "/login") {
    window.location.href = "/login";
  }
}

async function refreshAccessToken(): Promise<string | null> {
  const refreshToken = tokenService.getRefresh();

  if (!refreshToken) {
    return null;
  }

  if (!refreshPromise) {
    refreshPromise = axios
      .post<ApiResponse<AuthData>>(
        `${resolveApiBaseUrl()}/auth/refresh`,
        { refresh_token: refreshToken },
        {
          headers: {
            Accept: "application/json",
          },
        },
      )
      .then((response) => {
        const data = response.data.data;
        const accessToken = data.token ?? data.access_token;

        if (!accessToken || !data.refresh_token) {
          throw new Error("Refresh response is missing token data.");
        }

        tokenService.set(accessToken, data.refresh_token);

        return accessToken;
      })
      .catch(() => {
        tokenService.remove();
        return null;
      })
      .finally(() => {
        refreshPromise = null;
      });
  }

  return refreshPromise;
}

/*
|--------------------------------------------------------------------------
| Request Interceptor
|--------------------------------------------------------------------------
*/

http.interceptors.request.use((config) => {
  const token = tokenService.get();
  const isLoginRequest = config?.url?.includes("/auth/login");
  const isRefreshRequest = config?.url?.includes("/auth/refresh");
  const skipAuthHeader = config.meta?.skipAuth;

  if (token && !isLoginRequest && !isRefreshRequest && !skipAuthHeader) {
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

    return {
      data: api.data,
      meta: api.meta,
      success: api.success,
      message: api.message,
      errors: api.errors,
    } as any;
  },

  /*
  |--------------------------------------------------------------------------
  | Response Error
  |--------------------------------------------------------------------------
  */

  (error) => {
    const status = error.response?.status;

    const config = (error.config || {}) as any;
    const requestUrl = String(config.url || "");
    const isRefreshRequest = requestUrl.includes("/auth/refresh");
    const isLoginRequest = requestUrl.includes("/auth/login");
    const isLogoutRequest = requestUrl.includes("/auth/logout");

    if (config.meta?.showLoader) {
      tracker.end();
    }

    /*
    |--------------------------------------------------------------------------
    | Unauthorized
    |--------------------------------------------------------------------------
    */

    if (status === 401) {
      if (isLoginRequest) {
        // Let invalid-credentials errors flow back to the login form.
      } else if (isRefreshRequest || isLogoutRequest || config._retry) {
        redirectToLogin();
      } else {
        config._retry = true;

        return refreshAccessToken().then((newToken) => {
          if (!newToken) {
            redirectToLogin();
            return Promise.reject(error);
          }

          config.headers = config.headers || {};
          config.headers.Authorization = `Bearer ${newToken}`;

          return http(config);
        });
      }
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
