import {
  LayoutDashboard,
  Building2,
  CreditCard,
  Repeat,
  Puzzle,
  Users,
  Shield,
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
    permission: "tenants.index",
  },
  {
    label: "Plans",
    icon: CreditCard,
    route: "/plans",
    permission: "plans.index",
  },
  {
    label: "Subscriptions",
    icon: Repeat,
    route: "/subscriptions",
    permission: "subscriptions.index",
  },
  {
    label: "Modules",
    icon: Puzzle,
    route: "/modules",
    permission: "modules.index",
  },
  {
    label: "Users",
    icon: Users,
    route: "/users",
    permission: "platform.users.index",
  },

  {
    label: "Roles",
    icon: Shield,
    route: "/roles",
    permission: "platform.roles.index",
  },
];
