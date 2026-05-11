<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

defineProps({
    cart: { type: Object, default: () => ({}) },
    total: { type: Number, default: 0 },
});

const page = usePage();
const user = computed(() => page.props.auth?.user || {});

const logout = () => {
    router.post(route("logout"));
};

const cards = [
    {
        title: "Mis Pedidos",
        href: "/mis-pedidos",
        icon: "bottle",
        accent: "green",
    },
    {
        title: "Mis direcciones",
        href: "/mis-direcciones",
        icon: "pin",
        accent: "green",
    },
    {
        title: "Mi perfil",
        href: "/mi-perfil",
        icon: "user",
        accent: "teal",
    },
];
</script>

<template>
    <AppLayout
        :cart="cart"
        :total="total"
        title_meta="Mi cuenta - Brana"
        description_meta="Tu cuidado natural empieza aquí."
    >
        <div class="dashboardContainer">
            <div class="container-fluid">
                <header class="dashboardHeader">
                    <h1>
                        Tu cuidado <em>natural</em><br />
                        empieza aquí.
                    </h1>
                    <button class="btnLogout" @click="logout">
                        CERRAR SESIÓN
                    </button>
                </header>

                <section class="dashboardCards">
                    <Link
                        v-for="card in cards"
                        :key="card.title"
                        :href="card.href"
                        class="dashboardCard"
                    >
                        <h2>{{ card.title }}</h2>

                        <div class="cardIcon" v-if="card.icon === 'bottle'">
                            <svg
                                width="62"
                                height="62"
                                viewBox="0 0 64 64"
                                fill="none"
                                stroke="#1faa50"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M28 4h8v8h-8z" />
                                <path
                                    d="M22 14h20l-2 8c-1 4-2 7-2 12v22a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4V34c0-5-1-8-2-12l-2-8z"
                                />
                                <path d="M28 30c4 2 8-2 8 4" />
                                <circle cx="48" cy="40" r="3" />
                            </svg>
                        </div>

                        <div class="cardIcon" v-if="card.icon === 'pin'">
                            <svg
                                width="62"
                                height="62"
                                viewBox="0 0 64 64"
                                fill="none"
                                stroke="#1faa50"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M32 8c-9 0-16 7-16 16 0 12 16 28 16 28s16-16 16-28c0-9-7-16-16-16z"
                                />
                                <circle cx="32" cy="24" r="6" />
                            </svg>
                        </div>

                        <div class="cardIcon" v-if="card.icon === 'user'">
                            <svg
                                width="62"
                                height="62"
                                viewBox="0 0 64 64"
                                fill="none"
                                stroke="#0f766e"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="32" cy="22" r="10" />
                                <path d="M14 54c2-10 10-14 18-14s16 4 18 14" />
                            </svg>
                        </div>

                        <span
                            class="cardArrow"
                            :class="{ teal: card.accent === 'teal' }"
                        >
                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="white"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </span>
                    </Link>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped lang="scss">
.dashboardContainer {
    padding: 8rem 0 80px;
    min-height: 60vh;
}

.dashboardHeader {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 32px;
    margin-bottom: 56px;
    flex-wrap: wrap;

    h1 {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 56px;
        font-weight: 600;
        color: #2b1810;
        line-height: 1.1;
        margin: 0;

        em {
            font-style: italic;
            color: #1faa50;
        }
    }

    @media (max-width: 768px) {
        h1 {
            font-size: 36px;
        }
    }
}

.btnLogout {
    height: 56px;
    padding: 0 36px;
    background: #0e2818;
    color: #fff;
    border: none;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.1em;
    cursor: pointer;
    transition: background 0.2s ease;

    &:hover {
        background: #1a3d2a;
    }
}

.dashboardCards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0;
    border-top: 1px solid #e5e7eb;

    @media (max-width: 900px) {
        grid-template-columns: 1fr;
    }
}

.dashboardCard {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 36px 32px 32px;
    min-height: 280px;
    background: #fff;
    border-right: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
    text-decoration: none;
    color: inherit;
    transition: background 0.2s ease;

    &:last-child {
        border-right: none;
    }

    &:hover {
        background: #f9fafb;
    }

    h2 {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 28px;
        font-weight: 500;
        color: #2b1810;
        margin: 0;
    }

    .cardIcon {
        margin: 24px 0;
    }

    .cardArrow {
        position: absolute;
        right: 32px;
        bottom: 32px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #1faa50;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;

        &.teal {
            background: #0f766e;
        }
    }

    &:hover .cardArrow {
        background: #178a40;

        &.teal {
            background: #0d5e58;
        }
    }
}
</style>
