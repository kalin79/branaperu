import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.scss", "resources/js/app.js"],
            refresh: true,
            // ← Esto es clave para que Vite procese las fuentes
            assets: ["resources/fonts/**"],
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],

    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },

    build: {
        manifest: true,
        // Aumentamos el límite para evitar el warning
        chunkSizeWarningLimit: 1200,

        // Configuración correcta para Rolldown
        rollupOptions: {
            output: {
                // En Rolldown se usa esta sintaxis
                manualChunks: (id) => {
                    if (id.includes("node_modules")) {
                        if (id.includes("vee-validate") || id.includes("zod")) {
                            return "veevalidate";
                        }
                        if (id.includes("mercadopago")) {
                            return "mercadopago";
                        }
                        return "vendor";
                    }
                },
            },
        },
    },

    // Configuración para SCSS
    css: {
        preprocessorOptions: {
            scss: {
                api: "modern-compiler", // Recomendado para Sass moderno
            },
        },
    },
});
