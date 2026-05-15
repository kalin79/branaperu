<template>
    <section class="sectionCuidadoHome">
        <!-- <pre>{{ JSON.stringify(data, null, 2) }}</pre> -->
        <div class="container-fluid">
            <div class="headerContainer">
                <div class="leftContainer">
                    <h3 v-html="data.subtitulo"></h3>
                    <h2 v-html="data.titulo"></h2>
                </div>
                <div class="rightcontainer">
                    <div class="iconContainer">
                        <img :src="data.icono" :alt="data.titulo" />
                    </div>
                    <div class="infoContainer">
                        <p v-html="data.descripcion"></p>
                    </div>
                </div>
            </div>
            <div class="bodyContainer">
                <div class="bloquesContainer">
                    <div
                        class="itemContainer"
                        v-for="(item, index) in data.caracteristicas"
                        :key="index"
                        :class="item.color"
                    >
                        <div class="headerBloque">
                            <img :src="item.icono" :alt="item.titulo" />
                        </div>
                        <div class="bodyBloque">
                            <h2 v-html="item.titulo"></h2>
                            <p v-html="item.descripcion"></p>
                        </div>
                    </div>
                </div>
                <div class="imageCenter">
                    <img :src="data.imagen_fondo" :alt="data.titulo" />
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
const iconPlanta = "/images/flor.svg";
const imgCenter = "/images/iso1.png";
defineProps({
    data: {
        type: Object,
        default: () => null,
    },
});
</script>
