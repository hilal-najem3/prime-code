import { useAuthStore } from "@/core/store/authStore";
import { permissionService } from "./permissionService";

export function usePermissions() {
  const auth = useAuthStore();

  const can = (permission: string): boolean => {
    return permissionService.can(auth.permissions, permission);
  };

  const canAny = (perms: string[]): boolean => {
    return permissionService.canAny(auth.permissions, perms);
  };

  const canAll = (perms: string[]): boolean => {
    return permissionService.canAll(auth.permissions, perms);
  };

  return {
    can,
    canAny,
    canAll,
  };
}
