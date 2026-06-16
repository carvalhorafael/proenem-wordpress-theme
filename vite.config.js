import { resolve } from "node:path";
import { defineConfig } from "vite";

export default defineConfig({
  base: "",
  server: {
    host: "0.0.0.0",
    port: 5173,
    strictPort: true,
    cors: {
      origin: "http://localhost:8898",
    },
  },
  build: {
    manifest: true,
    outDir: "assets/dist",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: resolve(import.meta.dirname, "src/main.js"),
        editor: resolve(import.meta.dirname, "src/editor.js"),
      },
    },
  },
});
