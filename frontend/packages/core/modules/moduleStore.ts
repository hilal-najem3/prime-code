import { defineStore } from "pinia";

export const useModuleStore = defineStore("modules", {
  state: () => ({
    modules: [] as string[],
  }),

  actions: {
    setModules(modules: any[]) {
      this.modules = modules.map((m) => m.slug);
    },

    has(module: string) {
      return this.modules.includes(module);
    },

    clear() {
      this.modules = [];
    },
  },
});
