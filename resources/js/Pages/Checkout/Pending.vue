<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    order_number: String,
    order: Object,
    message: String,
});
</script>

<template>
    <AppLayout title="Pago en Proceso">
        <div class="min-h-screen bg-gray-50 py-12">
            <div class="max-w-2xl mx-auto px-4">
                <div class="bg-white rounded-3xl shadow-xl p-10 text-center">
                    <div
                        class="mx-auto w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mb-6"
                    >
                        <span class="text-5xl">⏳</span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-3">
                        Pago en proceso
                    </h1>
                    <p class="text-gray-600 mb-8">{{ message }}</p>

                    <div class="bg-gray-50 rounded-2xl p-6 mb-8">
                        <p class="font-medium">Pedido #{{ order_number }}</p>
                        <p class="text-xl font-semibold mt-2">
                            S/ {{ Number(order.final_total).toFixed(2) }}
                        </p>
                    </div>

                    <Link
                        :href="route('home')"
                        class="block w-full bg-gray-800 hover:bg-gray-900 text-white font-medium py-4 rounded-2xl transition"
                    >
                        Volver al inicio
                    </Link>

                    <p class="text-xs text-gray-400 mt-8">
                        Te notificaremos por correo cuando tu pago sea
                        confirmado.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
