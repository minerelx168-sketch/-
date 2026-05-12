import { defineConfig } from "vitest/config";
import path from "node:path";

export default defineConfig({
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "client/src"),
      "@shared": path.resolve(__dirname, "shared"),
      "@server": path.resolve(__dirname, "server"),
    },
  },
  test: {
    environment: "node",
    include: ["server/**/*.test.ts", "shared/**/*.test.ts"],
  },
});
