import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

// GSAP
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);

// Importar Ziggy (método que te funcionó en el otro proyecto)
import * as ZiggyModule from "./ziggy";
const Ziggy = ZiggyModule.Ziggy || {};
import "../css/app.scss";
// Importar fuentes para que Vite las procese
import.meta.glob(["../fonts/**"]);
createInertiaApp({
    title: (title) => `${title} - Brana`,

    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        let page = pages[`./Pages/${name}.vue`];

        // Si la página tiene layout definido, lo respetamos
        if (page.default) {
            page.default.layout = page.default.layout || undefined;
        }

        return page;
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);

        // ==================== FUNCIÓN ROUTE (igual que en tu otro proyecto) ====================
        const route = (name, params = {}, absolute = false) => {
            if (!name) return "/";
            if (typeof name === "string" && name.startsWith("/")) return name;

            // Intentar con Ziggy
            try {
                if (Ziggy && Ziggy.route) {
                    return Ziggy.route(name, params, absolute);
                }
            } catch (e) {
                console.warn(`[Ziggy] Ruta no encontrada: ${name}`);
            }

            // Fallback simple
            const fallbacks = {
                home: "/",
                shop: "/tienda",
                locals: "/locales",
                login: "/login",
                register: "/register",
            };

            return fallbacks[name] || `/${name}`;
        };

        // Registrar route globalmente (igual que en tu otro proyecto)
        window.route = route;
        app.config.globalProperties.route = route;
        app.config.globalProperties.$route = route;

        app.mixin({
            methods: {
                route,
            },
        });

        // GSAP global
        app.config.globalProperties.$gsap = gsap;
        app.config.globalProperties.$ScrollTrigger = ScrollTrigger;

        app.mount(el);
        return app;
    },

    progress: {
        color: "#e67e22",
        showSpinner: true,
    },
});
