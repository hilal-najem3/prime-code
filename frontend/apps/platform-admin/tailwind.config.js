import { theme } from "../../packages/config/theme.js";

export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts}",
    "../../packages/ui/**/*.{vue,js,ts}",
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
