<!-- Components/Hero/Hero.vue -->
<script setup>
import HeroSlider from "./HeroSimple.vue";

defineProps({
    slides: {
        type: Array,
        required: true,
        default: () => [],
    },
    type: { type: String, default: "slider" },
});

const components = {
    slider: HeroSlider,
};

// Si solo tienes un tipo por ahora, puedes simplificarlo mucho
</script>
<template>
    <component
        :is="components[type] || components.slider"
        :slides="slides"
        v-bind="$attrs"
    />
</template>
