<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as useInertiaForm, Link, usePage } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import { useForm as useVeeForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import HeroComponent from "@/Components/Hero/Hero.vue";
import { z } from "zod";

const props = defineProps({
    title_meta: { type: String, default: "Libro de Reclamaciones" },
    description_meta: { type: String, default: "" },
    cart: { type: Object, default: () => ({}) },
    total: { type: Number, default: 0 },
    company: { type: Object, default: () => ({}) },
    document_types: { type: Object, default: () => ({}) },
    claim_types: { type: Object, default: () => ({}) },
});
const banners = [
    {
        titulo: "Libro de Reclamaciones",
        accion: "Ver equipos",
        imagepc: "/images/libro.png",
        imagemobile: "/images/libro.png",
    },
];

// ====================== SCHEMA ZOD ======================
const schema = toTypedSchema(
    z.object({
        claim_type: z.enum(["reclamo", "queja"], {
            errorMap: () => ({ message: "Selecciona si es reclamo o queja" }),
        }),
        consumer_first_name: z
            .string()
            .min(2, "Los nombres deben tener al menos 2 caracteres")
            .max(100, "Demasiado largo"),
        consumer_last_name: z
            .string()
            .min(2, "Los apellidos deben tener al menos 2 caracteres")
            .max(100, "Demasiado largo"),
        consumer_document_type: z.enum(["DNI", "CE", "PASAPORTE", "RUC"], {
            errorMap: () => ({ message: "Selecciona el tipo de documento" }),
        }),
        consumer_document_number: z
            .string()
            .min(6, "Mínimo 6 caracteres")
            .max(20, "Demasiado largo"),
        consumer_phone: z
            .string()
            .min(9, "El celular debe tener al menos 9 dígitos")
            .regex(/^[0-9+\s-]+$/, "Número inválido"),
        consumer_email: z
            .string()
            .min(1, "El correo es requerido")
            .email("Ingresa un correo válido"),
        product_name: z.string().max(200).optional().or(z.literal("")),
        order_number: z.string().max(50).optional().or(z.literal("")),
        product_description: z.string().max(2000).optional().or(z.literal("")),
        claim_detail: z
            .string()
            .min(10, "El detalle debe tener al menos 10 caracteres")
            .max(3000, "Demasiado largo"),
        consumer_request: z
            .string()
            .min(5, "Indica tu pedido")
            .max(2000, "Demasiado largo"),
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
        claim_type: "reclamo",
        consumer_first_name: "",
        consumer_last_name: "",
        consumer_document_type: "DNI",
        consumer_document_number: "",
        consumer_phone: "",
        consumer_email: "",
        product_name: "",
        order_number: "",
        product_description: "",
        claim_detail: "",
        consumer_request: "",
        accepted_terms: false,
    },
});

const [claim_type, claim_typeAttrs] = defineField("claim_type");
const [consumer_first_name, consumer_first_nameAttrs] = defineField(
    "consumer_first_name",
);
const [consumer_last_name, consumer_last_nameAttrs] =
    defineField("consumer_last_name");
const [consumer_document_type, consumer_document_typeAttrs] = defineField(
    "consumer_document_type",
);
const [consumer_document_number, consumer_document_numberAttrs] = defineField(
    "consumer_document_number",
);
const [consumer_phone, consumer_phoneAttrs] = defineField("consumer_phone");
const [consumer_email, consumer_emailAttrs] = defineField("consumer_email");
const [product_name, product_nameAttrs] = defineField("product_name");
const [order_number, order_numberAttrs] = defineField("order_number");
const [product_description, product_descriptionAttrs] = defineField(
    "product_description",
);
const [claim_detail, claim_detailAttrs] = defineField("claim_detail");
const [consumer_request, consumer_requestAttrs] =
    defineField("consumer_request");
const [accepted_terms, accepted_termsAttrs] = defineField("accepted_terms");

// ====================== USER LOGUEADO ======================
const page = usePage();
const authUser = computed(() => page.props.auth?.user || null);

onMounted(() => {
    if (authUser.value) {
        consumer_first_name.value = authUser.value.name || "";
        consumer_last_name.value = authUser.value.last_name || "";
        consumer_email.value = authUser.value.email || "";
        consumer_phone.value = authUser.value.phone || "";
        if (authUser.value.document_type) {
            consumer_document_type.value = authUser.value.document_type;
        }
        if (authUser.value.document_number) {
            consumer_document_number.value = authUser.value.document_number;
        }
    }
});

// ====================== FLASH ======================
const flashSuccess = computed(() => page.props.flash?.success || null);
const flashClaimNumber = computed(() => page.props.flash?.claim_number || null);
const flashErrors = computed(() => page.props.errors || {});

// ====================== INERTIA FORM ======================
const inertiaForm = useInertiaForm({
    claim_type: "reclamo",
    consumer_first_name: "",
    consumer_last_name: "",
    consumer_document_type: "DNI",
    consumer_document_number: "",
    consumer_phone: "",
    consumer_email: "",
    product_name: "",
    order_number: "",
    product_description: "",
    claim_detail: "",
    consumer_request: "",
    accepted_terms: false,
});

const onSubmit = handleSubmit((values) => {
    Object.assign(inertiaForm, values);

    inertiaForm.post("/libro-de-reclamaciones", {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
            // re-llenar si está logueado
            if (authUser.value) {
                consumer_first_name.value = authUser.value.name || "";
                consumer_last_name.value = authUser.value.last_name || "";
                consumer_email.value = authUser.value.email || "";
                consumer_phone.value = authUser.value.phone || "";
            }
            window.scrollTo({ top: 0, behavior: "smooth" });
        },
        onError: (errs) => {
            console.error("❌ Errores reclamo:", errs);
        },
    });
});
</script>

<template>
    <AppLayout
        :cart="cart"
        :total="total"
        :title_meta="title_meta"
        :description_meta="description_meta"
    >
        <!-- BLOQUE PRINCIPAL -->
        <section class="claimMain">
            <HeroComponent type="legal" :slides="banners" />
            <div class="container-fluid">
                <!-- Datos de la empresa -->
                <div class="claimHeader">
                    <div class="empresaInfo">
                        <p><strong>Razón Social:</strong> {{ company.name }}</p>
                        <p><strong>RUC:</strong> {{ company.ruc }}</p>
                        <p><strong>Dirección:</strong> {{ company.address }}</p>
                    </div>
                    <div class="empresaInfoExtra">
                        <p>
                            Completa el formulario y nuestro equipo revisará tu
                            solicitud con atención, transparencia y el
                            compromiso de ofrecerte una respuesta oportuna. Tu
                            opinión nos ayuda a seguir creando experiencias más
                            armoniosas para ti.
                        </p>
                    </div>
                </div>

                <!-- Mensaje de éxito -->
                <div v-if="flashSuccess" class="alertSuccess">
                    <strong>¡Listo!</strong> {{ flashSuccess }}
                    <span v-if="flashClaimNumber" class="claimCode">
                        N° {{ flashClaimNumber }}
                    </span>
                </div>

                <!-- Mensaje de error -->
                <div v-if="flashErrors.general" class="alertError">
                    {{ flashErrors.general }}
                </div>

                <form @submit="onSubmit" class="claimForm">
                    <!-- ============= 1. CONSUMIDOR ============= -->
                    <h2 class="sectionTitle">
                        1. Identificación del consumidor reclamante
                    </h2>
                    <div class="gridContainer">
                        <div class="grid2">
                            <div class="field">
                                <input
                                    v-model="consumer_first_name"
                                    v-bind="consumer_first_nameAttrs"
                                    type="text"
                                    placeholder="Nombres"
                                    :class="{
                                        'input-error':
                                            errors.consumer_first_name,
                                    }"
                                />
                                <p
                                    class="errorMsg"
                                    v-if="errors.consumer_first_name"
                                >
                                    {{ errors.consumer_first_name }}
                                </p>
                            </div>

                            <div class="field">
                                <input
                                    v-model="consumer_last_name"
                                    v-bind="consumer_last_nameAttrs"
                                    type="text"
                                    placeholder="Apellidos"
                                    :class="{
                                        'input-error':
                                            errors.consumer_last_name,
                                    }"
                                />
                                <p
                                    class="errorMsg"
                                    v-if="errors.consumer_last_name"
                                >
                                    {{ errors.consumer_last_name }}
                                </p>
                            </div>

                            <div class="field">
                                <select
                                    v-model="consumer_document_type"
                                    v-bind="consumer_document_typeAttrs"
                                    :class="{
                                        'input-error':
                                            errors.consumer_document_type,
                                    }"
                                >
                                    <option value="">Tipo de documento</option>
                                    <option
                                        v-for="(label, key) in document_types"
                                        :key="key"
                                        :value="key"
                                    >
                                        {{ label }}
                                    </option>
                                </select>
                                <p
                                    class="errorMsg"
                                    v-if="errors.consumer_document_type"
                                >
                                    {{ errors.consumer_document_type }}
                                </p>
                            </div>

                            <div class="field">
                                <input
                                    v-model="consumer_document_number"
                                    v-bind="consumer_document_numberAttrs"
                                    type="text"
                                    placeholder="Número de documento"
                                    :class="{
                                        'input-error':
                                            errors.consumer_document_number,
                                    }"
                                />
                                <p
                                    class="errorMsg"
                                    v-if="errors.consumer_document_number"
                                >
                                    {{ errors.consumer_document_number }}
                                </p>
                            </div>

                            <div class="field">
                                <input
                                    v-model="consumer_phone"
                                    v-bind="consumer_phoneAttrs"
                                    type="tel"
                                    placeholder="Nro. de celular"
                                    :class="{
                                        'input-error': errors.consumer_phone,
                                    }"
                                />
                                <p
                                    class="errorMsg"
                                    v-if="errors.consumer_phone"
                                >
                                    {{ errors.consumer_phone }}
                                </p>
                            </div>

                            <div class="field">
                                <input
                                    v-model="consumer_email"
                                    v-bind="consumer_emailAttrs"
                                    type="email"
                                    placeholder="Correo electrónico"
                                    :class="{
                                        'input-error': errors.consumer_email,
                                    }"
                                />
                                <p
                                    class="errorMsg"
                                    v-if="errors.consumer_email"
                                >
                                    {{ errors.consumer_email }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ============= 2. BIEN CONTRATADO ============= -->
                    <h2 class="sectionTitle">
                        2. Identificación del bien contratado
                    </h2>
                    <div class="gridContainer">
                        <div class="grid2">
                            <div class="field">
                                <input
                                    v-model="product_name"
                                    v-bind="product_nameAttrs"
                                    type="text"
                                    placeholder="Producto"
                                />
                            </div>
                            <div class="field">
                                <input
                                    v-model="order_number"
                                    v-bind="order_numberAttrs"
                                    type="text"
                                    placeholder="Nro. de pedido"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="gridContainer">
                        <div class="field">
                            <textarea
                                v-model="product_description"
                                v-bind="product_descriptionAttrs"
                                placeholder="Descripción"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>

                    <!-- ============= 3. DETALLE ============= -->
                    <h2 class="sectionTitle">
                        3. Detalle de la reclamación y pedido del consumidor
                    </h2>

                    <div class="gridContainer">
                        <div class="field">
                            <select
                                v-model="claim_type"
                                v-bind="claim_typeAttrs"
                                :class="{ 'input-error': errors.claim_type }"
                            >
                                <option value="">Tipo</option>
                                <option
                                    v-for="(label, key) in claim_types"
                                    :key="key"
                                    :value="key"
                                >
                                    {{ label }}
                                </option>
                            </select>
                            <p class="errorMsg" v-if="errors.claim_type">
                                {{ errors.claim_type }}
                            </p>
                        </div>
                    </div>
                    <div class="gridContainer">
                        <div class="field">
                            <textarea
                                v-model="claim_detail"
                                v-bind="claim_detailAttrs"
                                placeholder="Detalle"
                                rows="4"
                                :class="{ 'input-error': errors.claim_detail }"
                            ></textarea>
                            <p class="errorMsg" v-if="errors.claim_detail">
                                {{ errors.claim_detail }}
                            </p>
                        </div>
                    </div>

                    <div class="gridContainer">
                        <div class="field">
                            <textarea
                                v-model="consumer_request"
                                v-bind="consumer_requestAttrs"
                                placeholder="Pedido (¿Qué es lo que solicitas?)"
                                rows="4"
                                :class="{
                                    'input-error': errors.consumer_request,
                                }"
                            ></textarea>
                            <p class="errorMsg" v-if="errors.consumer_request">
                                {{ errors.consumer_request }}
                            </p>
                        </div>
                    </div>

                    <!-- ============= TEXTO LEGAL ============= -->
                    <div class="gridContainer">
                        <div class="legalText">
                            <p>
                                <strong>* RECLAMO:</strong> Disconformidad
                                relacionada a los productos o servicios.
                            </p>
                            <p>
                                <strong>** QUEJA:</strong> Disconformidad no
                                relacionada a los productos o servicios; o
                                malestar o descontento respecto a la atención al
                                público.
                            </p>
                            <p>
                                La formulación del reclamo no impide acudir a
                                otras vías de solución de controversias ni es
                                requisito previo para interponer una denuncia
                                ante el INDECOPI.
                            </p>
                            <p>
                                <em>
                                    El proveedor debe dar respuesta al reclamo o
                                    queja en un plazo no mayor a quince (15)
                                    días hábiles, el cual es improrrogable.
                                </em>
                            </p>
                        </div>
                    </div>

                    <!-- ============= TERMINOS ============= -->
                    <div class="gridContainer">
                        <div class="field">
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
                                                route('terminos-y-condiciones')
                                            "
                                            >Términos y Condiciones</Link
                                        >
                                        y la
                                        <Link
                                            :href="
                                                route('politica-de-privacidad')
                                            "
                                            >Política de Privacidad</Link
                                        >.
                                    </p>
                                </label>
                            </div>
                            <p class="errorMsg" v-if="errors.accepted_terms">
                                {{ errors.accepted_terms }}
                            </p>
                        </div>
                    </div>

                    <!-- ============= BOTON ============= -->
                    <div class="gridContainer">
                        <div class="field">
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
        </section>
    </AppLayout>
</template>

<style lang="scss">
$color_1: #1f1f1f;
$color_2: #555;
$color_3: #333;
$color_4: #2e7d32;
$color_5: #d32f2f;
$color_6: #fff;
$color_7: #1b5e20;
$color_8: #b71c1c;
$font-family_1: inherit;
$border-color_1: #2e7d32;
$border-color_2: #d32f2f;
.gridContainer {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 1rem;
    > div {
        grid-column: 1 / -1;
        @media screen and (min-width: 992px) {
            grid-column: 1 / span 8;
        }
    }
}
.claimHero {
    background: #fff;
    padding: 40px 0;
}
.claimHeroContent {
    max-width: 540px;
    h1 {
        font-size: 2rem;
        margin: 0 0 12px;
        color: $color_1;
        line-height: 1.15;
    }
    p {
        color: $color_2;
        line-height: 1.5;
    }
}
.alertSuccess {
    margin-top: 1rem;
}
.claimMain {
    background: #fff;
    padding: 40px 0 80px;
    border-top: 2px solid #2e7d32;
}
.claimHeader {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    padding-top: 3rem;
    @media screen and (min-width: 992px) {
        grid-template-columns: repeat(12, 1fr);
        padding-top: 5rem;
    }
    .empresaInfo {
        @media screen and (min-width: 992px) {
            grid-column: 1 / span 4;
        }
    }
    .empresaInfoExtra {
        @media screen and (min-width: 992px) {
            grid-column: 7 / span 5;
        }
    }
}
.empresaInfo {
    p {
        font-family: Poppins, sans-serif;
        font-size: 0.95rem;
        line-height: 1.5em;
        font-weight: 500;
        color: black;
        letter-spacing: -0.025em;
        text-align: left;
        @media screen and (min-width: 992px) {
            font-size: 1rem;
            line-height: 1.35em;
        }
        @media screen and (min-width: 1400px) {
            font-size: 1.125rem;
            line-height: 1.35em;
        }
        strong {
            font-weight: 700;
        }
        br {
            display: none;
            @media screen and (min-width: 992px) {
                display: block;
            }
        }
    }
}
.empresaInfoExtra {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: flex-start;
    p {
        font-family: Poppins, sans-serif;
        font-size: 0.9rem;
        line-height: 1.25em;
        font-weight: 400;
        color: #4b4545;
        letter-spacing: -0.025em;
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
}
.sectionTitle {
    font-size: 1rem;
    color: $color_4;
    margin: 24px 0 12px;
}
.grid2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
.field {
    margin-bottom: 14px;
    input[type="text"],
    input[type="email"],
    input[type="tel"] {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #d6d6d6;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        font-family: $font-family_1;
        background: #fff;
        transition: border-color 0.15s;
        &:focus {
            border-color: $border-color_1;
        }
    }
    select {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #d6d6d6;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        font-family: $font-family_1;
        background: #fff;
        transition: border-color 0.15s;
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
        font-family: $font-family_1;
        background: #fff;
        transition: border-color 0.15s;
        &:focus {
            border-color: $border-color_1;
        }
    }
}
.input-error {
    border-color: $border-color_2 !important;
}
.errorMsg {
    color: $color_5;
    font-size: 12px;
    margin: 4px 2px 0;
}
.legalText {
    background: #fafafa;
    border-left: 4px solid #2e7d32;
    padding: 14px 18px;
    border-radius: 6px;
    margin: 18px 0;
    font-size: 12px;
    color: $color_2;
    line-height: 1.5;
    p {
        margin: 0 0 6px;
    }
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
        color: $color_4;
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
    margin-top: 16px;
    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
}
.alertSuccess {
    background: #e8f5e9;
    color: $color_7;
    border: 1px solid #a5d6a7;
    padding: 14px 16px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 14px;
    .claimCode {
        display: inline-block;
        margin-left: 8px;
        background: #2e7d32;
        color: $color_6;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }
}
.alertError {
    background: #fdecea;
    color: $color_8;
    border: 1px solid #f5c2c0;
    padding: 12px 14px;
    border-radius: 8px;
    margin-bottom: 14px;
    font-size: 14px;
}
</style>
