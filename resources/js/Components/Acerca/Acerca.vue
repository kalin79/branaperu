<template>
    <section class="acercaSection">
        <div class="container-fluid">
            <div class="headerContainer">
                <div class="layoutContainer">
                    <div>
                        <div class="alinearContainer">
                            <h2>ACERCA DE BRANA</h2>
                            <h1>
                                En Brana creemos <br />
                                que el
                                <span
                                    >bienestar no es <br />
                                    un lujo,
                                </span>
                                sino una <br />
                                necesidad esencial
                            </h1>
                        </div>
                    </div>
                </div>
                <div class="imagenFullContainer">
                    <img :src="imgFondo" alt="Brana" />
                </div>
            </div>
            <div class="bodyContainer">
                <div class="layoutGrid">
                    <div>
                        <h3>
                            Nacimos inspirados en la conexión entre la
                            naturaleza y la ciencia, con la convicción de que
                            cada aroma, cada gota y cada textura puede
                            transformar la vida de las personas. Somos más que
                            una marca de aromaterapia y cosmética natural: somos
                            un espacio donde la autenticidad, la confianza y la
                            empatía se convierten en experiencias que sanan,
                            cuidan y acompañan.
                        </h3>
                    </div>
                    <div>
                        <h2>Visión</h2>
                        <p>
                            Ser un referente mundial en bienestar natural,
                            reconocido por la excelencia, la innovación y la
                            sostenibilidad. Soñamos con construir un legado que
                            trascienda los productos, creando experiencias que
                            acompañen a las personas a vivir con más armonía,
                            confianza y plenitud.
                        </p>
                    </div>
                    <div>
                        <h2>Misión</h2>
                        <p>
                            Nos esforzamosporcrearproductos naturales de Calidad
                            a un precio accessible para mejorar la Calidad de
                            vida de las personas. Comprendemos queelbienestar es
                            importanteen la construcción de unavidadigna y
                            poresobuscamos ser empáticos con nuestrosclientes y
                            usuarios
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
const imgFondo = "/images/bgacerca.webp";
</script>
