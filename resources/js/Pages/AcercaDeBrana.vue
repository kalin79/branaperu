<template>
    <AppLayout
        :cart="cart"
        :total="total"
        :title_meta="title_meta"
        :description_meta="description_meta"
    >
        <div class="acercaPageContainer">
            <HeroComponent type="slider" :slides="banners" />
            <AcercaComponent />
            <HeroComponent type="footer" />
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import HeroComponent from "@/Components/Hero/Hero.vue";
import AcercaComponent from "@/Components/Acerca/Acerca.vue";
const banners = [
    {
        titulo: "Alquiler de maquinaria pesada",
        accion: "Ver equipos",
        imagepc: "/images/somos.webp",
        imagemobile: "/images/somosM.png",
    },
];
defineProps({
    title_meta: String,
    description_meta: String,
    cart: { type: Object, default: () => ({}) }, // ← Añadir
    total: { type: Number, default: 0 }, // ← Añadir
    // image_meta: String,
});
// Importante: Desactivamos layout automático de Inertia
defineOptions({
    layout: null, // ← Agrega esta línea
});
</script>
