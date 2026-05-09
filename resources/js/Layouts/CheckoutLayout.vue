<template>
    <div>
        <Head>
            <title>{{ title_meta }} | Brana</title>
            <meta property="og:title" :content="title_meta + ' | Brana'" />
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
