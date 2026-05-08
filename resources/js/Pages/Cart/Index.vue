<script setup>
import { Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

// === CORREGIDO ===
const props = defineProps({
    cart: Object,
    total: Number,
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
    <div>
        <div class="cart-page">
            <div class="container mx-auto px-4 py-8">
                <h1 class="text-3xl font-bold mb-8">🛒 Mi Carrito</h1>

                <!-- Carrito vacío -->
                <div
                    v-if="!Object.keys(cartItems).length"
                    class="text-center py-16"
                >
                    <p class="text-2xl text-gray-400 mb-6">
                        Tu carrito está vacío
                    </p>
                    <Link href="/" class="btn btn-primary">Ir a la tienda</Link>
                </div>

                <!-- Carrito con productos -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Lista de productos -->
                    <div class="lg:col-span-2 space-y-6">
                        <div
                            v-for="(item, id) in cartItems"
                            :key="id"
                            class="flex gap-4 bg-white p-4 rounded-xl shadow-sm border"
                        >
                            <img
                                :src="`/storage/${item.cover_image}`"
                                :alt="item.name"
                                class="w-24 h-24 object-cover rounded-lg"
                            />

                            <div class="flex-1">
                                <h3 class="font-semibold text-lg">
                                    {{ item.name }}
                                </h3>
                                <p
                                    v-if="item.subtitle"
                                    class="text-sm text-gray-500"
                                    v-html="item.subtitle"
                                ></p>
                                <p class="text-sm text-gray-600">
                                    {{ item.ml }}
                                </p>

                                <div class="flex items-center gap-3 mt-4">
                                    <button
                                        @click="
                                            updateQuantity(
                                                item.id,
                                                item.quantity - 1,
                                            )
                                        "
                                        class="w-8 h-8 border rounded-lg flex items-center justify-center hover:bg-gray-100"
                                    >
                                        –
                                    </button>
                                    <span class="font-medium w-8 text-center">{{
                                        item.quantity
                                    }}</span>
                                    <button
                                        @click="
                                            updateQuantity(
                                                item.id,
                                                item.quantity + 1,
                                            )
                                        "
                                        class="w-8 h-8 border rounded-lg flex items-center justify-center hover:bg-gray-100"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="font-semibold text-xl">
                                    {{ item.formatted_price }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    x {{ item.quantity }}
                                </p>
                                <p class="font-bold text-lg mt-4">
                                    S/
                                    {{
                                        (item.price * item.quantity).toFixed(2)
                                    }}
                                </p>

                                <button
                                    @click="removeFromCart(item.id)"
                                    class="text-red-500 text-sm mt-6 hover:underline"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de compra -->
                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border h-fit"
                    >
                        <h2 class="font-semibold text-xl mb-6">Resumen</h2>

                        <div class="space-y-4">
                            <div class="flex justify-between text-lg">
                                <span>Subtotal</span>
                                <span class="font-medium"
                                    >S/ {{ subtotal.toFixed(2) }}</span
                                >
                            </div>

                            <div class="pt-4 border-t">
                                <div
                                    class="flex justify-between text-2xl font-bold"
                                >
                                    <span>Total</span>
                                    <span>S/ {{ subtotal.toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="router.visit('/checkout')"
                            class="btn btn-primary w-full mt-8 py-4 text-lg"
                        >
                            Ir a pagar
                        </button>

                        <Link
                            href="/"
                            class="block text-center text-sm text-gray-500 mt-4 hover:underline"
                        >
                            Seguir comprando
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
