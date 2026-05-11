<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

import { Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

// === CORREGIDO ===
const props = defineProps({
    layout: null, // ← Agrega esta línea
    cart: { type: Object, default: () => ({}) },
    total: { type: Number, default: 0 }, // ← Añadir
    districts: { type: Object, default: () => ({}) },
    defaultDeliveryCost: { type: [Number, String], default: 10 },
    freeShippingThreshold: { type: [Number, String], default: null },
    title_meta: String,
    description_meta: String,
});

const cartItems = ref(props.cart);

// Función para eliminar
const removeFromCart = (productId) => {
    router.post(
        "/cart/remove",
        { product_id: productId },
        {
            onSuccess: () => {
                window.location.reload();
            },
        },
    );
};

// Función para actualizar cantidad
const updateQuantity = (productId, newQuantity) => {
    if (newQuantity < 1) return;

    router.post(
        "/cart/update",
        {
            product_id: productId,
            quantity: newQuantity,
        },
        {
            onSuccess: () => {
                window.location.reload();
            },
        },
    );
};

const subtotal = computed(() => props.total);
</script>

<template>
    <AppLayout
        :cart="cart"
        :total="total"
        :title_meta="title_meta"
        :description_meta="description_meta"
    >
        <div>
            <div class="cart-page">
                <div class="container-fluid">
                    <div>
                        <h1 class="titleH1">Mi <span>Carrito</span></h1>

                        <!-- Carrito vacío -->
                        <div class="text-center py-16">
                            <p>Tu carrito está vacío</p>
                            <Link href="/productos" class="btn btnVacio"
                                >Ir a la tienda</Link
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<style lang="scss">
.cart-page {
    padding: 8rem 0 5rem;
    .btnVacio {
        color: white;
    }
    p {
        font-family: Poppins, sans-serif;
        font-size: 0.865rem;
        line-height: 1.5em;
        font-weight: 600;
        color: #727272;
        letter-spacing: -0.025em;
        text-align: left;
        margin: 2rem 0;
        @media screen and (min-width: 992px) {
            font-size: 0.865rem;
            line-height: 1.5em;
        }
        @media screen and (min-width: 1400px) {
            font-size: 0.875rem;
            line-height: 1.5em;
        }
        br {
            display: none;
            @media screen and (min-width: 992px) {
                display: block;
            }
        }
    }
    .titleH1 {
        font-family: Poppins, sans-serif;
        font-size: 2.5rem;
        line-height: 0.75em;
        font-weight: 400;
        color: #5a4523;
        letter-spacing: -0.025em;
        text-align: left;
        @media screen and (min-width: 992px) {
            font-size: 3rem;
            line-height: 0.75em;
        }
        @media screen and (min-width: 1400px) {
            font-size: 3.375rem;
            line-height: 0.75em;
        }
        span {
            font-family: "PP Editorial New";
            font-weight: normal;
            color: #0a5d31;
        }
        br {
            display: none;
            @media screen and (min-width: 992px) {
                display: block;
            }
        }
    }
}
</style>
