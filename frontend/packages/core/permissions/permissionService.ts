/**
|--------------------------------------------------------------------------
| Permission Service
|--------------------------------------------------------------------------
| Pure functions (no Vue / no store)
*/

export const permissionService = {
  can(permissions: string[], permission: string): boolean {
    return permissions.includes(permission);
  },

  canAny(permissions: string[], required: string[]): boolean {
    return required.some((p) => permissions.includes(p));
  },

  canAll(permissions: string[], required: string[]): boolean {
    return required.every((p) => permissions.includes(p));
  },
};
