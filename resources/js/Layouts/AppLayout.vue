<template>
    <div>
        <Head>
            <title>{{ title_meta }} | Brana</title>
            <meta name="description" :content="description_meta" />
            <meta property="og:title" :content="title_meta + ' | Brana'" />
            <meta property="og:description" :content="description_meta" />
        </Head>

        <Header />
        <main>
            <slot />
        </main>
        <Footer />
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import Header from "@/Components/Header.vue";
import Footer from "@/Components/Footer.vue";

defineProps({
    title_meta: String,
    description_meta: String,
});
</script>
