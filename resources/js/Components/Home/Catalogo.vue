<template>
    <section class="catalogoHomeContainer">
        <!-- <pre>{{ JSON.stringify(categories, null, 2) }}</pre> -->
        <div class="headerContainerCategoria">
            <div class="container-fluid">
                <div class="layoutContainer">
                    <div class="leftContainer">
                        <h3>CATÁLOGO DE PRODUCTOS</h3>
                        <h2>
                            <span>Experiencias</span>
                        </h2>
                        <h4>que conectan.</h4>
                    </div>
                    <div class="rightContainer">
                        <p>
                            Más que productos, creamos momentos para ti.
                            Transforma tu rutina en un acto de autocuidado y
                            presencia.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="categoriaContainer"
            v-for="(category, index) in categories"
            :key="category.id"
        >
            <DerComponent :category="category" v-if="index % 2 === 0" />
            <IzqComponent :category="category" v-else />
        </div>
    </section>
</template>
<script setup>
import IzqComponent from "@/Components/Carrusel/Izquierda.vue";
import DerComponent from "@/Components/Carrusel/Derecha.vue";

defineProps({
    categories: {
        type: Array,
        required: true,
        default: () => [],
    },
});
</script>
