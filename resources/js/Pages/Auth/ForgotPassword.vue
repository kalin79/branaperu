<script setup>
import AuthSplitLayout from "@/Layouts/AuthSplitLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

defineProps({
    status: { type: String, default: "" },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <AuthSplitLayout
        meta-title="Recuperar contraseña - Brana"
        image="/images/auth/login.jpg"
        image-alt="Brana"
    >
        <template #header>
            <h1>Recupera tu <em>contraseña</em></h1>
        </template>
        <template #subtitle>
            Ingresa tu correo electrónico y te enviaremos un enlace para
            restablecer tu contraseña.
        </template>

        <div v-if="status" class="authStatus">{{ status }}</div>

        <form @submit.prevent="submit" class="authForm" novalidate>
            <div class="authField">
                <div class="authInputWrap">
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

            <button
                type="submit"
                class="authBtnPrimary"
                :disabled="form.processing"
            >
                {{ form.processing ? "ENVIANDO..." : "ENVIAR ENLACE" }}
            </button>
        </form>

        <div class="authFooterLink">
            <p>¿Ya recordaste tu contraseña?</p>
            <Link :href="route('login')">Volver a iniciar sesión</Link>
        </div>
    </AuthSplitLayout>
</template>

<style scoped lang="scss">
.authStatus {
    margin-bottom: 16px;
    padding: 12px 16px;
    background: #ecfdf5;
    color: #065f46;
    border-radius: 12px;
    font-size: 14px;
    text-align: center;
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

.authInputWrap input {
    width: 100%;
    height: 52px;
    padding: 0 20px;
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

.authError {
    margin: 0;
    padding-left: 20px;
    font-size: 12px;
    color: #ef4444;
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
