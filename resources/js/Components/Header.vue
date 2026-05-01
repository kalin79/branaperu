<template>
    <div class="headerMainContainer">
        <div class="container-fluid">
            <div class="lagoutContainer">
                <div class="logoMainContainer">
                    <img :src="logo" alt="Brana" />
                </div>
                <nav class="menuContainer">
                    <Link href="/acerca-de-brana">Acerca de Brana</Link>
                    <Link href="/productos">Catálogo de Productos</Link>
                    <Link href="/ventas-mayorista">Ventas Mayorista</Link>
                    <Link href="/blog">Blog</Link>
                    <Link href="/contacto">Contáctenos</Link>
                </nav>
                <div class="shopContainer">
                    <div class="shoppingBag">
                        <button class="bagContainer">
                            <img :src="bolsa" alt="bolsa de compra" />
                            <div>0</div>
                        </button>
                    </div>
                    <div class="separate">
                        <div class="barra"></div>
                    </div>
                    <div class="userContainer">
                        <a href="/auth/login">
                            <img :src="user" alt="login" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
const logo = "/images/logo.svg";
const bolsa = "/images/bolsa.svg";
const user = "/images/user.svg";
</script>
