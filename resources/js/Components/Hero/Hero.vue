<!-- Components/Hero/Hero.vue -->
<script setup>
import HeroSlider from "./HeroSimple.vue";
import HeroFooter from "./HeroFooter.vue";

defineProps({
    slides: {
        type: Array,
        default: () => [],
    },
    type: { type: String, default: "slider" },
});

const components = {
    slider: {
        component: HeroSlider,
        props: (props) => ({ slides: props.slides }),
    },
    footer: {
        component: HeroFooter,
        props: () => ({}),
    },
};

// Si solo tienes un tipo por ahora, puedes simplificarlo mucho
</script>
<template>
    <component
        :is="components[type]?.component || HeroSlider"
        v-bind="components[type]?.props({ slides })"
    />
</template>
