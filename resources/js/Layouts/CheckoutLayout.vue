<template>
    <div>
        <Head>
            <title>{{ title_meta }} | Brana</title>
            <meta name="description" :content="description_meta" />
            <meta property="og:title" :content="title_meta + ' | Brana'" />
            <meta property="og:description" :content="description_meta" />
        </Head>

        <HeaderPay />
        <main>
            <slot />
        </main>
        <Footer />
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import HeaderPay from "@/Components/HeaderPay.vue";
import Footer from "@/Components/Footer.vue";

defineProps({
    title_meta: String,
});
</script>
