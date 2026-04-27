<template>
    <div class="max-w-7xl mx-auto px-6 py-12">
        <h1 class="text-4xl font-bold mb-10">Categorías</h1>

        <div v-for="category in categories" :key="category.id" class="mb-16">
            <div class="flex justify-between items-end mb-6">
                <h2 class="text-3xl font-bold">{{ category.name }}</h2>
                <span class="text-gray-500">
                    {{ category.paginated_products.total }} productos
                </span>
            </div>

            <!-- Productos -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <div
                    v-for="product in category.paginated_products.data"
                    :key="product.id"
                    class="border rounded-3xl overflow-hidden hover:shadow-lg transition-shadow"
                >
                    <div class="aspect-square bg-gray-100">
                        <img
                            v-if="product.media?.[0]"
                            :src="product.media[0].url"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold line-clamp-2">
                            {{ product.name }}
                        </h3>
                        <p class="text-orange-600 font-medium mt-1">
                            S/ {{ product.price }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <div class="flex justify-center gap-3 mt-8">
                <a
                    v-if="category.paginated_products.prev_page_url"
                    :href="category.paginated_products.prev_page_url"
                    class="px-5 py-2 border rounded-xl hover:bg-gray-50"
                >
                    ← Anterior
                </a>
                <span class="px-5 py-2 text-gray-600">
                    Página {{ category.paginated_products.current_page }} de
                    {{ category.paginated_products.last_page }}
                </span>
                <a
                    v-if="category.paginated_products.next_page_url"
                    :href="category.paginated_products.next_page_url"
                    class="px-5 py-2 border rounded-xl hover:bg-gray-50"
                >
                    Siguiente →
                </a>
            </div>
        </div>
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
