<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    order_number: String,
    order: Object,
    error: String,
});
</script>

<template>
    <AppLayout title="Pago Rechazado">
        <div class="cart-page">
            <div class="container-fluid">
                <div class="headerMainPago">
                    <span>❌</span>
                    <h1>El pago <span>no se pudo procesar</span></h1>
                </div>
                <div class="bodyMainPago">
                    <p>{{ error }}</p>

                    <div class="dataPayBox">
                        <h3>Pedido #{{ order_number }}</h3>
                        <h2>S/ {{ Number(order.final_total).toFixed(2) }}</h2>
                    </div>

                    <div class="btnContainer">
                        <Link
                            :href="route('checkout.payment', order_number)"
                            class="btn"
                        >
                            Intentar pagar nuevamente
                        </Link>

                        <Link :href="route('cart.index')" class="btn">
                            Volver al carrito
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<style lang="scss">
.cart-page {
    padding: 8rem 0 5rem;
    .btnContainer {
        margin-top: 1rem;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 1rem;
        a {
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            border: 1px solid #0a5d31;
            padding: 0.65rem 2.5rem;
            background: #0a5d31;
            border-radius: 30px;
        }
    }
    .dataPayBox {
        h2 {
            font-family: Poppins, sans-serif;
            font-size: 1rem;
            line-height: 1.25em;
            font-weight: 700;
            color: #727272;
            letter-spacing: -0.025em;
            text-align: left;
            @media screen and (min-width: 992px) {
                font-size: 1.25rem;
                line-height: 1.5em;
            }
            @media screen and (min-width: 1400px) {
                font-size: 1.5rem;
                line-height: 1.5em;
            }
        }

        h3 {
            font-family: Poppins, sans-serif;
            font-size: 1rem;
            line-height: 1.5em;
            font-weight: 500;
            color: #727272;
            letter-spacing: -0.025em;
            text-align: left;
            @media screen and (min-width: 992px) {
                font-size: 1.15rem;
                line-height: 1.5em;
            }
            @media screen and (min-width: 1400px) {
                font-size: 1.25rem;
                line-height: 1.5em;
            }
        }
    }
    .headerMainPago {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 1rem;
    }
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
    h1 {
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
