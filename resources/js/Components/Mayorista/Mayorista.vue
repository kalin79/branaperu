<template>
    <section class="acercaSection">
        <div class="container-fluid">
            <div class="headerContainer">
                <div class="layoutContainer">
                    <div>
                        <div class="alinearContainer">
                            <h2>VENTAS MAYORISTA</h2>
                            <h1>
                                Ser <span>Socio-Vendedor </span><br />
                                Brana te permite <br />
                                comprar mejor, <br />
                                ahorrar más y hacer <br />
                                crecer tu negocio <br />
                                con beneficios <br />
                                exclusivos.
                            </h1>
                            <p>
                                Al registrarte, accedes a ventajas exclusivas
                                <br />
                                pensadas especialmente para distribuidores,
                                <br />
                                emprendedores y negocios que buscan calidad,
                                <br />
                                buen precio y respaldo constante. Estos son los
                                <br />
                                beneficios que recibirás desde el primer día:
                            </p>
                        </div>
                    </div>
                </div>
                <div class="imagenFullContainer">
                    <img :src="imgFondo" alt="Brana" />
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
const imgFondo = "/images/b1venta.webp";
</script>
