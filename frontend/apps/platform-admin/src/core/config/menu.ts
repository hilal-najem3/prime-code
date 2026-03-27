export interface MenuItem {
  label: string;
  icon?: string;
  route: string;
  permission?: string;
}

export const menu: MenuItem[] = [
  {
    label: "Dashboard",
    route: "/dashboard",
  },
  {
    label: "Tenants",
    route: "/tenants",
    permission: "tenants.view",
  },
  {
    label: "Plans",
    route: "/plans",
    permission: "plans.view",
  },
  {
    label: "Subscriptions",
    route: "/subscriptions",
    permission: "subscriptions.view",
  },
  {
    label: "Modules",
    route: "/modules",
    permission: "modules.view",
  },
  {
    label: "Users",
    route: "/users",
    permission: "users.view",
  },
];
