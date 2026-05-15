<template>
    <AppLayout
        :cart="cart"
        :total="total"
        :title_meta="title_meta"
        :description_meta="description_meta"
    >
        <div>
            <HeroComponent type="slider" :slides="banners" />
            <CuidadosComponent :data="cuidados" />
            <CatalogoComponent :categories="categories" />
            <UnicaComponent />
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import HeroComponent from "@/Components/Hero/Hero.vue";
import CuidadosComponent from "@/Components/Home/Cuidados.vue";
import CatalogoComponent from "@/Components/Home/Catalogo.vue";
import UnicaComponent from "@/Components/Home/Unica.vue";

// Importante: Desactivamos layout automático de Inertia
defineOptions({
    layout: null, // ← Agrega esta línea
});
defineProps({
    categories: {
        type: Array,
        required: true,
        default: () => [],
    },
    banners: {
        type: Array,
        default: () => [],
    },
    cuidados: {
        type: Object,
        default: () => null,
    },
    title_meta: String,
    description_meta: String,
    cart: { type: Object, default: () => ({}) }, // ← Añadir
    total: { type: Number, default: 0 }, // ← Añadir
});
</script>
