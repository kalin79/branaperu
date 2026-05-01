<template>
    <section class="sectionCuidadoHome">
        <!-- <pre>{{ JSON.stringify(data, null, 2) }}</pre> -->
        <div class="container-fluid">
            <div class="headerContainer">
                <div class="leftContainer">
                    <h3>NATURAL, SENSORIAL, REAL.</h3>
                    <h2>
                        Brana: Cuidado <br />
                        personal con <br />
                        propósito
                    </h2>
                </div>
                <div class="rightcontainer">
                    <div class="iconContainer">
                        <img
                            :src="iconPlanta"
                            alt="Brana: Cuidado personal con propósito"
                        />
                    </div>
                    <div class="infoContainer">
                        <p>
                            Creamos experiencias auténticas que nutren tu piel y
                            elevan tus sentidos, con ingredientes de origen
                            natural, fórmulas honestas y un compromiso profundo
                            con el bienestar y la sostenibilidad.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bodyContainer">
                <div class="bloquesContainer">
                    <div
                        class="itemContainer"
                        v-for="(item, index) in data"
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
                    <img
                        :src="imgCenter"
                        alt="Brana: Cuidado personal con propósito"
                    />
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
        type: Array,
        required: true,
        default: () => [],
    },
});
</script>
