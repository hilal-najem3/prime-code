import { defineStore } from "pinia";

export const useUIStore = defineStore("ui", {
  state: () => ({
    sidebarOpen: false, // 👈 start CLOSED (mobile-first)
  }),

  actions: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },
    openSidebar() {
      this.sidebarOpen = true;
    },
    closeSidebar() {
      this.sidebarOpen = false;
    },
  },
});
