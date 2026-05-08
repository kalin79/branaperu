<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm, Link, router } from "@inertiajs/vue3";
import { ref, computed, watch, watchEffect } from "vue";

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
const form = useForm({
    guest_name: "", // Nombres
    guest_last_name: "", // ← Agregado
    guest_email: "",
    guest_phone: "",
    delivery_full_name: "", // Recomendado: nombre completo para entrega
    delivery_district_id: null,
    shipping_address: "",
    delivery_reference: "",
    dni: "",
    accepted_terms: false,
    accepted_privacy: false,
    accepted_marketing: false,
});

// ====================== CÁLCULOS ======================
const subtotal = computed(() => {
    const cartItems = props.cart || {};
    return Object.values(cartItems).reduce((sum, item) => {
        return sum + (Number(item?.price) || 0) * (Number(item?.quantity) || 0);
    }, 0);
});

const deliveryCost = computed(() => {
    const districtId = form.delivery_district_id; // ← quita .data

    if (!districtId) {
        return Number(props.defaultDeliveryCost) || 0;
    }

    const allDistricts = Object.values(props.districts || {})
        .flatMap((prov) => Object.values(prov))
        .flat();

    const selected = allDistricts.find((d) => d.id === districtId);

    // Si el distrito tiene costo propio > 0 → usarlo

    if (selected && Number(selected.delivery_cost) > 0) {
        return Number(selected.delivery_cost);
    }

    // Sino → costo global

    return Number(props.defaultDeliveryCost) || 0;
});
// ====================== VALORES PARA MOSTRAR ======================
const displaySubtotal = ref(0);
const displayDelivery = ref(0);
const displayTotal = ref(0);
watchEffect(() => {
    displaySubtotal.value = Number(subtotal.value || 0);
    displayDelivery.value = Number(deliveryCost.value || 0);
    displayTotal.value = displaySubtotal.value + displayDelivery.value;

    console.log("🔄 Display:", displaySubtotal.value, displayTotal.value);
});

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

// ====================== FUNCIONES DE CANTIDAD ======================
const updateQuantity = (productId, newQuantity) => {
    if (newQuantity < 1) return;

    router.post(
        "/cart/update",
        {
            product_id: productId,
            quantity: newQuantity,
        },
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

watch(selectedDepartment, () => {
    selectedProvince.value = "";
    form.delivery_district_id = null; // ← quita .data
});

const removeProduct = (productId) => {
    if (!confirm("¿Estás seguro de eliminar este producto?")) {
        return;
    }

    router.post(
        "/cart/remove",
        {
            product_id: productId,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                console.log(`Producto ${productId} eliminado`);
            },
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
                            <h2>Por favor, coloca tus datos...</h2>
                            <Link href="/">Login</Link>
                        </div>
                        <div class="formContainer">
                            <form
                                @submit.prevent="form.post('/checkout/process')"
                            >
                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="form.guest_email"
                                            type="text"
                                            required
                                            placeholder="Ingresa correo electrónico"
                                        />
                                    </div>
                                </div>
                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="form.guest_name"
                                            type="text"
                                            required
                                            placeholder="Nombres"
                                        />
                                    </div>
                                </div>
                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="form.guest_last_name"
                                            type="text"
                                            required
                                            placeholder="Apellidos"
                                        />
                                    </div>
                                </div>

                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="form.dni"
                                            type="text"
                                            required
                                            placeholder="DNI /CE /Pasaporte"
                                        />
                                    </div>
                                </div>

                                <!-- Departamento, Provincia, Distrito -->
                                <div class="rowForm">
                                    <div class="selectCustom">
                                        <select
                                            v-model="selectedDepartment"
                                            class="w-full border rounded-lg px-4 py-3"
                                        >
                                            <option value="">
                                                Departamento
                                            </option>
                                            <option
                                                v-for="(prov, dep) in districts"
                                                :key="dep"
                                                :value="dep"
                                            >
                                                {{ dep }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="rowForm">
                                    <div class="selectCustom">
                                        <select
                                            v-model="selectedProvince"
                                            class="w-full border rounded-lg px-4 py-3"
                                        >
                                            <option value="">Provincia</option>
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
                                <div class="rowForm">
                                    <div class="selectCustom">
                                        <select
                                            v-model="form.delivery_district_id"
                                            class="w-full border rounded-lg px-4 py-3"
                                        >
                                            <option value="">Distrito</option>
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

                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <label>Dirección</label>
                                        <input
                                            v-model="form.shipping_address"
                                            type="text"
                                            required
                                            class="w-full border rounded-lg px-4 py-3"
                                        />
                                    </div>
                                </div>

                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="form.delivery_reference"
                                            type="text"
                                            placeholder="Dpto. / Casa / Referencia"
                                        />
                                    </div>
                                </div>

                                <div class="rowForm">
                                    <div class="inputContainer">
                                        <input
                                            v-model="form.guest_phone"
                                            type="tel"
                                            required
                                            placeholder="Celular"
                                        />
                                    </div>
                                </div>

                                <div class="rowForm">
                                    <div class="checkboxCustom">
                                        <label>
                                            <input
                                                type="checkbox"
                                                v-model="form.accepted_terms"
                                            />
                                            <span class="checkmark"></span>
                                            <p>
                                                Acepto los
                                                <a
                                                    href="#"
                                                    class="text-green-600"
                                                    >términos y condiciones</a
                                                >
                                            </p>
                                        </label>
                                    </div>
                                </div>

                                <div class="rowForm">
                                    <div class="checkboxCustom">
                                        <label>
                                            <input
                                                type="checkbox"
                                                v-model="form.accepted_privacy"
                                            />
                                            <span class="checkmark"></span>
                                            <p>
                                                Acepto la
                                                <a
                                                    href="#"
                                                    class="text-green-600"
                                                    >política de privacidad</a
                                                >
                                            </p>
                                        </label>
                                    </div>
                                </div>

                                <!-- RESUMEN -->
                                <div class="rowForm separate">
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
                                                      displayDelivery.toFixed(2)
                                            }}
                                        </span>
                                    </div>
                                    <div class="montoContainer big">
                                        <span>Total</span>
                                        <span
                                            >S/
                                            {{ displayTotal.toFixed(2) }}</span
                                        >
                                    </div>
                                </div>
                                <!-- Boton -->
                                <div class="rowForm">
                                    <button
                                        type="submit"
                                        :disabled="
                                            form.processing ||
                                            !form.accepted_terms ||
                                            !form.accepted_privacy
                                        "
                                        class="btn btnForm"
                                    >
                                        <span
                                            >{{
                                                form.processing
                                                    ? "Procesando..."
                                                    : "COMPRAR"
                                            }}
                                        </span>
                                    </button>
                                </div>

                                <div class="rowForm">
                                    <div class="iconTarjetas">
                                        <div
                                            v-for="(tarjeta, index) in tarjetas"
                                            :key="index"
                                        >
                                            <img :src="tarjeta.imagen" />
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
