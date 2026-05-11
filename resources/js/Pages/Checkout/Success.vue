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
        <div class="cart-page">
            <div class="container-fluid">
                <div class="headerMainPago">
                    <span>🎉</span>
                    <h1>¡Gracias por <span>tu compra!</span></h1>
                </div>
                <div class="bodyMainPago">
                    <div class="dataPayBox">
                        <h3>Pedido #{{ order.order_number }}</h3>
                        <h2 class="text-emerald-700 text-xl font-semibold">
                            Total pagado: S/
                            {{ Number(order.final_total).toFixed(2) }}
                        </h2>
                    </div>

                    <div class="resumenBox">
                        <h3>Resumen de tu pedido:</h3>
                        <div class="listadoBox">
                            <div
                                v-for="item in items"
                                :key="item.name"
                                class="itemProductBox"
                            >
                                <span
                                    >{{ item.quantity }} × {{ item.name }}</span
                                >
                                <span>
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

                    <div class="btnContainer">
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

                    <p>
                        Te hemos enviado un correo con los detalles de tu
                        pedido.
                    </p>
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
    .resumenBox {
        margin-top: 2rem;
        h3 {
            font-family: Poppins, sans-serif;
            font-size: 1rem;
            line-height: 1.25em;
            font-weight: 700;
            color: #5a4523;
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
        .listadoBox {
            .itemProductBox {
                span {
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
            }
        }
    }
    .dataPayBox {
        margin-top: 2rem;
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
