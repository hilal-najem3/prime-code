import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    host: "admin.dev.local", // 👈 IMPORTANT
    port: 5173,
    proxy: {
      "/api": {
        target: "http://admin.dev.local", // 👈 SAME DOMAIN
        changeOrigin: true,
      },
    },
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
      "@config": path.resolve(__dirname, "../../packages/config"),
      "@core": path.resolve(__dirname, "../../packages/core"),
      "@ui": path.resolve(__dirname, "../../packages/ui"),
      "@i18n": path.resolve(__dirname, "../../packages/i18n"),
    },
  },
});
