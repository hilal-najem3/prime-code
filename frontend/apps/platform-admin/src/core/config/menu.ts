import {
  LayoutDashboard,
  Building2,
  CreditCard,
  Repeat,
  Puzzle,
  Users,
} from "lucide-vue-next";

export interface MenuItem {
  label: string;
  icon?: any;
  route: string;
  permission?: string;
}

export const menu: MenuItem[] = [
  {
    label: "Dashboard",
    icon: LayoutDashboard,
    route: "/dashboard",
  },
  {
    label: "Tenants",
    icon: Building2,
    route: "/tenants",
    permission: "tenants.view",
  },
  {
    label: "Plans",
    icon: CreditCard,
    route: "/plans",
    permission: "plans.view",
  },
  {
    label: "Subscriptions",
    icon: Repeat,
    route: "/subscriptions",
    permission: "subscriptions.view",
  },
  {
    label: "Modules",
    icon: Puzzle,
    route: "/modules",
    permission: "modules.view",
  },
  {
    label: "Users",
    icon: Users,
    route: "/users",
    permission: "users.view",
  },
];