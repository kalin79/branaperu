<template>
    <AppLayout :title_meta="title_meta" :description_meta="description_meta">
        <div class="mayoristaPageContainer">
            <HeroComponent type="slider" :slides="banners" />
            <MayoristaComponent />
            <BeneficiosComponent />
            <FuncionaComponent />
            <HeroComponent type="footer" />
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import HeroComponent from "@/Components/Hero/Hero.vue";
import MayoristaComponent from "@/Components/Mayorista/Mayorista.vue";
import BeneficiosComponent from "@/Components/Mayorista/Beneficios.vue";
import FuncionaComponent from "@/Components/Mayorista/Funciona.vue";
const banners = [
    {
        titulo: "Alquiler de maquinaria pesada",
        accion: "Ver equipos",
        link: "/equipos",
        imagepc: "/images/bventa.webp",
        imagemobile: "/images/bventa.webp",
        link: "/productos",
    },
];
defineProps({
    title_meta: String,
    description_meta: String,
    // image_meta: String,
});
// Importante: Desactivamos layout automático de Inertia
defineOptions({
    layout: null, // ← Agrega esta línea
});
</script>
