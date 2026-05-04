<template>
    <div class="category-tree">
        <div class="tree-header">
            <h3 class="tree-title">Categorías</h3>
            <button @click="handleShowAll" class="all-categories-btn">
                Ver todos
            </button>
        </div>

        <div class="categories-list">
            <div
                v-for="category in categories"
                :key="category.id"
                class="category-item"
            >
                <!-- Categoría Padre -->
                <div
                    :class="['parent-category']"
                    @click="selectCategory(category)"
                >
                    <div class="parent-content">
                        <span class="category-name">{{
                            $stripHtml(category.name)
                        }}</span>
                        <!-- <span class="product-count">
                            1({{ category.products_count || 0 }})
                            <small style="font-size: 10px; color: gray"
                                >({{
                                    category.debug_category_ids?.length || 0
                                }})</small
                            >x
                        </span> -->
                        <span
                            v-if="category.children && category.children.length"
                            class="product-count"
                        >
                            ({{ category.products_count || 0 }})
                        </span>
                    </div>

                    <!-- Botón expandir (solo si tiene subcategorías) -->
                    <button
                        v-if="category.children && category.children.length"
                        @click.stop="toggleExpand(category.id)"
                        class="expand-btn"
                    >
                        {{ expanded[category.id] ? "−" : "+" }}
                    </button>
                </div>

                <!-- Subcategorías -->
                <div
                    v-if="category.children && category.children.length"
                    class="subcategories"
                    :class="[
                        { expanded: expanded[category.id] },
                        category.color,
                    ]"
                >
                    <div
                        v-for="sub in category.children"
                        :key="sub.id"
                        class="subcategory-item"
                        @click="selectCategory(sub)"
                    >
                        <span class="subcategory-name">{{ sub.name }}</span>
                        <span class="sub-product-count">
                            ({{ sub.products_count || 0 }})
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { inject } from "vue";
const $stripHtml = inject("$stripHtml");
const props = defineProps({
    categories: Array, // Categorías padre con sus children
});
const emit = defineEmits(["select-category", "show-all"]); // sirve para enviar informacion al padre

const expanded = ref({}); // Para controlar qué categorías están expandidas
// Toggle expandir/colapsar subcategorías
const toggleExpand = (categoryId) => {
    expanded.value[categoryId] = !expanded.value[categoryId];
};
// Seleccionar categoría
const selectCategory = (category) => {
    console.log(category);
    emit("select-category", category); // envia la categoria al padre
};

// ←←← FUNCIÓN QUE FALTABA
const handleShowAll = () => {
    emit("show-all");
};
</script>
