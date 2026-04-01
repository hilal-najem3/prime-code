import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    proxy: {
      "/api": {
        target: "http://localhost:8000",
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
