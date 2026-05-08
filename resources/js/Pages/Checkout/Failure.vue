<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from '@inertiajs/vue3';

defineProps({
    order_number: String,
    order: Object,
    error: String,
});
</script>

<template>
    <AppLayout title="Pago Rechazado">
        <div class="min-h-screen bg-gray-50 py-12">
            <div class="max-w-2xl mx-auto px-4">
                <div class="bg-white rounded-3xl shadow-xl p-10 text-center">
                    <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
                        <span class="text-5xl">❌</span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-3">
                        El pago no se pudo procesar
                    </h1>
                    <p class="text-gray-600 mb-8">{{ error }}</p>

                    <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-left">
                        <p class="font-medium">Pedido #{{ order_number }}</p>
                        <p class="text-2xl font-semibold mt-2">
                            S/ {{ Number(order.final_total).toFixed(2) }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <Link 
                            :href="route('checkout.payment', order_number)"
                            class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-4 rounded-2xl transition">
                            Intentar pagar nuevamente
                        </Link>

                        <Link 
                            :href="route('cart.index')"
                            class="block w-full border border-gray-300 hover:bg-gray-50 font-medium py-4 rounded-2xl transition">
                            Volver al carrito
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>