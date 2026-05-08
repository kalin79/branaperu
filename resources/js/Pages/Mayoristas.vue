<template>
    <AppLayout
        :cart="cart"
        :total="total"
        :title_meta="title_meta"
        :description_meta="description_meta"
    >
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
        imagemobile: "/images/bventaM.png",
        link: "/productos",
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
