<script setup>
import { router } from "@inertiajs/vue3";
import { ref, computed, onMounted, watch } from "vue";
import { inject } from "vue";
const $removeBreaks = inject("$removeBreaks");
const iconDelete = "/images/delete.svg";
const props = defineProps({
    cart: Object,
    total: Number,
});

const isOpen = defineModel({ default: false });

const cartItems = ref(props.cart || {});

const subtotal = computed(() => props.total || 0);

const closeDrawer = () => {
    isOpen.value = false;
};

const removeFromCart = (productId) => {
    router.post(
        "/cart/remove",
        { product_id: productId },
        {
            onSuccess: () => router.reload({ only: ["cart"] }),
        },
    );
};

const updateQuantity = (productId, newQuantity) => {
    if (newQuantity < 1) return;
    router.post(
        "/cart/update",
        { product_id: productId, quantity: newQuantity },
        {
            onSuccess: () => router.reload({ only: ["cart"] }),
        },
    );
};
watch(
    () => props.cart,
    (newCart) => {
        cartItems.value = { ...(newCart || {}) };
    },
    { deep: true },
);
</script>

<template>
    <div class="drawerContainer">
        <div v-if="isOpen" class="cart-overlay" @click="closeDrawer"></div>

        <!-- Drawer -->
        <div :class="['cart-drawer', isOpen ? 'open' : '']">
            <div class="drawer-header">
                <div class="titleLineBox">
                    <h2>
                        Carrito
                        <span>({{ Object.keys(cartItems).length }})</span>
                    </h2>
                </div>
                <button class="close-btn" @click="closeDrawer">✕</button>
            </div>

            <!-- Productos -->
            <!-- <pre>{{ JSON.stringify(cartItems, null, 2) }}</pre> -->
            <div class="drawer-body">
                <div
                    v-for="(item, key) in cartItems"
                    :key="key"
                    class="cart-item"
                >
                    <img
                        :src="`/storage/${item.cover_image}`"
                        :alt="item.name"
                        class="item-image"
                    />

                    <div class="item-info">
                        <h5 v-html="$removeBreaks(item.category_name)"></h5>
                        <h4>{{ item.name }}</h4>
                        <!-- <p class="item-subtitle" v-html="item.subtitle"></p> -->
                        <p>{{ item.ml }}</p>

                        <div class="quantity-control">
                            <div class="controlLayout">
                                <button
                                    @click="
                                        updateQuantity(
                                            item.id,
                                            item.quantity - 1,
                                        )
                                    "
                                >
                                    −
                                </button>
                                <span>{{ item.quantity }}</span>
                                <button
                                    @click="
                                        updateQuantity(
                                            item.id,
                                            item.quantity + 1,
                                        )
                                    "
                                >
                                    +
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="item-price">
                        <p class="price">{{ item.formatted_price }}</p>
                        <p class="subtotal">
                            S/ {{ (item.price * item.quantity).toFixed(2) }}
                        </p>
                        <button
                            @click="removeFromCart(item.id)"
                            class="remove-btn"
                        >
                            <img :src="iconDelete" alt="Eliminar producto" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="drawer-footer">
                <div class="total-row">
                    <span>SubTotal</span>
                    <span class="total-amount"
                        >S/ {{ subtotal.toFixed(2) }}</span
                    >
                </div>
                <div class="btnCartContainer">
                    <button
                        @click="
                            router.visit('/checkout');
                            closeDrawer();
                        "
                        class="btn btnCheckout"
                    >
                        FINALIZAR COMPRA
                    </button>

                    <button @click="closeDrawer" class="btn btnClose">
                        SEGUIR COMPRANDO
                    </button>
                </div>
                <div class="legalContainer">
                    <p>
                        Los envíos, impuestos y códigos de descuento se calculan
                        al finalizar la compra.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
