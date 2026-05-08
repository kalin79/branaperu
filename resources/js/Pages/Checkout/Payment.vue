<script setup>
import CheckoutLayout from "@/Layouts/CheckoutLayout.vue";
import { onMounted, ref, computed } from "vue";

const props = defineProps({
    CheckoutLayout: null,
    preference: Object,
    order_number: String,
    order: Object,
});

const loading = ref(true);

// Cargar SDK de Mercado Pago
onMounted(() => {
    const script = document.createElement("script");
    script.src = "https://sdk.mercadopago.com/js/v2";
    script.async = true;
    script.onload = () => {
        loading.value = false;
        initMercadoPago();
    };
    document.body.appendChild(script);
});

const initMercadoPago = () => {
    if (!props.preference?.id) return; // ✅ verifica el id, no el init_point

    // Crear botón de pago
    const mp = new MercadoPago(import.meta.env.VITE_MP_PUBLIC_KEY, {
        locale: "es-PE",
    });

    mp.checkout({
        preference: {
            id: props.preference.id,
        },
        render: {
            container: "#mp-checkout-btn",
            label: "Pagar con Mercado Pago",
        },
    });
};

const fmt = (val) => Number(val || 0).toFixed(2);

// Guard por si order llega vacío
const orderTotal = computed(() => fmt(props.order?.final_total));
const orderSubtotal = computed(() => fmt(props.order?.subtotal));
const orderDelivery = computed(() => fmt(props.order?.delivery_cost));
</script>

<template>
    <CheckoutLayout title="Finalizar Compra">
        <div class="max-w-3xl mx-auto px-4 py-12">
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <!-- Header -->
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-bold text-gray-900">
                        Pedido #{{ order_number }}
                    </h1>
                    <p class="text-2xl font-semibold text-emerald-600 mt-3">
                        S/ {{ fmt(order.final_total) }}
                    </p>
                </div>

                <!-- Resumen -->
                <div class="bg-gray-50 rounded-2xl p-6 mb-10">
                    <h2 class="font-semibold mb-4">Resumen de tu pedido</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>S/ {{ fmt(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Envío</span>
                            <span>S/ {{ fmt(order.delivery_cost) }}</span>
                        </div>
                        <div
                            class="border-t pt-3 flex justify-between font-semibold text-base"
                        >
                            <span>Total</span>
                            <span>S/ {{ fmt(order.final_total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botón Mercado Pago -->
                <div id="mp-checkout-btn" class="mp-button-container"></div>

                <p v-if="loading" class="text-center text-gray-500 mt-6">
                    Cargando pasarela segura...
                </p>

                <div class="text-center mt-8 text-xs text-gray-400">
                    Pago 100% seguro • Procesado por Mercado Pago
                </div>
            </div>
        </div>
    </CheckoutLayout>
</template>

<style scoped>
.mp-button-container {
    min-height: 50px;
}
</style>
