<template>
    <div
        class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4"
    >
        <div class="max-w-md w-full">
            <!-- Logo centrado -->
            <div class="flex justify-center mb-8">
                <h1 class="text-4xl font-bold text-orange-600">Brana</h1>
            </div>

            <!-- Contenido de Login / Register -->
            <slot />
        </div>
    </div>
</template>

<script setup>
// Este layout es intencionalmente minimalista
</script>
