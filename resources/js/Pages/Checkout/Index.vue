<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    useForm as useInertiaForm,
    Link,
    router,
    usePage,
} from "@inertiajs/vue3";
import { ref, computed, watch, watchEffect, onMounted } from "vue";
import { useForm as useVeeForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "zod";

const iconBolsa = "/images/bolsa3.svg";
const iconDelete = "/images/delete2.svg";
const tarjetas = [
    {
        imagen: "/images/t2.png",
    },
    {
        imagen: "/images/t3.png",
    },
    {
        imagen: "/images/t4.png",
    },
    {
        imagen: "/images/t7.png",
    },
    {
        imagen: "/images/t1.png",
    },
    {
        imagen: "/images/t5.png",
        imagen: "/images/t6.png",
    },
];

// ====================== SCHEMA ZOD ======================
const schema = toTypedSchema(
    z.object({
        guest_email: z
            .string()
            .min(1, "El correo es requerido")
            .email("Ingresa un correo válido"),
        guest_name: z
            .string()
            .min(2, "El nombre debe tener al menos 2 caracteres")
            .max(50, "Nombre demasiado largo"),
        guest_last_name: z
            .string()
            .min(2, "El apellido debe tener al menos 2 caracteres")
            .max(50, "Apellido demasiado largo"),
        dni: z
            .string()
            .min(8, "El DNI debe tener al menos 8 caracteres")
            .max(12, "Documento demasiado largo"),
        delivery_district_id: z
            .number({ invalid_type_error: "Selecciona un distrito" })
            .min(1, "Selecciona un distrito"),
        shipping_address: z.string().min(5, "Ingresa una dirección válida"),
        delivery_reference: z.string().optional(),
        guest_phone: z
            .string()
            .min(9, "El celular debe tener al menos 9 dígitos")
            .regex(/^[0-9+\s-]+$/, "Número de celular inválido"),
        accepted_terms: z.literal(true, {
            errorMap: () => ({ message: "Debes aceptar los términos" }),
        }),
        accepted_privacy: z.literal(true, {
            errorMap: () => ({
                message: "Debes aceptar la política de privacidad",
            }),
        }),
    }),
);

// ====================== VEE-VALIDATE ======================
const { handleSubmit, defineField, errors } = useVeeForm({
    validationSchema: schema,
});

// Definir cada campo
const [guest_email, guest_emailAttrs] = defineField("guest_email");
const [guest_name, guest_nameAttrs] = defineField("guest_name");
const [guest_last_name, guest_last_nameAttrs] = defineField("guest_last_name");
const [dni, dniAttrs] = defineField("dni");
const [delivery_district_id, delivery_district_idAttrs] = defineField(
    "delivery_district_id",
);
const [shipping_address, shipping_addressAttrs] =
    defineField("shipping_address");
const [delivery_reference, delivery_referenceAttrs] =
    defineField("delivery_reference");
const [guest_phone, guest_phoneAttrs] = defineField("guest_phone");
const [accepted_terms, accepted_termsAttrs] = defineField("accepted_terms");
const [accepted_privacy, accepted_privacyAttrs] =
    defineField("accepted_privacy");

// ====================== USER LOGUEADO ======================
const page = usePage();
const user = computed(() => page.props.auth?.user || null);

onMounted(() => {
    if (user.value) {
        guest_email.value = user.value.email || "";
        guest_name.value = user.value.name || "";
        guest_last_name.value = user.value.last_name || "";
        guest_phone.value = user.value.phone || "";
    }
});

// ====================== INERTIA FORM (solo para submit) ======================
const inertiaForm = useInertiaForm({
    guest_name: "",
    guest_last_name: "",
    guest_email: "",
    guest_phone: "",
    delivery_district_id: null,
    delivery_full_name: "",
    shipping_address: "",
    delivery_reference: "",
    dni: "",
    accepted_terms: false,
    accepted_privacy: false,
    accepted_marketing: false,
    // ← Campos adicionales que enviamos desde frontend
    subtotal: 0,
    delivery_cost: 0, // ← agregar
    final_total: 0,
    items: [],
});

// ====================== SUBMIT ======================
const onSubmit = handleSubmit((values) => {
    // Asignación directa (forma correcta en Inertia)
    inertiaForm.guest_name = values.guest_name;
    inertiaForm.guest_last_name = values.guest_last_name;
    inertiaForm.guest_email = values.guest_email;
    inertiaForm.guest_phone = values.guest_phone;
    inertiaForm.dni = values.dni;
    inertiaForm.delivery_district_id = values.delivery_district_id;
    inertiaForm.shipping_address = values.shipping_address;
    inertiaForm.delivery_reference = values.delivery_reference || "";
    inertiaForm.accepted_terms = values.accepted_terms;
    inertiaForm.accepted_privacy = values.accepted_privacy;
    inertiaForm.accepted_marketing = false;

    // Datos calculados
    inertiaForm.subtotal = subtotal.value;
    inertiaForm.delivery_cost = displayDelivery.value; // ← agregar
    inertiaForm.final_total = displayTotal.value;
    inertiaForm.delivery_full_name =
        `${values.guest_name} ${values.guest_last_name}`.trim();

    // Items del carrito (muy importante)
    inertiaForm.items = Object.values(props.cart);
    console.log("items count:", inertiaForm.items.length); // debe ser > 0

    console.log("🚀 Datos que se enviarán:", inertiaForm.data());

    console.log(
        "accepted_terms:",
        inertiaForm.accepted_terms,
        typeof inertiaForm.accepted_terms,
    );
    console.log(
        "accepted_privacy:",
        inertiaForm.accepted_privacy,
        typeof inertiaForm.accepted_privacy,
    );
    console.log("items:", inertiaForm.items);

    inertiaForm.post("/checkout/process", {
        onSuccess: () => {
            console.log("✅ Pedido creado correctamente");
        },
        onError: (errors) => {
            console.error("❌ Errores:", errors);
            alert("Errores: " + JSON.stringify(errors, null, 2)); // ← temporal
        },
    });
});

const props = defineProps({
    layout: null, // ← Agrega esta línea
    cart: { type: Object, default: () => ({}) },
    total: { type: Number, default: 0 }, // ← Añadir
    districts: { type: Object, default: () => ({}) },
    defaultDeliveryCost: { type: [Number, String], default: 10 },
    freeShippingThreshold: { type: [Number, String], default: null },
    title_meta: String,
    description_meta: String,
});

// ====================== CÁLCULOS ======================
const subtotal = computed(() => {
    return Object.values(props.cart || {}).reduce((sum, item) => {
        return sum + (Number(item?.price) || 0) * (Number(item?.quantity) || 0);
    }, 0);
});

const deliveryCost = computed(() => {
    if (!delivery_district_id.value)
        return Number(props.defaultDeliveryCost) || 0;

    const allDistricts = Object.values(props.districts || {})
        .flatMap((prov) => Object.values(prov))
        .flat();

    const selected = allDistricts.find(
        (d) => d.id === delivery_district_id.value,
    );

    // ✅ Solo usa el costo del distrito si es mayor a 0
    if (selected && Number(selected.delivery_cost) > 0) {
        return Number(selected.delivery_cost);
    }

    // Si es 0, null o undefined → costo global
    return Number(props.defaultDeliveryCost) || 0;
});

// ====================== VALORES PARA MOSTRAR ======================
const displaySubtotal = computed(() => Number(subtotal.value || 0));
const displayDelivery = computed(() => Number(deliveryCost.value || 0));
const displayTotal = computed(
    () => displaySubtotal.value + displayDelivery.value,
);

// ====================== FILTROS UBICACIÓN ======================
const selectedDepartment = ref("");
const selectedProvince = ref("");

const provinces = computed(() => {
    if (!selectedDepartment.value) return [];
    return Object.keys(props.districts[selectedDepartment.value] || {});
});

const districtsList = computed(() => {
    if (!selectedDepartment.value || !selectedProvince.value) return [];
    return (
        props.districts[selectedDepartment.value]?.[selectedProvince.value] ||
        []
    );
});

watch(selectedDepartment, () => {
    selectedProvince.value = "";
    delivery_district_id.value = null;
});

// ====================== FUNCIONES DE CANTIDAD ======================
const updateQuantity = (productId, newQuantity) => {
    if (newQuantity < 1) return;
    router.post(
        "/cart/update",
        { product_id: productId, quantity: newQuantity },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const increaseQuantity = (item) => updateQuantity(item.id, item.quantity + 1);
const decreaseQuantity = (item) => {
    if (item.quantity > 1) updateQuantity(item.id, item.quantity - 1);
};

const removeProduct = (productId) => {
    if (!confirm("¿Estás seguro de eliminar este producto?")) return;
    router.post(
        "/cart/remove",
        { product_id: productId },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <AppLayout
        :cart="cart"
        :total="total"
        :title_meta="title_meta"
        :description_meta="description_meta"
    >
        <div class="checkoutPageContainer">
            <div class="container-fluid">
                <div class="headerContainer">
                    <div class="imgContainer">
                        <img :src="iconBolsa" alt="Tus compras" />
                    </div>
                    <h1>Tus compras</h1>
                </div>
                <!-- <pre>{{ JSON.stringify(cart, null, 2) }}</pre> -->
                <div class="myPurchasesContainer">
                    <!-- ITEMS -->
                    <div class="myProductsContainer">
                        <div
                            v-for="item in cart"
                            :key="item.id"
                            class="cardCheckoutProduct"
                        >
                            <div class="headerCard">
                                <img
                                    :src="`/storage/${item.cover_image}`"
                                    :alt="item.name"
                                />
                            </div>
                            <div class="bodyCard">
                                <div class="leftContainer">
                                    <h3 v-html="item.category_name"></h3>
                                    <h2 v-html="item.name"></h2>
                                    <p>Tamaño: {{ item.ml }}</p>
                                    <div class="controlProduct">
                                        <button @click="decreaseQuantity(item)">
                                            -
                                        </button>
                                        <span>{{ item.quantity }}</span>
                                        <button @click="increaseQuantity(item)">
                                            +
                                        </button>
                                    </div>
                                </div>
                                <div class="rightContainer">
                                    <p>
                                        S./
                                        {{
                                            (
                                                item.price * item.quantity
                                            ).toFixed(2)
                                        }}
                                    </p>
                                    <button @click="removeProduct(item.id)">
                                        <img
                                            :src="iconDelete"
                                            alt="borrar Producto"
                                        />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FORMULARIO (sin cambios) -->
                    <div class="myDataContainer">
                        <!-- === COLUMNA DERECHA: FORMULARIO === -->
                        <div class="headerData">
                            <h2 v-if="!user">Por favor, coloca tus datos...</h2>
                            <h2 v-else>
                                Hola {{ user.name }}, confirma tus datos...
                            </h2>

                            <Link
                                v-if="!user"
                                :href="
                                    route('login', { redirect: '/checkout' })
                                "
                                class="checkoutLoginLink"
                            >
                                Iniciar sesión
                            </Link>
                        </div>
                        <div class="formContainer">
                            <form @submit="onSubmit">
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="inputContainer">
                                            <input
                                                v-model="guest_email"
                                                v-bind="guest_emailAttrs"
                                                type="email"
                                                placeholder="Ingresa correo electrónico"
                                                :class="{
                                                    'input-error':
                                                        errors.guest_email,
                                                }"
                                            />
                                        </div>
                                        <p
                                            class="errorMsg"
                                            v-if="errors.guest_email"
                                        >
                                            {{ errors.guest_email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="inputContainer">
                                            <input
                                                v-model="guest_name"
                                                v-bind="guest_nameAttrs"
                                                type="text"
                                                placeholder="Nombres"
                                                :class="{
                                                    'input-error':
                                                        errors.guest_name,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <p
                                        class="errorMsg"
                                        v-if="errors.guest_name"
                                    >
                                        {{ errors.guest_name }}
                                    </p>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="inputContainer">
                                            <input
                                                v-model="guest_last_name"
                                                v-bind="guest_last_nameAttrs"
                                                type="text"
                                                placeholder="Apellidos"
                                                :class="{
                                                    'input-error':
                                                        errors.guest_last_name,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <p
                                        class="errorMsg"
                                        v-if="errors.guest_last_name"
                                    >
                                        {{ errors.guest_last_name }}
                                    </p>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="inputContainer">
                                            <input
                                                v-model="dni"
                                                v-bind="dniAttrs"
                                                type="text"
                                                placeholder="DNI / CE / Pasaporte"
                                                :class="{
                                                    'input-error': errors.dni,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <p class="errorMsg" v-if="errors.dni">
                                        {{ errors.dni }}
                                    </p>
                                </div>

                                <!-- Departamento, Provincia, Distrito -->
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="selectCustom">
                                            <select
                                                v-model="selectedDepartment"
                                            >
                                                <option value="">
                                                    Departamento
                                                </option>
                                                <option
                                                    v-for="(
                                                        prov, dep
                                                    ) in districts"
                                                    :key="dep"
                                                    :value="dep"
                                                >
                                                    {{ dep }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="selectCustom">
                                            <select v-model="selectedProvince">
                                                <option value="">
                                                    Provincia
                                                </option>
                                                <option
                                                    v-for="prov in provinces"
                                                    :key="prov"
                                                    :value="prov"
                                                >
                                                    {{ prov }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="selectCustom">
                                            <select
                                                v-model="delivery_district_id"
                                                v-bind="
                                                    delivery_district_idAttrs
                                                "
                                                :class="{
                                                    'input-error':
                                                        errors.delivery_district_id,
                                                }"
                                            >
                                                <option :value="null">
                                                    Distrito
                                                </option>
                                                <option
                                                    v-for="dist in districtsList"
                                                    :key="dist.id"
                                                    :value="dist.id"
                                                >
                                                    {{ dist.district }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <p
                                        class="errorMsg"
                                        v-if="errors.delivery_district_id"
                                    >
                                        {{ errors.delivery_district_id }}
                                    </p>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="inputContainer">
                                            <label>Dirección</label>
                                            <input
                                                v-model="shipping_address"
                                                v-bind="shipping_addressAttrs"
                                                type="text"
                                                :class="{
                                                    'input-error':
                                                        errors.shipping_address,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <p
                                        class="errorMsg"
                                        v-if="errors.shipping_address"
                                    >
                                        {{ errors.shipping_address }}
                                    </p>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="inputContainer">
                                            <input
                                                v-model="delivery_reference"
                                                v-bind="delivery_referenceAttrs"
                                                type="text"
                                                placeholder="Dpto. / Casa / Referencia"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="inputContainer">
                                            <input
                                                v-model="guest_phone"
                                                v-bind="guest_phoneAttrs"
                                                type="tel"
                                                placeholder="Celular"
                                                :class="{
                                                    'input-error':
                                                        errors.guest_phone,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <p
                                        class="errorMsg"
                                        v-if="errors.guest_phone"
                                    >
                                        {{ errors.guest_phone }}
                                    </p>
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
                                                    Acepto los
                                                    <a href="#"
                                                        >términos y
                                                        condiciones</a
                                                    >
                                                </p>
                                            </label>
                                        </div>
                                    </div>
                                    <p
                                        class="errorMsg"
                                        v-if="errors.accepted_terms"
                                    >
                                        {{ errors.accepted_terms }}
                                    </p>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="checkboxCustom">
                                            <label>
                                                <input
                                                    type="checkbox"
                                                    v-model="accepted_privacy"
                                                    v-bind="
                                                        accepted_privacyAttrs
                                                    "
                                                />
                                                <span class="checkmark"></span>
                                                <p>
                                                    Acepto la
                                                    <a href="#"
                                                        >política de
                                                        privacidad</a
                                                    >
                                                </p>
                                            </label>
                                        </div>
                                    </div>
                                    <p
                                        class="errorMsg"
                                        v-if="errors.accepted_privacy"
                                    >
                                        {{ errors.accepted_privacy }}
                                    </p>
                                </div>
                                <!-- RESUMEN -->
                                <div class="validateRow separate">
                                    <div class="rowForm">
                                        <div class="montoContainer">
                                            <span>Subtotal</span>
                                            <span class="font-medium"
                                                >S/
                                                {{
                                                    displaySubtotal.toFixed(2)
                                                }}</span
                                            >
                                        </div>
                                        <div class="montoContainer">
                                            <span>Envío</span>
                                            <span>
                                                {{
                                                    displayDelivery === 0
                                                        ? "Gratis"
                                                        : "S/ " +
                                                          displayDelivery.toFixed(
                                                              2,
                                                          )
                                                }}
                                            </span>
                                        </div>
                                        <div class="montoContainer big">
                                            <span>Total</span>
                                            <span
                                                >S/
                                                {{
                                                    displayTotal.toFixed(2)
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                                <!-- Boton -->
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <button
                                            type="submit"
                                            :disabled="inertiaForm.processing"
                                            class="btn btnForm"
                                        >
                                            <span>{{
                                                inertiaForm.processing
                                                    ? "Procesando..."
                                                    : "COMPRAR"
                                            }}</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="validateRow">
                                    <div class="rowForm">
                                        <div class="iconTarjetas">
                                            <div
                                                v-for="(
                                                    tarjeta, index
                                                ) in tarjetas"
                                                :key="index"
                                            >
                                                <img :src="tarjeta.imagen" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
