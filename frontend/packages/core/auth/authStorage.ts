const TOKEN_KEY = "access_token";
import { useModuleStore } from "../modules/moduleStore";
import { tenantModuleService } from "../api/services/tenantModuleService";

const moduleStore = useModuleStore();

export const authStorage = {
  setToken(token: string) {
    localStorage.setItem(TOKEN_KEY, token);
  },

  getToken() {
    return localStorage.getItem(TOKEN_KEY);
  },

  clearToken() {
    localStorage.removeItem(TOKEN_KEY);
  },

  async loadModules(): Promise<any> {
    const currentTenantId = localStorage.getItem("current_tenant_id");
    const res = await tenantModuleService.get(currentTenantId);
    moduleStore.setModules(res.data);
  },
};
