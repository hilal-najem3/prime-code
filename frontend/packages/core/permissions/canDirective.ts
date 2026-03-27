import type { Directive } from "vue";
import { usePermissions } from "./usePermissions";

/**
|--------------------------------------------------------------------------
| v-can Directive
|--------------------------------------------------------------------------
| Usage:
| <button v-can="'permission.name'"></button>
|
| If user doesn't have permission → element removed
*/
export const canDirective: Directive = {
  mounted(el, binding) {
    const { can } = usePermissions();

    const permission = binding.value;

    if (!permission) return;

    if (!can(permission)) {
      el.remove();
    }
  },
};
