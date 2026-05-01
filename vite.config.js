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
