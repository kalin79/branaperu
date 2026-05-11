<script setup>
import AuthSplitLayout from "@/Layouts/AuthSplitLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: { type: Boolean, default: true },
    status: { type: String, default: "" },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <AuthSplitLayout
        meta-title="Inicia sesión - Brana"
        image="/images/auth1.webp"
        image-alt="Aceite esencial de Eucalipto"
    >
        <template #header>
            <h1 class="titleH1">Accede a tu <br /><span>cuenta</span></h1>
        </template>
        <template #subtitle>
            Al acceder a su cuenta Brana podrá rastrear y administrar sus
            pedidos y también guardar múltiples direcciones.
        </template>

        <div v-if="status" class="authStatus">{{ status }}</div>

        <form @submit.prevent="submit" class="authForm" novalidate>
            <div class="authField">
                <div class="authInputWrap">
                    <span class="authInputIcon">
                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                            />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                    </span>
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="username"
                        autofocus
                        placeholder="Ingresa correo electrónico"
                        :class="{ 'has-error': form.errors.email }"
                    />
                </div>
                <p v-if="form.errors.email" class="authError">
                    {{ form.errors.email }}
                </p>
            </div>

            <div class="authField">
                <div class="authInputWrap">
                    <span class="authInputIcon">
                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect
                                x="3"
                                y="11"
                                width="18"
                                height="11"
                                rx="2"
                                ry="2"
                            />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </span>
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="Ingresa tu clave"
                        :class="{ 'has-error': form.errors.password }"
                    />
                </div>
                <p v-if="form.errors.password" class="authError">
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="authForgotRow" v-if="canResetPassword">
                <Link :href="route('password.request')" class="authLinkSmall">
                    Olvidaste tu contraseña
                </Link>
            </div>

            <button
                type="submit"
                class="authBtnPrimary"
                :disabled="form.processing"
            >
                {{ form.processing ? "INGRESANDO..." : "INGRESAR" }}
            </button>
        </form>

        <div class="authFooterLink">
            <p>¿Aún no tienes una cuenta?</p>
            <Link :href="route('register')">Regístrate aquí</Link>
        </div>
    </AuthSplitLayout>
</template>

<style scoped lang="scss">
.titleH1 {
    font-family: Poppins, sans-serif;
    font-size: 2.5rem;
    line-height: 0.75em;
    font-weight: 400;
    color: #5a4523;
    letter-spacing: -0.025em;
    text-align: center;
    @media screen and (min-width: 992px) {
        font-size: 3rem;
        line-height: 0.75em;
    }
    @media screen and (min-width: 1400px) {
        font-size: 3.375rem;
        line-height: 0.75em;
    }
    span {
        font-family: "PP Editorial New";
        font-weight: normal;
        color: #0a5d31;
    }
    br {
        display: none;
        @media screen and (min-width: 992px) {
            display: block;
        }
    }
}
.authStatus {
    margin-bottom: 16px;
    padding: 12px 16px;
    background: #ecfdf5;
    color: #065f46;
    border-radius: 12px;
    font-size: 14px;
}

.authForm {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.authField {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.authInputWrap {
    position: relative;
    display: flex;
    align-items: center;

    .authInputIcon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
        display: flex;
        align-items: center;
    }

    input {
        width: 100%;
        height: 52px;
        padding: 0 20px 0 50px;
        border: 1.5px solid #e5e7eb;
        border-radius: 999px;
        background: #fff;
        font-size: 14px;
        color: #1f2937;
        outline: none;
        transition: border-color 0.2s ease;

        &::placeholder {
            color: #9ca3af;
        }

        &:focus {
            border-color: #1faa50;
        }

        &.has-error {
            border-color: #ef4444;
        }
    }
}

.authError {
    margin: 0;
    padding-left: 20px;
    font-size: 12px;
    color: #ef4444;
}

.authForgotRow {
    text-align: right;
    margin-top: -4px;
}

.authLinkSmall {
    font-size: 13px;
    color: #6b7280;
    text-decoration: underline;
    text-underline-offset: 3px;

    &:hover {
        color: #1faa50;
    }
}

.authBtnPrimary {
    margin-top: 8px;
    height: 56px;
    border-radius: 999px;
    border: none;
    background: #1faa50;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.1em;
    cursor: pointer;
    transition: background 0.2s ease;

    &:hover:not(:disabled) {
        background: #178a40;
    }

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
}

.authFooterLink {
    margin-top: 32px;
    text-align: center;

    p {
        margin: 0 0 4px;
        font-size: 14px;
        color: #4b5563;
    }

    a {
        font-size: 14px;
        color: #1f3a2e;
        text-decoration: underline;
        text-underline-offset: 3px;
        font-weight: 500;

        &:hover {
            color: #1faa50;
        }
    }
}
</style>
