import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

// GSAP
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);

// Ziggy
import * as ZiggyModule from "./ziggy";
const Ziggy = ZiggyModule.Ziggy || {};

import "../css/app.scss";
import.meta.glob(["../fonts/**"]);

createInertiaApp({
    // Título por defecto
    title: (title) => (title ? `${title}` : "Brana"),

    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);

        // ==================== ROUTE (Ziggy) ====================
        const route = (name, params = {}, absolute = false) => {
            if (!name) return "/";
            if (typeof name === "string" && name.startsWith("/")) return name;

            try {
                if (Ziggy && Ziggy.route) {
                    return Ziggy.route(name, params, absolute);
                }
            } catch (e) {
                console.warn(`[Ziggy] Ruta no encontrada: ${name}`);
            }

            const fallbacks = {
                home: "/",
                "acerca-de-brana": "/acerca-de-brana",
            };
            return fallbacks[name] || `/${name}`;
        };

        window.route = route;
        app.config.globalProperties.route = route;
        app.config.globalProperties.$route = route;

        app.mixin({ methods: { route } });

        // GSAP Global
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
