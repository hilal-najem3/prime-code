import http from "../../http";

export const pageService = {
  getAll(tenantId: number | string) {
    return http.get(`/platform/tenants/${tenantId}/pages`, {
      meta: {
        showLoader: true,
      },
    });
  },

  create(tenantId: number | string, data: any) {
    return http.post(`/platform/tenants/${tenantId}/pages`, data, {
      meta: {
        showLoader: true,
      },
    });
  },

  update(tenantId: number | string, pageId: number | string, data: any) {
    return http.put(`/platform/tenants/${tenantId}/pages/${pageId}`, data, {
      meta: {
        showLoader: true,
      },
    });
  },

  delete(tenantId: number | string, pageId: number | string) {
    return http.delete(`/platform/tenants/${tenantId}/pages/${pageId}`, {
      meta: {
        showLoader: true,
      },
    });
  },
};
