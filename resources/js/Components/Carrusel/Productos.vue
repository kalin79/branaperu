<template>
    <div class="carruselContainerProductos">
        <!-- <pre>{{ JSON.stringify(products, null, 2) }}</pre> -->
        <Splide :options="options" class="splideProductoCarrusel">
            <SplideSlide v-for="product in products" :key="product.id">
                <Link :href="`/producto/${product.slug}`">
                    <div :class="['cardProduct', category.color]">
                        <div :class="['imgContainerCard']">
                            <img
                                :src="`/storage/${product.cover_image}`"
                                :alt="product.name"
                            />
                        </div>
                        <div class="btnCar">
                            <img :src="imgBolsa" alt="Slide" />
                        </div>
                        <div class="footerCar">
                            <div>
                                <h3>
                                    {{ $stripHtml(category.name) }}
                                </h3>
                                <p v-html="product.name"></p>
                            </div>
                            <div>
                                <p>
                                    {{ product.formatted_price }}
                                </p>
                            </div>
                        </div>
                    </div>
                </Link>
            </SplideSlide>
        </Splide>
    </div>
</template>
<script setup>
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import "@splidejs/vue-splide/css"; // Estilos por defecto
import { Link } from "@inertiajs/vue3";
import { inject } from "vue";
const $stripHtml = inject("$stripHtml");
const imgBolsa = "/images/bolsa2.svg";

defineProps({
    products: {
        type: Array,
        required: true,
        default: () => [],
    },
    category: {
        type: Object,
        required: true,
        default: () => [],
    },
});

// Opciones de Splide (fácil de entender)
const options = {
    type: "loop",
    perPage: 3,
    gap: "1rem",
    pagination: false,
    arrows: true,
    breakpoints: {
        768: {
            perPage: 1,
            focus: 0,
            gap: "1rem",
        },
        992: {
            perPage: 2,
            focus: 0,
            gap: "1rem",
        },
        1600: {
            perPage: 2,
        },
    },
};
</script>
<style lang="scss">
.splideProductoCarrusel {
    .splide__arrow {
        transform: none;
        top: -3rem;
        background: #5a4523;
        &--prev {
            left: auto;
            right: 3.5rem;
            background-image: url("/images/arrow1.svg");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-size: 18px 18px; /* ← Tamaño de la imagen dentro */
            svg {
                display: none;
            }
        }
        &--next {
            background-image: url("/images/arrow2.svg");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-size: 18px 18px; /* ← Tamaño de la imagen dentro */
            svg {
                display: none;
            }
        }
    }
}
</style>
