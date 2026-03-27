import { computed } from "vue";
import { useAuthStore } from "@/core/store/authStore";
import { permissionService } from "./permissionService";
import type { MenuItem } from "@/core/config/menu";

/**
|--------------------------------------------------------------------------
| useMenu
|--------------------------------------------------------------------------
| Filters menu based on permissions
*/
export function useMenu(menu: MenuItem[]) {
  const auth = useAuthStore();

  const filteredMenu = computed(() => {
    return menu.filter((item) => {
      if (!item.permission) return true;

      return permissionService.can(auth.permissions, item.permission);
    });
  });

  return {
    menu: filteredMenu,
  };
}
