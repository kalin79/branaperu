<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    title: {
        type: String,
        default: "Finalizar compra",
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header limpio -->
        <div class="bg-white border-b">
            <div
                class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between"
            >
                <Link :href="route('home')" class="flex items-center gap-3">
                    <img src="/logo-brana.png" alt="Brana" class="h-9" />
                    <!-- o usa el logo que ya tienes -->
                </Link>

                <div
                    class="flex items-center gap-8 text-sm font-medium text-gray-500"
                >
                    <div class="flex items-center gap-2">
                        <div
                            class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold"
                        >
                            1
                        </div>
                        <span>Carrito</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold"
                        >
                            2
                        </div>
                        <span>Datos</span>
                    </div>
                    <div
                        class="flex items-center gap-2 text-emerald-600 font-semibold"
                    >
                        <div
                            class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold"
                        >
                            3
                        </div>
                        <span>Pago</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-emerald-600">
                    <span class="text-xs font-medium">🔒 Pago seguro</span>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <slot />

        <!-- Footer pequeño -->
        <div class="bg-white border-t py-6 text-center text-xs text-gray-400">
            © 2025 Brana - Todos los derechos reservados
        </div>
    </div>
</template>
