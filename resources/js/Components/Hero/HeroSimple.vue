<!-- resources/js/Components/Hero/HeroSimple.vue -->
<script setup>
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import "@splidejs/vue-splide/css"; // Estilos por defecto
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    slides: {
        type: Array,
        required: true,
        default: () => [],
    },
});

// Opciones de Splide (fácil de entender)
const options = {
    // type: "loop", // loop = infinito
    perPage: 1,
    autoplay: true,
    interval: 4000,
    pauseOnHover: true,
    arrows: true, // flechas
    pagination: true, // puntos
    speed: 800,
    gap: 0,
};
</script>

<template>
    <section class="sliderContainerHero">
        <Splide :options="options" class="itemContainer">
            <SplideSlide v-for="(item, index) in props.slides" :key="index">
                <Link :href="item.link" v-if="item.link">
                    <img
                        :src="item.imagepc"
                        :srcset="`${item.imagemobile} 768w, ${item.imagepc} 1200w`"
                        sizes="(max-width: 768px) 100vw, 1200px"
                        :alt="item.titulo || 'Slide'"
                        class="media"
                        loading="lazy"
                    />
                </Link>
                <img
                    v-else
                    :src="item.imagepc"
                    :srcset="`${item.imagemobile} 768w, ${item.imagepc} 1200w`"
                    sizes="(max-width: 768px) 100vw, 1200px"
                    :alt="item.titulo || 'Slide'"
                    class="media"
                    loading="lazy"
                />
            </SplideSlide>
        </Splide>
    </section>
</template>
<style lang="scss" scoped>
/* ==================== FLECHAS ==================== */
:deep(.splide__arrow) {
    background: rgba(255, 255, 255, 0.9);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    color: #1f2937;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease; /* Mantengo la transición suave */
}

/* Posición de las flechas */
:deep(.splide__arrow--prev) {
    left: 20px;
}
:deep(.splide__arrow--next) {
    right: 20px;
}

/* Opcional: Si quieres que cambie un poco al hover (solo opacidad) */
:deep(.splide__arrow:hover) {
    background: rgba(255, 255, 255, 1); /* Solo se pone más blanco */
}

/* ==================== PAGINACIÓN ==================== */
:deep(.splide__pagination) {
    bottom: 30px;
    gap: 10px;
}

:deep(.splide__pagination__page) {
    width: 12px;
    height: 12px;
    background: rgba(255, 255, 255, 0.6);
    border: 2px solid white;
    border-radius: 50%;
    transition: all 0.3s ease;

    &:hover {
        background: rgba(255, 255, 255, 0.85);
    }
}

:deep(.splide__pagination__page.is-active) {
    background: white;
    width: 34px;
    border-radius: 9999px;
}
</style>
