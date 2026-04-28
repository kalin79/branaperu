<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        @click="onClick"
        class="inline-flex items-center justify-center font-medium transition-all duration-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-[0.97]"
        :class="[
            sizeClass,
            variantClass,
            fullWidth ? 'w-full' : '',
            disabled || loading ? 'opacity-70 cursor-not-allowed' : '',
        ]"
    >
        <!-- Icono izquierdo -->
        <slot name="icon-left" />

        <!-- Loading spinner -->
        <span v-if="loading" class="animate-spin -ml-1 mr-2">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                />
            </svg>
        </span>

        <span>{{ label }}</span>

        <!-- Icono derecho -->
        <slot name="icon-right" />
    </button>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    label: { type: String, required: true },
    variant: { type: String, default: "primary" }, // primary, secondary, outline, ghost, danger
    size: { type: String, default: "md" }, // sm, md, lg
    fullWidth: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    type: { type: String, default: "button" },
});

const emit = defineEmits(["click"]);

const sizeClass = computed(
    () =>
        ({
            sm: "px-4 py-2 text-sm",
            md: "px-6 py-3 text-base",
            lg: "px-8 py-4 text-lg",
        })[props.size],
);

const variantClass = computed(() => {
    const variants = {
        primary: "bg-orange-600 hover:bg-orange-700 text-white",
        secondary: "bg-gray-900 hover:bg-gray-800 text-white",
        outline:
            "border-2 border-orange-600 text-orange-600 hover:bg-orange-50",
        ghost: "text-gray-700 hover:bg-gray-100",
        danger: "bg-red-600 hover:bg-red-700 text-white",
    };
    return variants[props.variant] || variants.primary;
});

const onClick = (e) => {
    if (!props.disabled && !props.loading) emit("click", e);
};
</script>
