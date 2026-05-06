<script setup>
import { Link } from "@inertiajs/vue3";
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import "@splidejs/vue-splide/css"; // Estilos por defecto
import GaleriaComponent from "@/Components/Carrusel/Galeria.vue";
import SeccionesComponent from "@/Components/Producto/Secciones.vue";
import { inject } from "vue";
const $removeBreaks = inject("$removeBreaks");
const $stripHtml = inject("$stripHtml");
const iconBolsa = "/images/bolsa.svg";
// Opciones de Splide (fácil de entender)
const options = {
    type: "loop",
    perPage: 1,
    gap: "1rem",
    pagination: true,
    arrows: false,
};
defineProps({
    product: Object,
});
</script>
<template>
    <div>
        <!-- <pre> {{ JSON.stringify(product, null, 2) }} </pre> -->
        <div class="headerContainer">
            <div class="layoutContainer">
                <div class="panelGallery">
                    <GaleriaComponent :galleries="product.media" />
                </div>
                <div class="panelData">
                    <div class="categoryContainer">
                        <h2 v-html="$removeBreaks(product.category.name)"></h2>
                    </div>
                    <h1 v-html="product.name"></h1>
                    <h3 v-html="product.subtitle"></h3>
                    <div class="detalleProducto">
                        <div class="headerContainder">
                            <div v-html="product.ml"></div>
                            <div v-html="product.formatted_price"></div>
                        </div>
                        <div
                            class="bodyContainer"
                            v-html="product.description"
                        ></div>
                        <div class="contadorContainer">
                            <button><span>-</span></button>
                            <div class="cantidadBox">0</div>
                            <button><span>+</span></button>
                        </div>
                        <div class="btnCarContainer">
                            <button class="btn">
                                <img :src="iconBolsa" alt="" />
                                <span>AÑADIR AL CARRITO</span>
                            </button>
                        </div>
                        <div
                            class="relationProductsBox"
                            v-if="
                                product?.related_products_frontend &&
                                product.related_products_frontend.length > 0
                            "
                        >
                            <div class="headerTitular">
                                <h4>Va bien con:</h4>
                            </div>

                            <Splide
                                :options="options"
                                class="splideRelationProductoCarrusel"
                            >
                                <SplideSlide
                                    v-for="item in product.related_products_frontend"
                                    :key="item.id"
                                >
                                    <div class="relatedProductBox">
                                        <div class="imgProduct">
                                            <img
                                                :src="`/storage/${item.cover_image}`"
                                                :alt="$removeBreaks(item.name)"
                                            />
                                        </div>
                                        <div class="dataProduct">
                                            <div class="alignCenter">
                                                <h3>
                                                    {{
                                                        $stripHtml(
                                                            product?.category
                                                                .name,
                                                        )
                                                    }}
                                                </h3>
                                                <h2>
                                                    {{ $stripHtml(item.name) }}
                                                </h2>
                                                <p>
                                                    {{ item.formatted_price }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="btnAddCarBox">
                                            <button><span>+</span></button>
                                        </div>
                                    </div>
                                </SplideSlide>
                            </Splide>
                        </div>
                        <div class="beneficiosProductosBox">
                            <div
                                class="cardBeneficio"
                                v-for="beneficio in product.features"
                                :key="beneficio.id"
                            >
                                <div class="iconCard">
                                    <img
                                        :src="`/storage/${beneficio.image}`"
                                        :alt="beneficio.name"
                                    />
                                </div>
                                <div class="dataCard">
                                    <h5 v-html="beneficio.name"></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="seccionContainer">
            <SeccionesComponent :secciones="product.sections" />
        </div>
    </div>
</template>
<style lang="scss" scoped>
/* ==================== FLECHAS ==================== */
:deep(.splide__arrow) {
    background: rgba(10, 93, 49, 0.9);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    color: #1f2937;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease; /* Mantengo la transición suave */
}

/* Posición de las flechas */
:deep(.splide__arrow--prev) {
    left: 20px;
}
:deep(.splide__arrow--next) {
    right: 20px;
}

/* Opcional: Si quieres que cambie un poco al hover (solo opacidad) */
:deep(.splide__arrow:hover) {
    background: rgba(10, 93, 49, 1); /* Solo se pone más blanco */
}

/* ==================== PAGINACIÓN ==================== */
:deep(.splide__pagination) {
    bottom: 30px;
    gap: 0.25rem;
}

:deep(.splide__pagination__page) {
    width: 18px;
    height: 18px;
    background: rgba(10, 93, 49, 0.6);
    border: 2px solid white;
    border-radius: 50%;
    transition: all 0.3s ease;

    &:hover {
        background: rgba(10, 93, 49, 0.85);
    }
}

:deep(.splide__pagination__page.is-active) {
    background: #0a5d31;
    width: 34px;
    border-radius: 9999px;
}
</style>
