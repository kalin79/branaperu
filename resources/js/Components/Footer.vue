<template>
    <footer class="footerSectionContainer">
        <div class="iconFondo">
            <img :src="fondo" alt="" />
        </div>
        <div class="container-fluid">
            <div class="layoudContainer">
                <div class="columnaUno">
                    <div class="logoContainer">
                        <img :src="logo" alt="Brana" />
                    </div>
                    <div class="infoContainer">
                        <h3>Mente & Cuerpo <br />en equilibrio natural</h3>
                    </div>
                    <div class="redesContainer">
                        <a href="#" target="_blank">
                            <img :src="facebook" alt="Facebook :: Brana" />
                        </a>
                        <a href="#" target="_blank">
                            <img :src="instagram" alt="Instagram :: Brana" />
                        </a>
                        <a href="#" target="_blank">
                            <img :src="tiktok" alt="Tik Tok :: Brana" />
                        </a>
                    </div>
                </div>
                <div class="columnaDos">
                    <h2>MAPA DE SITIO</h2>
                    <div class="navContainer">
                        <Link href="/">Principal</Link>
                        <Link href="/acerca-de-brana">Acerca de Brana</Link>
                        <Link href="/productos">Catálogo de productos</Link>
                        <Link href="/ventas-mayorista">Ventas Mayorista</Link>
                        <Link href="/blog">BLOG</Link>
                        <Link href="/contacto">Contáctenos</Link>
                        <Link href="/auth/login">Login</Link>
                    </div>
                </div>
                <div class="columnaTres">
                    <h2>NUESTRA POLÍTICAS</h2>
                    <div class="navContainer">
                        <Link href="/terminos-y-condiciones"
                            >Términos y condiciones</Link
                        >
                        <Link href="/politica-de-entrega"
                            >Políticas de entrega</Link
                        >
                        <Link href="/politica-de-privacidad"
                            >Políticas de privacidad</Link
                        >
                        <Link href="/politica-de-cambio"
                            >Política de cambio</Link
                        >
                        <Link href="/libro-de-reclamaciones"
                            >Libro de reclamaciones</Link
                        >
                    </div>
                </div>
                <div class="columnaCuatro">
                    <h2>CONTÁCTANOS</h2>
                    <div class="rowInfoContainer">
                        <div class="infoContainer">
                            <a href="mailto:branasac@brana.pe" target="_blank"
                                >branasac@brana.pe</a
                            >
                            <p>+51 996 357 889</p>
                        </div>
                        <h2>CONTÁCTANOS</h2>
                        <div class="rowInfoContainer">
                            <div class="infoContainer">
                                <div class="rowInfo">
                                    <p class="bold">Galería Barrio Chino</p>
                                    <p>
                                        Jr. Paruro 860 / Jr. Ucayali 724,
                                        Cercado de Lima, Ref. Calle Capón cerca
                                        al Mcdo. Central Stands 1077, 1102, 1113
                                        y 1080
                                    </p>
                                </div>
                                <div class="rowInfo">
                                    <p class="bold">Jesús María</p>
                                    <p>
                                        Av. Arnaldo Márquez 1082 Ref. Al costado
                                        de la comisaria del distrito de Jesús
                                        María
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cierrContainer">
                <p>© 2025 Brana Todos los derechos reservados</p>
            </div>
        </div>
    </footer>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
const logo = "/images/logofooter.svg";
const facebook = "/images/facebook.svg";
const instagram = "/images/instagram.svg";
const tiktok = "/images/tiktok.svg";
const fondo = "/images/florFooter.png";
</script>
