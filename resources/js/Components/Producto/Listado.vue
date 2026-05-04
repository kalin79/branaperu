<template>
    <section class="listaSectionContainer">
        <!-- <pre>{{ JSON.stringify(selectedCategory, null, 2) }}</pre> -->
        <div class="container-fluid">
            <div class="layoutContainer">
                <div class="siderColumn">
                    <div class="sideBarSticky">
                        <CategoryTree
                            :categories="parentCategories"
                            @select-category="handleSelectCategory"
                            @show-all="handleShowAll"
                        />
                    </div>
                </div>
                <div class="contentColumn">
                    <div v-if="!selectedCategory">
                        <div
                            :class="['carruselSection', category.color]"
                            v-for="category in parentCategories"
                            :key="category.id"
                        >
                            <div class="headerContainer">
                                <h3 v-html="$removeBreaks(category.name)"></h3>
                            </div>
                            <div class="productsCarruselContainer">
                                <ProductosCarrusel
                                    :products="category.carousel_products"
                                    :category="category"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="listadoSection" v-else>
                        <ProductGrid
                            :category="selectedCategory"
                            :products="products"
                            :loading="loading"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
import { ref } from "vue";
// import { onBeforeRouteUpdate } from "vue-router"; // si usas vue-router, o directamente:
import CategoryTree from "@/Components/Producto/CategoryTree.vue";
import ProductosCarrusel from "@/Components/Carrusel/Productos.vue";
import ProductGrid from "@/Components/Producto/ProductGrid.vue"; // ← Tu listado paginado
import { inject } from "vue";
const $removeBreaks = inject("$removeBreaks");
// Después de router.get

defineProps({
    parentCategories: {
        type: Array,
        required: true,
        default: () => [],
    },
    selectedCategory: Object, // null al inicio
    products: Object, // viene cuando se selecciona una categoría
});
// const selectedCategory = ref(null);
const emit = defineEmits(["select-category", "show-all"]);
const loading = ref(false);
// Click en categoría (padre o hijo)
const handleSelectCategory = (category) => {
    loading.value = true;
    emit("select-category", category);
};

// Click en "Ver todos"
const handleShowAll = () => {
    loading.value = true;
    emit("show-all");
};
</script>
