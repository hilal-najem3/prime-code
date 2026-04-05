import http from "../http";

export const subscriptionService = {
  assign(tenantId: number, planId: number) {
    return http.post(`/tenants/${tenantId}/subscription`, {
      plan_id: planId,
    });
  },
};
