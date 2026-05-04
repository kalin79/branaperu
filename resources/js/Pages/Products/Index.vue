<template>
    <AppLayout :title_meta="title_meta" :description_meta="description_meta">
        <div class="productPageContainer">
            <HeroComponent type="slider" :slides="banners" />
            <ListadoComponent
                :parentCategories="parentCategories"
                :selectedCategory="selectedCategory"
                :products="products"
                @select-category="handleCategorySelect"
                @show-all="handleShowAll"
            />
        </div>
    </AppLayout>
</template>
<script setup>
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import HeroComponent from "@/Components/Hero/Hero.vue";
import ListadoComponent from "@/Components/Producto/Listado.vue";

const banners = [
    {
        titulo: "Alquiler de maquinaria pesada",
        accion: "Ver equipos",
        link: "",
        imagepc: "/images/productosbg.webp",
        imagemobile: "/images/productosbg.webp",
    },
];
// Importante: Desactivamos layout automático de Inertia
defineProps({
    layout: null, // ← Agrega esta línea
    parentCategories: {
        type: Array,
        required: true,
        default: () => [],
    },
    selectedCategory: Object, // null al inicio
    products: Object, // viene cuando se selecciona una categoría
    title_meta: String,
    description_meta: String,
});
const handleCategorySelect = (category) => {
    // Guardamos la posición actual del scroll antes de navegar
    const currentScroll = window.scrollY;
    router.get(
        `/productos/categoria/${category.slug}`,
        {
            preserveState: true,
            preserveScroll: true,
            replace: true, // ← Agrega esto
            only: ["selectedCategory", "products"],
        },
        {
            // Después de que Inertia termine la petición
            onSuccess: () => {
                // Opcional: mantener la posición anterior o ir al top suave
                setTimeout(() => {
                    window.scrollTo({
                        top: currentScroll > 300 ? 300 : currentScroll, // mantiene un poco de scroll
                        behavior: "smooth",
                    });
                }, 100);
            },
            onError: () => {
                console.log("Error al cargar categoría");
            },
        },
    );
};
const handleShowAll = () => {
    // Guardamos la posición actual del scroll antes de navegar
    const currentScroll = window.scrollY;
    router.get(
        "/productos",
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ["parentCategories", "selectedCategory", "products"],
        },
        {
            // Después de que Inertia termine la petición
            onSuccess: () => {
                // Opcional: mantener la posición anterior o ir al top suave
                setTimeout(() => {
                    window.scrollTo({
                        top: currentScroll > 300 ? 300 : currentScroll, // mantiene un poco de scroll
                        behavior: "smooth",
                    });
                }, 100);
            },
            onError: () => {
                console.log("Error al cargar categoría");
            },
        },
    );
};
</script>
