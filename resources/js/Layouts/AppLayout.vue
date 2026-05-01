<template>
    <div>
        <Header />
        <!-- CONTENIDO PRINCIPAL -->
        <slot />
        <Footer />
    </div>
</template>

<script setup>
import Header from "@/Components/Header.vue";
import Footer from "@/Components/Footer.vue";
</script>
