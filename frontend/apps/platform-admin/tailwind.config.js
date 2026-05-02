// path : frontend/apps/platform-admin/tailwind.config.js
import { theme } from "../../packages/config/theme.js";

export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts}",
    // "../../packages/ui/**/*.{vue,js,ts}",
    "../../packages/ui/components/*.{vue,js,ts}",
    "../../packages/ui/composables/*.{vue,js,ts}",
    "../../packages/ui/styles/*.{vue,js,ts}",
    "../../packages/ui/index.ts",
    "../../packages/ui/package.json",

    // media
    "../../packages/media/components/*.{vue,js,ts}",
    "../../packages/media/composables/*.{vue,js,ts}",
    "../../packages/media/styles/*.{vue,js,ts}",
    "../../packages/media/index.ts",
  ],

  theme: {
    extend: {
      colors: {
        bg: theme.colors.background,
        surface: theme.colors.surface,
        text: theme.colors.text,
        border: theme.colors.border,
        brand: theme.colors.brand,
        state: theme.colors.state,
      },
    },
  },

  plugins: [],
};
