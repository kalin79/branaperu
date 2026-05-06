<script setup>
import { Link } from "@inertiajs/vue3";
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import "@splidejs/vue-splide/css";
import { ref, onMounted, nextTick } from "vue";
defineProps({
    galleries: Object,
});
const mainRef = ref(null);
const thumbsRef = ref(null);

const optionsMain = {
    type: "fade",
    rewind: true,
    pagination: false,
    arrows: true,
    heightRatio: 0.75,
};

const optionsThumbs = {
    fixedWidth: 90,
    fixedHeight: 90,
    gap: 10,
    rewind: true,
    pagination: false,
    arrows: false,
    isNavigation: true,
    focus: "center",
    breakpoints: {
        640: { fixedWidth: 70, fixedHeight: 70 },
    },
};

onMounted(async () => {
    await nextTick();

    if (mainRef.value?.splide && thumbsRef.value?.splide) {
        mainRef.value.splide.sync(thumbsRef.value.splide);

        // Opcional: forzar actualización
        // mainRef.value.splide.refresh();
    }
});
</script>
<template>
    <div class="mediaGalleryContainer">
        <!-- <pre>{{ JSON.stringify(galleries, null, 2) }}</pre> -->
        <!-- Galería principal -->
        <Splide ref="mainRef" :options="optionsMain" class="main-splide">
            <SplideSlide v-for="media in galleries" :key="media.id">
                <div class="imgAlignBox">
                    <div v-if="media.media_type === 'image'">
                        <img
                            :src="`/storage/${media.file_url}`"
                            :alt="media.title"
                            class="main-image"
                        />
                    </div>
                    <div class="videoBox" v-else>
                        <iframe
                            width="560"
                            height="315"
                            :src="`https://www.youtube.com/embed/${media.video_id}`"
                            title="YouTube video player"
                            frameborder="0"
                            allow="
                                accelerometer;
                                autoplay;
                                clipboard-write;
                                encrypted-media;
                                gyroscope;
                                picture-in-picture;
                                web-share;
                            "
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
            </SplideSlide>
        </Splide>

        <!-- Thumbnails -->
        <Splide ref="thumbsRef" :options="optionsThumbs" class="thumbs-splide">
            <SplideSlide v-for="mediaThumbs in galleries" :key="mediaThumbs.id">
                <img
                    :src="`/storage/${mediaThumbs.thumbnail_url}`"
                    :alt="mediaThumbs.name"
                    class="thumb-image"
                />
            </SplideSlide>
        </Splide>
    </div>
</template>
<style lang="scss">
.thumbs-splide .splide__slide {
    opacity: 0.6;
    cursor: pointer;
    transition: all 0.3s;
}

.thumbs-splide .splide__slide.is-active {
    opacity: 1;
    border: 2px solid #0a5d31 !important;
    border-radius: 6px;
}
.main-splide {
    .splide__arrow {
        svg {
            fill: white;
        }
    }
}
</style>
