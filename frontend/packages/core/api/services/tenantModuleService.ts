import http from "../http";

export const tenantModuleService = {
  get(tenantId: any) {
    return http.get(`/tenants/${tenantId}/modules`);
  },

  sync(tenantId: any, modules: number[]) {
    return http.post(`/tenants/${tenantId}/modules`, {
      modules,
    });
  },
};
