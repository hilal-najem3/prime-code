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
    label: "Users",
    icon: Users,
    route: "/users",
    permission: "tenant.users.index",
  },

  {
    label: "Roles",
    icon: Shield,
    route: "/roles",
    permission: "tenant.roles.index",
  },
];
