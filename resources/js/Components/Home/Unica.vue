<template>
    <section class="unicaHomeContainer">
        <div class="hoja hoja1">
            <img :src="iconHoja1" alt="" />
        </div>
        <div class="hoja hoja2">
            <img :src="iconHoja2" alt="" />
        </div>
        <div class="hoja hoja3">
            <img :src="iconHoja3" alt="" />
        </div>
        <div class="container-fluid">
            <div class="headerContainer">
                <h2>
                    Lo que hace <span>Única</span> <br />
                    a Brana
                </h2>
            </div>
            <div class="listadoContainer">
                <div
                    class="cardContainer"
                    v-for="(item, index) in items"
                    :key="index"
                >
                    <div class="cardHeader">
                        <div class="imagenContainer">
                            <img :src="item.icono" :alt="item.titulo" />
                        </div>
                    </div>
                    <div class="cardBody">
                        <h3 v-html="item.titulo"></h3>
                        <p v-html="item.descripcion"></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
const iconHoja1 = "/images/hoja1.svg";
const iconHoja2 = "/images/hoja2.svg";
const iconHoja3 = "/images/hoja3.svg";
const items = [
    {
        icono: "/images/unica1.svg",
        titulo: "Ingredientes <br />Naturales",
        descripcion:
            "Formulaciones basadas en extractos botánicos puros para cuidar tu piel y bienestar.",
    },
    {
        icono: "/images/unica2.svg",
        titulo: "Aromaterapia <br />Efectiva",
        descripcion:
            "Aceites esenciales de alta calidad que transforman el ambiente y mejoran tu estado de ánimo.",
    },
    {
        icono: "/images/unica3.svg",
        titulo: "Belleza y <br />Bienestar",
        descripcion:
            "Sin parabenos, siliconas ni químicos agresivos, respetando tu cuerpo y la naturaleza.",
    },
    {
        icono: "/images/unica4.svg",
        titulo: "100% <br />Transparentes",
        descripcion:
            "Información clara sobre cada ingrediente y sus beneficios.",
    },
    {
        icono: "/images/unica5.svg",
        titulo: "Compromiso <br />con el planeta",
        descripcion: "Packaging eco-friendly y materiales reciclables.",
    },
    {
        icono: "/images/unica6.svg",
        titulo: "Productos Veganos <br />y Cruelty-Free",
        descripcion:
            "Respetuosos con los animales y certificados en procesos sostenibles.",
    },
    {
        icono: "/images/unica7.svg",
        titulo: "Hecho en Perú con <br />Amor",
        descripcion:
            "Inspirado en la biodiversidad peruana, con estándares de calidad internacional.",
    },
];
</script>
