<template>
    <div class="product-grid-container">
        <!-- <pre>{{ JSON.stringify(category, null, 2) }}</pre> -->
        <!-- Título y cantidad -->
        <div :class="['grid-header', category.color || category.parent_color]">
            <div class="leftContainer">
                <h2 v-html="category.name"></h2>
            </div>
            <div class="rightContainer">
                <p v-if="products">
                    <span>{{ products.total }}</span> productos encontrados
                </p>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="loading-state">
            <p>Cargando productos...</p>
        </div>

        <!-- Grid de Productos -->
        <div v-else class="products-grid">
            <Link
                :href="`/producto/${product.slug}`"
                v-for="product in products.data"
                :key="product.id"
                :class="[
                    'product-card',
                    category.color || category.parent_color,
                ]"
            >
                <div class="product-image">
                    <img
                        :src="
                            `/storage/${product.cover_image}` ||
                            '/images/no-image.png'
                        "
                        :alt="product.name"
                    />
                </div>
                <div class="btnCar">
                    <img :src="imgBolsa" alt="Slide" />
                </div>
                <div
                    :class="[
                        'product-info',
                        category.color || category.parent_color,
                    ]"
                >
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
            </Link>
        </div>

        <!-- Paginación -->
        <div v-if="products && products.last_page > 1" class="pagination">
            <a
                v-for="(link, index) in products.links"
                :key="index"
                :href="link.url"
                :class="['page-link', { active: link.active }]"
                v-html="link.label"
            >
            </a>
        </div>
    </div>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
import { inject } from "vue";
const $removeBreaks = inject("$removeBreaks");
const $stripHtml = inject("$stripHtml");
const imgBolsa = "/images/bolsa2.svg";
const props = defineProps({
    category: Object,
    products: Object, // Objeto de paginación de Laravel
    loading: Boolean,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat("es-PE", {
        style: "currency",
        currency: "PEN",
    }).format(price);
};
</script>
