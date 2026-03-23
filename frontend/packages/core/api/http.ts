import axios from "axios";

/*
|--------------------------------------------------------------------------
| Axios Instance
|--------------------------------------------------------------------------
*/

const http = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000/api",
  headers: {
    Accept: "application/json",
  },
});

/*
|--------------------------------------------------------------------------
| Request Interceptor
|--------------------------------------------------------------------------
| Attach token automatically
*/

http.interceptors.request.use((config) => {
  const token = localStorage.getItem("access_token");

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

/*
|--------------------------------------------------------------------------
| Response Interceptor
|--------------------------------------------------------------------------
| Normalize API + handle errors
*/

http.interceptors.response.use(
  (response) => {
    // Your backend uses unified ApiResponse
    return response.data;
  },
  (error) => {
    const status = error.response?.status;

    /*
    |--------------------------------------------------------------------------
    | Unauthorized (401)
    |--------------------------------------------------------------------------
    */

    if (status === 401) {
      localStorage.removeItem("access_token");
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
