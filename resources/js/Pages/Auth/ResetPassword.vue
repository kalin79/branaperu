<script setup>
import AuthSplitLayout from "@/Layouts/AuthSplitLayout.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <AuthSplitLayout
        meta-title="Nueva contraseña - Brana"
        image="/images/auth/login.jpg"
        image-alt="Brana"
    >
        <template #header>
            <h1>Crea tu nueva <em>contraseña</em></h1>
        </template>
        <template #subtitle>
            Ingresa una nueva contraseña para tu cuenta Brana.
        </template>

        <form @submit.prevent="submit" class="authForm" novalidate>
            <div class="authField">
                <div class="authInputWrap">
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="username"
                        placeholder="Correo electrónico"
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
                        v-model="form.password"
                        type="password"
                        autocomplete="new-password"
                        autofocus
                        placeholder="Nueva contraseña"
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
                        placeholder="Confirma la nueva contraseña"
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
                {{
                    form.processing
                        ? "ACTUALIZANDO..."
                        : "RESTABLECER CONTRASEÑA"
                }}
            </button>
        </form>
    </AuthSplitLayout>
</template>

<style scoped lang="scss">
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
</style>
