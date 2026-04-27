<template>
    <div class="min-h-screen bg-white flex flex-col">
        <!-- HEADER PÚBLICO -->
        <header class="border-b bg-white sticky top-0 z-50">
            <div
                class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between"
            >
                <!-- Logo -->
                <Link :href="route('home')" class="flex items-center gap-2">
                    <span
                        class="text-3xl font-bold tracking-tighter text-orange-600"
                        >Brana</span
                    >
                </Link>

                <!-- Menú de navegación -->
                <nav
                    class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-700"
                >
                    <Link
                        :href="route('home')"
                        class="hover:text-orange-600 transition-colors"
                        >Inicio</Link
                    >
                    <Link
                        :href="route('shop')"
                        class="hover:text-orange-600 transition-colors"
                        >Tienda</Link
                    >
                    <Link
                        :href="route('locals')"
                        class="hover:text-orange-600 transition-colors"
                        >Locales</Link
                    >
                </nav>

                <!-- Acciones derechas -->
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('login')"
                        class="font-medium text-gray-700 hover:text-gray-900 transition-colors px-5 py-2"
                    >
                        Iniciar sesión
                    </Link>

                    <Link
                        :href="route('register')"
                        class="btn-primary px-6 py-3 text-sm font-semibold"
                    >
                        Registrarse
                    </Link>
                </div>
            </div>
        </header>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- FOOTER BÁSICO -->
        <footer class="bg-gray-900 text-white py-12 mt-auto">
            <div
                class="max-w-7xl mx-auto px-6 text-center text-gray-400 text-sm"
            >
                © {{ new Date().getFullYear() }} Brana - Todos los derechos
                reservados
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
</script>
