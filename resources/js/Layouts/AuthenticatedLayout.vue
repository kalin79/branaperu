<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b">
            <div
                class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between"
            >
                <a href="/" class="text-3xl font-bold text-orange-600">Brana</a>
                <div class="flex items-center gap-8">
                    <Link :href="route('dashboard')">Dashboard</Link>
                    <Link :href="route('profile')">Mi Perfil</Link>
                    <Link :href="route('orders')">Mis Pedidos</Link>
                    <button
                        @click="logout"
                        class="text-red-600 hover:text-red-700"
                    >
                        Cerrar sesión
                    </button>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-6 py-10">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

const logout = () => {
    Inertia.post(route("logout"));
};
</script>
