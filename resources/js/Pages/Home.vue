<template>
    <div>
        <pre>{{ JSON.stringify(categories, null, 2) }}</pre>
    </div>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

defineOptions({
    layout: AppLayout,
});

defineProps({
    categories: {
        type: Array,
        required: true,
        default: () => [],
    },
});
</script>
