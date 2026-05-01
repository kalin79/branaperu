<template>
    <AppLayout :title_meta="title_meta" :description_meta="description_meta">
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
const banners = [
    {
        titulo: "Alquiler de maquinaria pesada",
        accion: "Ver equipos",
        link: "/productos",
        imagepc: "/images/1.webp",
        imagemobile: "/images/1.webp",
    },
    {
        titulo: "Soluciones para construcción",
        accion: "Contáctanos",
        link: "/productos",
        imagepc: "/images/2.webp",
        imagemobile: "/images/2.webp",
    },
];

const cuidados = [
    {
        titulo: "Fórmulas limpias, sin promesas vacías",
        icono: "/images/i1.svg",
        descripcion:
            "Cada producto está hecho con ingredientes naturales cuidadosamente seleccionados, sin químicos agresivos ni rellenos innecesarios. Lo que ves es lo que sientes: pureza real.",
        color: "verde",
    },
    {
        titulo: "Cuidamos de ti y del planeta",
        icono: "/images/i2.svg",
        descripcion:
            "Todos nuestros productos son veganos, libres de crueldad animal y presentados en empaques sostenibles. BRANA es belleza que respeta.",
        color: "amarrillo",
    },
    {
        titulo: "Resultados visibles, sensaciones memorables",
        icono: "/images/i3.svg",
        descripcion:
            "Cada fórmula está respaldada por ciencia botánica y diseñada para ofrecer beneficios reales: hidratación profunda, relajación sensorial y armonía con tu piel.",
        color: "violeta",
    },
    {
        titulo: "Te contamos todo, sin secretos",
        icono: "/images/i4.svg",
        descripcion:
            "En BRANA mostramos cada ingrediente con orgullo. Porque confiamos en nuestras fórmulas y tú mereces saber qué estás usando.",
        color: "rosado",
    },
];
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
    title_meta: String,
    description_meta: String,
});
</script>
