<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    secciones: Array,
    default: () => [],
});
// Reactive active tab (por defecto la primera)
const activeTabId = ref(props.secciones?.[0]?.id || null);
const activeSection = computed(() =>
    props.secciones.find((s) => s.id === activeTabId.value),
);
</script>
<template>
    <div class="container-fluid">
        <!-- <pre>{{ JSON.stringify(secciones, null, 2) }}</pre> -->
        <div class="headerContainer">
            <h2>
                Todo acerca del <br />
                <span>producto</span>
            </h2>
        </div>
        <div class="tabContainer">
            <div class="tabsHeader">
                <button
                    v-for="seccion in secciones"
                    :key="seccion.id"
                    :class="{ active: activeTabId === seccion.id }"
                    @click="activeTabId = seccion.id"
                    class="tab-button"
                >
                    <span v-html="seccion.name"></span>
                </button>
            </div>
            <div class="tabsContent">
                <div v-if="activeSection" class="content-wrapper">
                    <!-- Imagen / Media (si existe) -->
                    <div
                        v-if="
                            activeSection.media_type === 'image' &&
                            activeSection.file_media
                        "
                        class="section-image"
                    >
                        <img
                            :src="`/storage/${activeSection.file_media}`"
                            :alt="activeSection.name"
                        />
                    </div>

                    <div
                        class="section-video"
                        v-if="
                            activeSection.media_type === 'youtube' &&
                            activeSection.video_id
                        "
                    >
                        <iframe
                            width="560"
                            height="315"
                            :src="`https://www.youtube.com/embed/${activeSection.video_id}`"
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

                    <!-- Contenido HTML -->
                    <div
                        class="section-content"
                        v-html="activeSection.content"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</template>
