import http from "../http";

export const subscriptionService = {
  getAll() {
    return http.get("/subscriptions");
  },

  assign(tenantId: number, planId: number) {
    return http.post(`/tenants/${tenantId}/subscription`, {
      plan_id: planId,
    });
  },

  cancel(id: number) {
    return http.delete(`/subscriptions/${id}`);
  },
};
