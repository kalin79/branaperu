<script setup>
import { Link } from "@inertiajs/vue3";
// AJUSTA esta ruta a donde tengas tu componente Footer:
// Si está en resources/js/Components/Footer.vue → así está bien.
// Si está en otro lado, cambia la ruta.
import Footer from "@/Components/Footer.vue";

defineProps({
    title: { type: String, default: "" },
    metaTitle: { type: String, default: "Brana" },
    image: { type: String, required: true },
    imageAlt: { type: String, default: "" },
});
</script>

<template>
    <div class="authSplitContainer">
        <main class="authMain">
            <!-- IZQUIERDA: imagen del producto -->
            <aside class="authImageSide">
                <img :src="image" :alt="imageAlt" />
            </aside>

            <!-- DERECHA: form -->
            <section class="authFormSide">
                <header class="authTopBar">
                    <Link href="/" class="authLogo" aria-label="Ir al inicio">
                        <img src="/images/logo.svg" alt="Brana" />
                    </Link>
                    <Link href="/" class="btnVolver">VOLVER AL INICIO</Link>
                </header>

                <div class="authFormWrapper">
                    <div class="authFormHeader" v-if="$slots.header || title">
                        <slot name="header">
                            <h1 v-html="title"></h1>
                        </slot>
                        <p class="authFormSubtitle" v-if="$slots.subtitle">
                            <slot name="subtitle" />
                        </p>
                    </div>

                    <div class="authFormBody">
                        <slot />
                    </div>
                </div>
            </section>
        </main>

        <Footer />
    </div>
</template>

<style scoped lang="scss">
.authSplitContainer {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background: #fff;
}

.authMain {
    display: grid;
    grid-template-columns: minmax(0, 45%) minmax(0, 55%);
    flex: 1;
    min-height: 720px;

    @media (max-width: 991px) {
        grid-template-columns: 1fr;
        min-height: auto;
    }
}

.authImageSide {
    position: relative;
    overflow: hidden;
    background: #2d4a3e;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    @media (max-width: 991px) {
        height: 380px;
    }
}

.authFormSide {
    background-image: url("/images/flor.webp");
    background-position: -240px top;
    background-repeat: no-repeat;
    background-size: contain;
    position: relative;
    display: flex;
    flex-direction: column;
    padding: 32px 40px 48px;

    @media (max-width: 600px) {
        padding: 24px 20px 40px;
    }
}

.authTopBar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}

.authLogo img {
    height: 36px;
    width: auto;
    display: block;
}

.btnVolver {
    display: inline-flex;
    justify-content: center;
    padding: 12px 28px;
    border: 1.5px solid #d6dbd7;
    border-radius: 999px;
    background: #fff;
    text-decoration: none;
    transition: all 0.2s ease;

    font-family: Poppins, sans-serif;
    font-size: 0.865rem;
    line-height: 1.5em;
    font-weight: 600;
    color: #727272;
    letter-spacing: -0.025em;
    text-align: left;
    @media screen and (min-width: 992px) {
        font-size: 0.865rem;
        line-height: 1.5em;
    }
    @media screen and (min-width: 1400px) {
        font-size: 0.875rem;
        line-height: 1.5em;
    }
    br {
        display: none;
        @media screen and (min-width: 992px) {
            display: block;
        }
    }

    &:hover {
        border-color: #1faa50;
        color: #1faa50;
    }
}

.authFormWrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    max-width: 460px;
    width: 100%;
    margin: 0 auto;
    padding: 24px 0;
}

.authFormHeader {
    text-align: center;
    margin-bottom: 28px;
}

.authFormSubtitle {
    font-family: Poppins, sans-serif;
    font-size: 1rem;
    line-height: 1.5em;
    font-weight: 400;
    color: #5a4523;
    letter-spacing: -0.025em;
    text-align: center;
    margin-top: 1.5rem;
    @media screen and (min-width: 992px) {
        font-size: 1rem;
        line-height: 1.5em;
    }
    @media screen and (min-width: 1400px) {
        font-size: 1.125rem;
        line-height: 1.5em;
    }
    br {
        display: none;
        @media screen and (min-width: 992px) {
            display: block;
        }
    }
}

.authFormBody {
    width: 100%;
}
</style>
