<script setup>
import AuthSplitLayout from "@/Layouts/AuthSplitLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    last_name: "",
    email: "",
    phone: "",
    birth_date: "", // ← NUEVO
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <AuthSplitLayout
        meta-title="Crea tu cuenta - Brana"
        image="/images/register.webp"
        image-alt="Aceite de Argán"
    >
        <template #header>
            <h1 class="titleH1">Regístra tu <br /><span>cuenta</span></h1>
        </template>
        <template #subtitle>
            Al acceder a su cuenta Brana podrá rastrear y administrar sus
            pedidos y también guardar múltiples direcciones.
        </template>

        <form @submit.prevent="submit" class="authForm" novalidate>
            <div class="authField">
                <div class="authInputWrap">
                    <input
                        v-model="form.name"
                        type="text"
                        autocomplete="given-name"
                        autofocus
                        placeholder="Nombres"
                        :class="{ 'has-error': form.errors.name }"
                    />
                </div>
                <p v-if="form.errors.name" class="authError">
                    {{ form.errors.name }}
                </p>
            </div>

            <div class="authField">
                <div class="authInputWrap">
                    <input
                        v-model="form.last_name"
                        type="text"
                        autocomplete="family-name"
                        placeholder="Apellidos"
                        :class="{ 'has-error': form.errors.last_name }"
                    />
                </div>
                <p v-if="form.errors.last_name" class="authError">
                    {{ form.errors.last_name }}
                </p>
            </div>

            <div class="authField">
                <div class="authInputWrap">
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        placeholder="Ingresa tu correo electrónico"
                        :class="{ 'has-error': form.errors.email }"
                    />
                </div>
                <p v-if="form.errors.email" class="authError">
                    {{ form.errors.email }}
                </p>
            </div>

            <div class="authField">
                <div class="authInputWrap">
                    <input
                        v-model="form.phone"
                        type="tel"
                        autocomplete="tel"
                        placeholder="Celular"
                        :class="{ 'has-error': form.errors.phone }"
                    />
                </div>
                <p v-if="form.errors.phone" class="authError">
                    {{ form.errors.phone }}
                </p>
            </div>

            <div class="authField">
                <div class="authInputWrap">
                    <input
                        v-model="form.birth_date"
                        type="date"
                        placeholder="Fecha de nacimiento (opcional)"
                        :class="{ 'has-error': form.errors.birth_date }"
                    />
                </div>
                <p v-if="form.errors.birth_date" class="authError">
                    {{ form.errors.birth_date }}
                </p>
            </div>

            <div class="authField">
                <div class="authInputWrap">
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Ingresa tu clave"
                        :class="{ 'has-error': form.errors.password }"
                    />
                </div>
                <p v-if="form.errors.password" class="authError">
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="authField">
                <div class="authInputWrap">
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Confirma tu clave"
                        :class="{
                            'has-error': form.errors.password_confirmation,
                        }"
                    />
                </div>
                <p v-if="form.errors.password_confirmation" class="authError">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <button
                type="submit"
                class="authBtnPrimary"
                :disabled="form.processing"
            >
                {{ form.processing ? "REGISTRANDO..." : "REGISTRARME" }}
            </button>
        </form>

        <div class="authFooterLink">
            <p>¿Ya tienes una cuenta?</p>
            <Link :href="route('login')">Ingresa aquí</Link>
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
.authForm {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.authField {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.authInputWrap {
    position: relative;

    input {
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
