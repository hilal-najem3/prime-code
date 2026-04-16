import http from "../../http";

export const tenantSubscriptionService = {
  assign(
    tenantId: number | string,
    data: {
      plan_id: number | string;
    },
  ) {
    return http.post(`/tenants/${tenantId}/subscription`, data, {
      meta: {
        showLoader: true,
      },
    });
  },
};
