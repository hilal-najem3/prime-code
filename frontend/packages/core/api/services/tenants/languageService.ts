import http from "../../http";

export const languageService = {
  getAll(tenantId: any) {
    return http.get(`/platform/tenants/${tenantId}/languages`, {
      meta: {
        showLoader: true,
      },
    });
  },

  create(tenantId: any, data: any) {
    return http.post(`/platform/tenants/${tenantId}/languages`, data, {
      meta: {
        showLoader: true,
      },
    });
  },

  update(tenantId: any, languageId: any, data: any) {
    return http.put(
      `/platform/tenants/${tenantId}/languages/${languageId}`,
      data,
      {
        meta: {
          showLoader: true,
        },
      },
    );
  },

  delete(tenantId: any, languageId: any) {
    return http.delete(
      `/platform/tenants/${tenantId}/languages/${languageId}`,
      {
        meta: {
          showLoader: true,
        },
      },
    );
  },

  toggleActive(tenantId: any, languageId: any) {
    return http.patch(
      `/platform/tenants/${tenantId}/languages/${languageId}/toggle`,
      null,
      {
        meta: {
          showLoader: true,
        },
      },
    );
  },

  setDefault(tenantId: any, languageId: number | string) {
    return http.patch(
      `/platform/tenants/${tenantId}/languages/${languageId}/default`,
      null,
      {
        meta: {
          showLoader: true,
        },
      },
    );
  },
};
