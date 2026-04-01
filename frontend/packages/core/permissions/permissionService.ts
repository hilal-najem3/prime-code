/**
|--------------------------------------------------------------------------
| Permission Service
|--------------------------------------------------------------------------
| Pure functions (no Vue / no store)
*/

const resolvePermission = (permission: string): string => {
  if (!permission) {
    return permission;
  }

  if (permission.endsWith(".view")) {
    return permission.replace(/\.view$/, ".index");
  }

  if (!permission.includes(".")) {
    return `${permission}.index`;
  }

  return permission;
};

export const permissionService = {
  resolve(permission: string): string {
    return resolvePermission(permission);
  },

  can(permissions: string[], permission: string): boolean {
    const resolvedPermission = resolvePermission(permission);

    return permissions.includes(resolvedPermission);
  },

  canAny(permissions: string[], required: string[]): boolean {
    return required.some((permission) =>
      permissions.includes(resolvePermission(permission)),
    );
  },

  canAll(permissions: string[], required: string[]): boolean {
    return required.every((permission) =>
      permissions.includes(resolvePermission(permission)),
    );
  },
};
