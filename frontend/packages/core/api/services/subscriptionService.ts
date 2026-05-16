import http from "../http";

export const subscriptionService = {
  getAll() {
    return http.get("/subscriptions", {
      meta: {
        showLoader: true,
      },
    });
  },

  assign(tenantId: string, planId: string) {
    return http.post(
      `/tenants/${tenantId}/subscription`,
      {
        plan_id: planId,
      },
      {
        meta: {
          showLoader: true,
        },
      },
    );
  },

  cancel(id: string) {
    return http.delete(`/subscriptions/${id}`, {
      meta: {
        showLoader: true,
      },
    });
  },
};
