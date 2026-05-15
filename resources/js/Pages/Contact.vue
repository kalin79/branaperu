<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as useInertiaForm, Link, usePage } from "@inertiajs/vue3";
import { computed, onMounted, watch } from "vue";
import { useForm as useVeeForm } from "vee-validate";
import HeroComponent from "@/Components/Hero/Hero.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "zod";

const props = defineProps({
    title_meta: { type: String, default: "Contáctenos" },
    description_meta: { type: String, default: "" },
    cart: { type: Object, default: () => ({}) },
    total: { type: Number, default: 0 },
});

const banners = [
    {
        titulo: "Libro de Reclamaciones",
        accion: "Ver equipos",
        imagepc: "/images/contacto.png",
        imagemobile: "/images/contacto.png",
    },
];

// ====================== SCHEMA ZOD ======================
const schema = toTypedSchema(
    z.object({
        full_name: z
            .string()
            .min(2, "Tu nombre debe tener al menos 2 caracteres")
            .max(100, "Nombre demasiado largo"),
        email: z
            .string()
            .min(1, "El correo es requerido")
            .email("Ingresa un correo válido"),
        phone: z
            .string()
            .min(9, "El celular debe tener al menos 9 dígitos")
            .regex(/^[0-9+\s-]+$/, "Número de celular inválido"),
        subject: z
            .string()
            .min(3, "El asunto debe tener al menos 3 caracteres")
            .max(150, "Asunto demasiado largo"),
        message: z
            .string()
            .min(10, "El mensaje debe tener al menos 10 caracteres")
            .max(2000, "Mensaje demasiado largo"),
        accepted_terms: z.literal(true, {
            errorMap: () => ({
                message: "Debes aceptar los términos y condiciones",
            }),
        }),
    }),
);

// ====================== VEE-VALIDATE ======================
const { handleSubmit, defineField, errors, resetForm } = useVeeForm({
    validationSchema: schema,
    initialValues: {
        full_name: "",
        email: "",
        phone: "",
        subject: "",
        message: "",
        accepted_terms: false,
    },
});

const [full_name, full_nameAttrs] = defineField("full_name");
const [email, emailAttrs] = defineField("email");
const [phone, phoneAttrs] = defineField("phone");
const [subject, subjectAttrs] = defineField("subject");
const [message, messageAttrs] = defineField("message");
const [accepted_terms, accepted_termsAttrs] = defineField("accepted_terms");

// ====================== USER LOGUEADO (autocompleta) ======================
const page = usePage();
const authUser = computed(() => page.props.auth?.user || null);

onMounted(() => {
    if (authUser.value) {
        full_name.value = [authUser.value.name, authUser.value.last_name]
            .filter(Boolean)
            .join(" ")
            .trim();
        email.value = authUser.value.email || "";
        phone.value = authUser.value.phone || "";
    }
});

// ====================== FLASH (success / error) ======================
const flashSuccess = computed(() => page.props.flash?.success || null);
const flashErrors = computed(() => page.props.errors || {});

// ====================== INERTIA FORM ======================
const inertiaForm = useInertiaForm({
    full_name: "",
    email: "",
    phone: "",
    subject: "",
    message: "",
    accepted_terms: false,
});

const onSubmit = handleSubmit((values) => {
    inertiaForm.full_name = values.full_name;
    inertiaForm.email = values.email;
    inertiaForm.phone = values.phone;
    inertiaForm.subject = values.subject;
    inertiaForm.message = values.message;
    inertiaForm.accepted_terms = values.accepted_terms;

    inertiaForm.post("/contacto", {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
            // Re-autocompleta si está logueado
            if (authUser.value) {
                full_name.value = [
                    authUser.value.name,
                    authUser.value.last_name,
                ]
                    .filter(Boolean)
                    .join(" ")
                    .trim();
                email.value = authUser.value.email || "";
                phone.value = authUser.value.phone || "";
            }
        },
        onError: (errs) => {
            console.error("❌ Errores contacto:", errs);
        },
    });
});

const tiendas = [
    {
        nombre: "Galería Barrio Chino",
        direccion: "Jr. Paruro 860 / Jr. Ucayali 724, Cercado de Lima",
        ref: "Ref. Calle Capón cerca al Mcdo. Central",
        stands: "Stands 1077, 1102, 1113 y 1080",
    },
    {
        nombre: "Jesús María",
        direccion: "Av. Arnaldo Márquez 1082",
        ref: "Ref. Al costado de la comisaría del distrito de Jesús María",
    },
];
</script>

<template>
    <AppLayout
        :cart="cart"
        :total="total"
        :title_meta="title_meta"
        :description_meta="description_meta"
    >
        <!-- BLOQUE PRINCIPAL -->
        <section class="contactMain">
            <HeroComponent type="legal" :slides="banners" />
            <div class="container-fluid">
                <div class="contactGrid">
                    <!-- COLUMNA IZQUIERDA: FORM -->
                    <div class="contactFormCol">
                        <h1>Conecta con nosotros</h1>
                        <p class="muted">
                            Completa el formulario de contacto, escríbenos
                            directamente a nuestro correo, tu experiencia
                            comienza con un mensaje. Nosotros nos encargamos del
                            resto.
                        </p>

                        <!-- Alerta de éxito -->
                        <div v-if="flashSuccess" class="alertSuccess">
                            {{ flashSuccess }}
                        </div>

                        <!-- Alerta de error general -->
                        <div v-if="flashErrors.general" class="alertError">
                            {{ flashErrors.general }}
                        </div>

                        <form @submit="onSubmit" class="contactForm">
                            <div class="validateRow">
                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="full_name"
                                            v-bind="full_nameAttrs"
                                            type="text"
                                            placeholder="Nombres y Apellidos"
                                            :class="{
                                                'input-error': errors.full_name,
                                            }"
                                        />
                                    </div>
                                    <p class="errorMsg" v-if="errors.full_name">
                                        {{ errors.full_name }}
                                    </p>
                                </div>
                            </div>

                            <div class="validateRow">
                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="email"
                                            v-bind="emailAttrs"
                                            type="email"
                                            placeholder="Correo electrónico"
                                            :class="{
                                                'input-error': errors.email,
                                            }"
                                        />
                                    </div>
                                    <p class="errorMsg" v-if="errors.email">
                                        {{ errors.email }}
                                    </p>
                                </div>
                            </div>

                            <div class="validateRow">
                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="phone"
                                            v-bind="phoneAttrs"
                                            type="tel"
                                            placeholder="Nro. de celular"
                                            :class="{
                                                'input-error': errors.phone,
                                            }"
                                        />
                                    </div>
                                    <p class="errorMsg" v-if="errors.phone">
                                        {{ errors.phone }}
                                    </p>
                                </div>
                            </div>

                            <div class="validateRow">
                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="subject"
                                            v-bind="subjectAttrs"
                                            type="text"
                                            placeholder="Asunto"
                                            :class="{
                                                'input-error': errors.subject,
                                            }"
                                        />
                                    </div>
                                    <p class="errorMsg" v-if="errors.subject">
                                        {{ errors.subject }}
                                    </p>
                                </div>
                            </div>

                            <div class="validateRow">
                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <textarea
                                            v-model="message"
                                            v-bind="messageAttrs"
                                            rows="5"
                                            placeholder="Escribe aquí tu mensaje..."
                                            :class="{
                                                'input-error': errors.message,
                                            }"
                                        ></textarea>
                                    </div>
                                    <p class="errorMsg" v-if="errors.message">
                                        {{ errors.message }}
                                    </p>
                                </div>
                            </div>

                            <div class="validateRow">
                                <div class="rowForm">
                                    <div class="checkboxCustom">
                                        <label>
                                            <input
                                                type="checkbox"
                                                v-model="accepted_terms"
                                                v-bind="accepted_termsAttrs"
                                            />
                                            <span class="checkmark"></span>
                                            <p>
                                                Estoy de acuerdo con los
                                                <Link
                                                    :href="
                                                        route(
                                                            'terminos-y-condiciones',
                                                        )
                                                    "
                                                    >Términos y
                                                    Condiciones</Link
                                                >
                                                y la
                                                <Link
                                                    :href="
                                                        route(
                                                            'politica-de-privacidad',
                                                        )
                                                    "
                                                    >Política de
                                                    Privacidad</Link
                                                >.
                                            </p>
                                        </label>
                                    </div>
                                    <p
                                        class="errorMsg"
                                        v-if="errors.accepted_terms"
                                    >
                                        {{ errors.accepted_terms }}
                                    </p>
                                </div>
                            </div>

                            <div class="validateRow">
                                <div class="rowForm">
                                    <button
                                        type="submit"
                                        :disabled="inertiaForm.processing"
                                        class="btn btnForm"
                                    >
                                        <span>{{
                                            inertiaForm.processing
                                                ? "Enviando..."
                                                : "ENVIAR"
                                        }}</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- COLUMNA DERECHA: INFO TIENDAS -->
                    <div class="contactInfoCol">
                        <h2>Vive la experiencia en nuestras tiendas</h2>
                        <p class="muted">
                            Te invitamos a visitarnos y recibir atención
                            especializada, disfrutando de la experiencia
                            completa de nuestros productos.
                        </p>

                        <div class="tiendaContainer">
                            <div
                                v-for="(tienda, idx) in tiendas"
                                :key="idx"
                                class="tiendaItem"
                            >
                                <div class="tiendaIcon">
                                    <img
                                        src="/images/local.svg"
                                        alt="Tienda"
                                        onerror="this.style.display = 'none'"
                                    />
                                </div>
                                <div class="tiendaData">
                                    <h3>{{ tienda.nombre }}</h3>
                                    <p>{{ tienda.direccion }}</p>
                                    <p>{{ tienda.ref }}</p>
                                    <p v-if="tienda.stands">
                                        {{ tienda.stands }}
                                    </p>
                                </div>
                            </div>

                            <div class="tiendaItem">
                                <div class="tiendaIcon">
                                    <img
                                        src="/images/reloj.svg"
                                        alt="Horarios"
                                        onerror="this.style.display = 'none'"
                                    />
                                </div>
                                <div class="tiendaData">
                                    <h3>Horarios de atención</h3>
                                    <p>
                                        De lunes a sábados 11:00 am – 06:00 pm
                                    </p>
                                    <p>Domingos de 10:30 am – 04:00 pm</p>
                                    <p>Otros horarios previa coordinación</p>
                                </div>
                            </div>

                            <div class="tiendaItem">
                                <div class="tiendaIcon">
                                    <img
                                        src="/images/email.svg"
                                        alt="Correo"
                                        onerror="this.style.display = 'none'"
                                    />
                                </div>
                                <div class="tiendaData">
                                    <h3>Correo electrónico</h3>
                                    <p>
                                        <a href="mailto:branasac@gmail.com"
                                            >branasac@gmail.com</a
                                        >
                                    </p>
                                </div>
                            </div>

                            <div class="tiendaItem">
                                <div class="tiendaIcon">
                                    <img
                                        src="/images/whatsapp.svg"
                                        alt="Whatsapp"
                                        onerror="this.style.display = 'none'"
                                    />
                                </div>
                                <div class="tiendaData">
                                    <h3>Whatsapp</h3>
                                    <p>
                                        <a
                                            href="https://wa.me/51955128016"
                                            target="_blank"
                                            rel="noopener"
                                            >+51 955 128 016</a
                                        >
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="socialContact">
                            <a
                                href="https://www.facebook.com/share/15ZPFT1Hu4/"
                                target="_blanck"
                                aria-label="Facebook"
                            >
                                <img
                                    src="/images/rfacebook.svg"
                                    alt="Whatsapp"
                                    onerror="this.style.display = 'none'"
                                />
                            </a>
                            <a
                                href="https://www.instagram.com/brana.peru?igsh=aDZhbjRzb2U2b2N6"
                                target="_blanck"
                                aria-label="Instagram"
                            >
                                <img
                                    src="/images/rinsta.svg"
                                    alt="Whatsapp"
                                    onerror="this.style.display = 'none'"
                                />
                            </a>
                            <a
                                href="https://www.tiktok.com/@brana.peru?_t=ZM-8wYJe2ctnHy&_r=1"
                                target="_blanck"
                                aria-label="TikTok"
                            >
                                <img
                                    src="/images/rtiktok.svg"
                                    alt="Whatsapp"
                                    onerror="this.style.display = 'none'"
                                />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<style lang="scss">
$color_1: #1f1f1f;
$color_2: #555;
$color_3: #2e7d32;
$color_4: #666;
$color_5: #d32f2f;
$color_6: #fff;
$color_7: #b71c1c;
$font-family_1: inherit;
$border-color_1: #2e7d32;
$border-color_2: #d32f2f;

.contactHero {
    background: #fff;
    padding: 40px 0;
}

.tiendaContainer {
    margin-top: 2rem;
    @media screen and (min-width: 992px) {
        margin-top: 4rem;
    }
}

.contactMain {
    background: #fff;
    padding: 40px 0 80px;
    border-top: 2px solid #2e7d32;
    h1 {
        font-family: Poppins, sans-serif;
        font-size: 1.1rem;
        line-height: 1.25em;
        font-weight: 500;
        color: #000000;
        letter-spacing: -0.025em;
        text-align: left;
        @media screen and (min-width: 992px) {
            font-size: 1.2rem;
            line-height: 1.35em;
        }
        @media screen and (min-width: 1400px) {
            font-size: 1.375rem;
            line-height: 1.35em;
        }
        br {
            display: none;
            @media screen and (min-width: 992px) {
                display: block;
            }
        }
    }
}
.contactGrid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 1rem;
    margin-top: 3rem;
    @media screen and (min-width: 992px) {
        grid-template-columns: repeat(12, 1fr);
        margin-top: 5rem;
    }
}
.contactFormCol {
    @media screen and (min-width: 992px) {
        grid-column: 1 / span 5;
    }
    h2 {
        font-family: Poppins, sans-serif;
        font-size: 1.1rem;
        line-height: 1.25em;
        font-weight: 500;
        color: #000000;
        letter-spacing: -0.025em;
        text-align: left;
        @media screen and (min-width: 992px) {
            font-size: 1.2rem;
            line-height: 1.35em;
        }
        @media screen and (min-width: 1400px) {
            font-size: 1.375rem;
            line-height: 1.35em;
        }
        br {
            display: none;
            @media screen and (min-width: 992px) {
                display: block;
            }
        }
    }
}
.contactInfoCol {
    @media screen and (min-width: 992px) {
        grid-column: 7 / span 5;
    }
    h2 {
        font-family: Poppins, sans-serif;
        font-size: 1.1rem;
        line-height: 1.25em;
        font-weight: 500;
        color: #000000;
        letter-spacing: -0.025em;
        text-align: left;
        @media screen and (min-width: 992px) {
            font-size: 1.2rem;
            line-height: 1.35em;
        }
        @media screen and (min-width: 1400px) {
            font-size: 1.375rem;
            line-height: 1.35em;
        }
        br {
            display: none;
            @media screen and (min-width: 992px) {
                display: block;
            }
        }
    }
}
.muted {
    font-family: Poppins, sans-serif;
    font-size: 0.9rem;
    line-height: 1.25em;
    font-weight: 400;
    color: #4b4545;
    letter-spacing: -0.025em;
    margin-top: 1rem;
    @media screen and (min-width: 992px) {
        font-size: 0.9rem;
        line-height: 1.25em;
    }
    @media screen and (min-width: 1400px) {
        font-size: 1rem;
        line-height: 1.15em;
    }
    br {
        display: none;
        @media screen and (min-width: 992px) {
            display: block;
        }
    }
}
.contactForm {
    margin-top: 2rem;
    .errorMsg {
        color: white !important;
    }
    .validateRow {
        margin-bottom: 14px;
    }
}
.inputContainer {
    input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #d6d6d6;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.15s;
        font-family: $font-family_1;
        &:focus {
            border-color: $border-color_1;
        }
    }
    textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #d6d6d6;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.15s;
        font-family: $font-family_1;
        &:focus {
            border-color: $border-color_1;
        }
    }
    .input-error {
        border-color: $border-color_2;
    }
}
.errorMsg {
    color: $color_5;
    font-size: 12px;
    margin: 4px 2px 0;
}
.checkboxCustom {
    label {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        cursor: pointer;
        font-size: 13px;
    }
    a {
        color: $color_3;
        text-decoration: underline;
    }
}
.btnForm {
    width: 100%;
    padding: 14px;
    border: 0;
    border-radius: 999px;
    background: #2e7d32;
    color: $color_6;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
}
.tiendaItem {
    flex: 1;
    display: flex;
    gap: 14px;
    margin-bottom: 22px;
}
.tiendaIcon {
    flex: 0 0 46px;
    width: 46px;
    height: 46px;
    flex-shrink: 0;
    img {
        width: 100%;
        height: 100%;
    }
}
.tiendaData {
    h3 {
        font-size: 1rem;
        margin: 0 0 4px;
        color: $color_1;
    }
    p {
        margin: 0;
        color: $color_2;
        font-size: 14px;
        line-height: 1.45;
    }
    a {
        color: $color_3;
    }
}
.socialContact {
    display: flex;
    gap: 12px;
    margin-top: 1rem;
    padding-left: 4rem;
    a {
        width: 46px;
        height: 46px;
        border-radius: 50%;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        text-decoration: none;
    }
}
.alertSuccess {
    background: #e8f5e9;
    color: $color_3;
    border: 1px solid #a5d6a7;
    padding: 12px 14px;
    border-radius: 8px;
    margin-bottom: 14px;
    font-size: 14px;
}
.alertError {
    background: #fdecea;
    color: $color_7;
    border: 1px solid #f5c2c0;
    padding: 12px 14px;
    border-radius: 8px;
    margin-bottom: 14px;
    font-size: 14px;
}
</style>
