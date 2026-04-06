import http from "../http";

export const planService = {
  getAll() {
    return http.get("/plans", {
      meta: {
        showLoader: true,
      },
    });
  },

  create(data: any) {
    return http.post("/plans", data, {
      meta: {
        showLoader: true,
      },
    });
  },

  update(id: number, data: any) {
    return http.put(`/plans/${id}`, data, {
      meta: {
        showLoader: true,
      },
    });
  },

  delete(id: number) {
    return http.delete(`/plans/${id}`, {
      meta: {
        showLoader: true,
      },
    });
  },
};
