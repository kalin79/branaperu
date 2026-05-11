<template>
    <div class="headerMainContainer">
        <div class="container-fluid">
            <div class="lagoutContainer">
                <button class="btnMobilContainer">
                    <img :src="iconMenu" alt="Menu Brana" />
                </button>

                <Link href="/" class="logoMainContainer">
                    <img :src="logo" alt="Brana" />
                </Link>

                <nav class="menuContainer">
                    <Link href="/acerca-de-brana">Acerca de Brana</Link>
                    <Link href="/productos">Catálogo de Productos</Link>
                    <Link href="/ventas-mayorista">Ventas Mayorista</Link>
                    <Link href="/blog">Blog</Link>
                    <Link href="/contacto">Contáctenos</Link>
                </nav>

                <div class="shopContainer">
                    <div class="shoppingBag">
                        <button @click="openDrawer" class="bagContainer">
                            <img :src="bolsa" alt="bolsa de compra" />
                            <div class="cart-count">
                                {{ cartCount }}
                            </div>
                        </button>
                    </div>
                    <div class="separate">
                        <div class="barra"></div>
                    </div>
                    <div class="userContainer">
                        <a href="/login">
                            <img :src="user" alt="login" />
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drawer del carrito -->
        <CartDrawer v-model="isOpen" :cart="cartData" :total="totalData" />
    </div>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
import { computed, watch } from "vue";
import CartDrawer from "@/Components/Cart/Drawer.vue";
import { useCartDrawer } from "@/Composables/useCartDrawer";

const { isOpen, cart, total, openDrawer, setCartData } = useCartDrawer();

const logo = "/images/logo.svg";
const bolsa = "/images/bolsa.svg";
const user = "/images/user.svg";
const iconMenu = "/images/menu.svg";

const props = defineProps({
    cart: { type: Object, default: () => ({}) },
    total: { type: Number, default: 0 },
});

const cartCount = computed(() => {
    return Object.values(props.cart).reduce((sum, item) => {
        return sum + (parseInt(item.quantity) || 0);
    }, 0);
});
// Sincronizar datos del props con el composable
watch(
    () => props.cart,
    (newCart) => {
        setCartData(newCart, props.total);
    },
    { deep: true, immediate: true },
);

const cartData = computed(() => cart.value);
const totalData = computed(() => total.value);
</script>
