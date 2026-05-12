/** @type {import('tailwindcss').Config} */
export default {
  content: ["./client/index.html", "./client/src/**/*.{ts,tsx}"],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        brand: {
          primary: "#2196F3",
          danger: "#E74C3C",
          success: "#27AE60",
          warning: "#F39C12",
        },
      },
      fontFamily: {
        sans: ['"Noto Sans Thai"', "system-ui", "sans-serif"],
      },
    },
  },
  plugins: [],
};
