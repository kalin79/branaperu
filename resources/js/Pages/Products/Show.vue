<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import DetalleComponent from "@/Components/Producto/Detalle.vue";
import HeroComponent from "@/Components/Hero/Hero.vue";
import { inject } from "vue";
const $stripHtml = inject("$stripHtml");
defineProps({
    layout: null, // ← Agrega esta línea
    product: Object,
    title_meta: String,
    description_meta: String,
});
</script>

<template>
    <AppLayout
        :title_meta="$stripHtml(title_meta)"
        :description_meta="description_meta"
    >
        <div class="productoDetallaContainer">
            <DetalleComponent :product="product" />
            <HeroComponent type="footer" />
        </div>
    </AppLayout>
</template>
