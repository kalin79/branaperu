<template>
    <div>
        <div class="imageFullContainer" v-if="category?.image?.trim()">
            <img :src="`/storage/${category.image}`" :alt="category.name" />
        </div>
        <div class="container-itemsCategoria">
            <div class="layoutContainer">
                <div class="galleryContainer">
                    <div :class="['headerTitularContainer', category.color]">
                        <div class="titularContainer">
                            <div class="iconContainer">
                                <img
                                    :src="`/storage/${category.icon}`"
                                    :alt="category.name"
                                />
                            </div>
                            <h2 v-html="category.name"></h2>
                        </div>
                        <Link href="/productos" class="botonContainer">
                            <img :src="iconArrow" alt="" />
                        </Link>
                    </div>
                    <div class="bodyContainerProducts">
                        <Splide :options="options" class="productContainer">
                            <SplideSlide
                                v-for="product in category.paginated_products
                                    .data"
                                :key="product.id"
                            >
                                <Link :href="`/producto/${product.slug}`">
                                    <div
                                        :class="['cardProduct', category.color]"
                                    >
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
                                                    {{
                                                        cleanHtml(category.name)
                                                    }}
                                                </h3>
                                                <p v-html="product.name"></p>
                                            </div>
                                            <div>
                                                <p>
                                                    {{
                                                        product.formatted_price
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </Link>
                            </SplideSlide>
                        </Splide>
                    </div>
                    <div class="footerContainer">
                        <p v-html="category.description"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import "@splidejs/vue-splide/css"; // Estilos por defecto

import { Link } from "@inertiajs/vue3";
const iconArrow = "/images/arrow.svg";
const imgBolsa = "/images/bolsa2.svg";
defineProps({
    category: {
        type: Object,
        required: true,
        default: () => [],
    },
});
// Función para limpiar HTML
const cleanHtml = (html) => {
    if (!html) return "";

    // Crear un elemento temporal para quitar etiquetas HTML de forma segura
    const div = document.createElement("div");
    div.innerHTML = html;
    return div.textContent || div.innerText || "";
};
// Opciones de Splide (fácil de entender)
const options = {
    type: "loop",
    perPage: 3,
    focus: "center",
    gap: "0rem",
    pagination: true,
    arrows: false,
    breakpoints: {
        768: {
            perPage: 1,
            focus: 0,
            gap: "1rem",
        },
        992: {
            perPage: 2,
            focus: 0,
            gap: "2rem",
        },
        1600: {
            perPage: 2,
        },
    },
};
</script>
