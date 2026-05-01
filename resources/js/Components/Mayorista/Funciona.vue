<template>
    <section class="funcionaContainer">
        <div class="hoja hoja1">
            <img :src="iconhoja1" />
        </div>
        <div class="hoja hoja2">
            <img :src="iconhoja2" />
        </div>
        <div class="container-fluid">
            <div class="layoutContainer">
                <div class="leftContainer">
                    <h3>ÚNETE Y HASTA SOCIO</h3>
                    <h2>¿Cómo funciona?</h2>
                    <p>
                        Comprar como mayorista en Brana es sencillo y rápido.
                        Hemos creado un proceso en tres pasos para que puedas
                        acceder a precios preferenciales y comenzar a vender sin
                        complicaciones.
                    </p>
                </div>
                <div class="rightContainer">
                    <div
                        class="rowFuncion"
                        v-for="(funcion, index) in funciones"
                        :key="index"
                    >
                        <h4 v-html="funcion.titulo"></h4>
                        <p v-html="funcion.descripcion"></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
const iconhoja1 = "/images/hoja5.svg";
const iconhoja2 = "/images/hoja6.svg";
const funciones = [
    {
        titulo: "1. Crea tu cuenta mayorista",
        descripcion:
            "Regístrate gratis y accede inmediatamente a la plataforma de precios preferenciales.",
    },
    {
        titulo: "2. Revisa el catálogo y elige tus productos",
        descripcion:
            "Aceite de almendras, lavanda, eucalipto, incienso, etc. Todos con precios especiales por volumen.",
    },
    {
        titulo: "3. Compra y empieza a vender",
        descripcion:
            "Recibe tu pedido y comienza a generar ingresos con productos naturales y de alta rotación.",
    },
];
</script>
