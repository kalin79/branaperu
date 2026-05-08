<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    order: Object,
    items: Array,
});
</script>

<template>
    <AppLayout title="¡Compra Exitosa!">
        <div class="min-h-screen bg-gray-50 py-12">
            <div class="max-w-2xl mx-auto px-4">
                <div class="bg-white rounded-3xl shadow-xl p-10 text-center">
                    <!-- Icono de éxito -->
                    <div
                        class="mx-auto w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mb-6"
                    >
                        <span class="text-5xl">🎉</span>
                    </div>

                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        ¡Gracias por tu compra!
                    </h1>
                    <p class="text-lg text-emerald-600 font-medium">
                        Pedido #{{ order.order_number }}
                    </p>

                    <div class="my-10 p-6 bg-emerald-50 rounded-2xl">
                        <p class="text-emerald-700 text-xl font-semibold">
                            Total pagado: S/
                            {{ Number(order.final_total).toFixed(2) }}
                        </p>
                    </div>

                    <div class="text-left mb-8">
                        <h3 class="font-semibold mb-4 text-gray-800">
                            Resumen de tu pedido:
                        </h3>
                        <div class="space-y-3">
                            <div
                                v-for="item in items"
                                :key="item.name"
                                class="flex justify-between text-sm"
                            >
                                <span
                                    >{{ item.quantity }} × {{ item.name }}</span
                                >
                                <span class="font-medium">
                                    S/
                                    {{
                                        Number(
                                            item.price * item.quantity,
                                        ).toFixed(2)
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <Link
                            :href="route('home')"
                            class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-4 rounded-2xl transition"
                        >
                            Volver al inicio
                        </Link>

                        <Link
                            :href="route('orders')"
                            class="block w-full border border-gray-300 hover:bg-gray-50 font-medium py-4 rounded-2xl transition"
                        >
                            Ver mis pedidos
                        </Link>
                    </div>

                    <p class="text-xs text-gray-400 mt-8">
                        Te hemos enviado un correo con los detalles de tu
                        pedido.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
