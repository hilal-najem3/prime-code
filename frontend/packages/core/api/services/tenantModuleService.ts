import http from "../http";

export const tenantModuleService = {
  get(tenantId: any) {
    return http.get(`platform/tenants/${tenantId}/modules`);
  },

  sync(tenantId: any, modules: number[]) {
    return http.post(`platform/tenants/${tenantId}/modules`, {
      modules,
    });
  },
};
