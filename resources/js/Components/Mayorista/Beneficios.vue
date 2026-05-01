<template>
    <section class="beneficiosContainer">
        <div class="container-fluid">
            <div class="layoutContainer">
                <div
                    class="cardContainer"
                    v-for="(beneficio, index) in beneficios"
                    :key="index"
                >
                    <div class="headerCard">
                        <img :src="beneficio.icono" alt="Brana" />
                    </div>
                    <div class="bodyCard">
                        <h3 v-html="beneficio.titulo"></h3>
                        <p v-html="beneficio.descripcion"></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
const beneficios = [
    {
        icono: "/images/b1.svg",
        titulo: "Precios preferenciales <br />por volumen",
        descripcion:
            "Obtén acceso a tarifas mayoristas desde tu primera compra. Mientras más compras, más ahorras.",
    },
    {
        icono: "/images/b2.svg",
        titulo: "Experiencias de prueba que <br />impulsan la decisión de compra",
        descripcion:
            "Acceso a probadores que te ayuden a concretar más ventas con tus clientes",
    },
    {
        icono: "/images/b3.svg",
        titulo: "Soporte comercial <br />personalizado",
        descripcion:
            "Recibe asesoría sobre qué productos se venden mejor y cómo impulsar tus ventas.",
    },
    {
        icono: "/images/b4.svg",
        titulo: "Maquila inteligente para <br />marcas que venden",
        descripcion:
            "Servicios de maquila y línea blanca para tus ventas corporativas. Maquila con tu marca.",
    },
];
</script>
