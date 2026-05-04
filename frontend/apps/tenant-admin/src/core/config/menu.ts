import {
  LayoutDashboard,
  Building2,
  CreditCard,
  Repeat,
  Puzzle,
  Users,
  Shield,
  HeartPulse,
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
    label: "Patients",
    icon: HeartPulse,
    route: "/patients",
    permission: "patients.index",
  },

  {
    label: "Roles",
    icon: Shield,
    route: "/roles",
    permission: "roles.index",
  },
];
