import http from "../http";

export const moduleService = {
  getAll(params?: any) {
    return http.get("/modules", {
      params,
      meta: {
        showLoader: true,
      },
    });
  },

  create(data: any) {
    return http.post("/modules", data, {
      meta: {
        showLoader: true,
      },
    });
  },

  update(id: number, data: any) {
    return http.put(`/modules/${id}`, data, {
      meta: {
        showLoader: true,
      },
    });
  },

  delete(id: number) {
    return http.delete(`/modules/${id}`, {
      meta: {
        showLoader: true,
      },
    });
  },
};
