<script setup>
import CheckoutLayout from "@/Layouts/CheckoutLayout.vue";
import { ref, computed, onMounted, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

const iconDelivery = "/images/delivery.svg";
const iconTienda = "/images/tienda.svg";
const props = defineProps({
    CheckoutLayout: null,
    order: Object,
    items: Array,
    districts: Object, // { departamento: { provincia: [districts...] } }
    locals: Array,
    mpPublicKey: String,
    defaultDeliveryCost: { type: [Number, String], default: 0 },
});

// ===== Estado del formulario =====
const form = ref({
    guest_name: props.order.guest_name || "",
    guest_last_name: props.order.guest_last_name || "",
    guest_email: props.order.guest_email || "",
    guest_phone: props.order.guest_phone || "",
    dni: props.order.dni || "",

    delivery_method: props.order.delivery_method || "delivery",

    // Delivery
    delivery_district_id: props.order.delivery_district_id || null,
    shipping_address: props.order.shipping_address || "",
    delivery_reference: props.order.delivery_reference || "",

    // Pickup
    pickup_local_id: props.order.pickup_local_id || null,

    // Documento
    document_type: props.order.document_type || "boleta",
    billing_ruc: props.order.billing_ruc || "",
    billing_business_name: props.order.billing_business_name || "",
    billing_address: props.order.billing_address || "",

    accepted_marketing: !!props.order.accepted_marketing,
});

// ===== Totales reactivos (se actualizan tras aplicar cupón) =====
const totals = ref({
    subtotal: props.order.subtotal,
    discount_amount: props.order.discount_amount,
    delivery_cost: props.order.delivery_cost,
    final_total: props.order.final_total,
    coupon_code: props.order.coupon_code,
    coupon_name: props.order.coupon_name,
    discount_rule_name: props.order.discount_rule_name,
    discount_rule_percent: props.order.discount_rule_percent,
});

// ===== Estado UI =====
const couponInput = ref("");
const couponLoading = ref(false);
const couponError = ref(null);
const couponSuccess = ref(null);

const errors = ref({});
const globalError = ref(null);
const submitting = ref(false);

// ===== Cascada de departamento → provincia → distrito =====
const selectedDept = ref("");
const selectedProvince = ref("");

const departments = computed(() => Object.keys(props.districts || {}));

const provinces = computed(() => {
    if (!selectedDept.value) return [];
    return Object.keys(props.districts[selectedDept.value] || {});
});

const districtsForProvince = computed(() => {
    if (!selectedDept.value || !selectedProvince.value) return [];
    return props.districts[selectedDept.value]?.[selectedProvince.value] || [];
});

// Si la orden ya tiene un distrito seleccionado, prellenar la cascada
onMounted(() => {
    if (form.value.delivery_district_id) {
        for (const [dept, provs] of Object.entries(props.districts || {})) {
            for (const [prov, dists] of Object.entries(provs)) {
                const found = dists.find(
                    (d) => d.id === form.value.delivery_district_id,
                );
                if (found) {
                    selectedDept.value = dept;
                    selectedProvince.value = prov;
                    return;
                }
            }
        }
    }
});

// ===== Computeds =====
const isDelivery = computed(() => form.value.delivery_method === "delivery");
const isPickup = computed(() => form.value.delivery_method === "pickup");
const isFactura = computed(() => form.value.document_type === "factura");

const fmt = (v) => Number(v || 0).toFixed(2);

// Busca un distrito por id en TODA la estructura (no solo la provincia actual)
const findDistrictById = (id) => {
    if (!id) return null;
    for (const provs of Object.values(props.districts || {})) {
        for (const dists of Object.values(provs)) {
            const found = dists.find((d) => d.id === id);
            if (found) return found;
        }
    }
    return null;
};

// Recalcula final_total localmente usando los valores actuales de totals
const recalcFinalTotal = () => {
    const subtotal = Number(totals.value.subtotal || 0);
    const discount = Number(totals.value.discount_amount || 0);
    const delivery = Number(totals.value.delivery_cost || 0);
    totals.value.final_total = Math.max(0, subtotal - discount + delivery);
};

// ===== Cupones =====
const applyCoupon = async () => {
    if (!couponInput.value.trim()) return;
    couponLoading.value = true;
    couponError.value = null;
    couponSuccess.value = null;

    try {
        const { data } = await axios.post(
            `/checkout/${props.order.order_number}/apply-coupon`,
            { code: couponInput.value },
        );

        if (data.success) {
            updateTotals(data.order);
            couponSuccess.value = "Cupón aplicado correctamente";
            couponInput.value = "";
        } else {
            couponError.value = data.message;
        }
    } catch (e) {
        couponError.value =
            e.response?.data?.message || "Error aplicando el cupón";
    } finally {
        couponLoading.value = false;
    }
};

const removeCoupon = async () => {
    couponLoading.value = true;
    couponError.value = null;
    try {
        const { data } = await axios.delete(
            `/checkout/${props.order.order_number}/coupon`,
        );
        if (data.success) {
            updateTotals(data.order);
            couponSuccess.value = null;
            couponError.value = null;
        } else {
            couponError.value = data.message || "No se pudo quitar el cupón";
        }
    } catch (e) {
        console.error(
            "Error removeCoupon:",
            e.response?.status,
            e.response?.data,
        );
        couponError.value =
            e.response?.data?.message || "Error al quitar el cupón";
    } finally {
        couponLoading.value = false;
    }
};

const updateTotals = (orderData) => {
    totals.value = {
        subtotal: orderData.subtotal,
        discount_amount: orderData.discount_amount,
        delivery_cost: orderData.delivery_cost,
        final_total: orderData.final_total,
        coupon_code: orderData.coupon_code,
        coupon_name: orderData.coupon_name,
        discount_rule_name: orderData.discount_rule_name,
        discount_rule_percent: orderData.discount_rule_percent,
    };
};

// Devuelve el costo efectivo: si el distrito tiene 0 o null, usa el global
const getEffectiveDeliveryCost = (district) => {
    if (!district) return 0;
    const cost = Number(district.delivery_cost || 0);
    return cost > 0 ? cost : Number(props.defaultDeliveryCost || 0);
};

// Refresca delivery_cost cuando cambia el distrito
watch(
    () => form.value.delivery_district_id,
    (newId) => {
        if (!isDelivery.value) return;
        const district = findDistrictById(newId);
        totals.value.delivery_cost = getEffectiveDeliveryCost(district);
        recalcFinalTotal();
    },
);

// Refresca delivery_cost cuando cambia el método de entrega
watch(
    () => form.value.delivery_method,
    (method) => {
        if (method === "pickup") {
            totals.value.delivery_cost = 0;
        } else {
            const district = findDistrictById(form.value.delivery_district_id);
            totals.value.delivery_cost = getEffectiveDeliveryCost(district);
        }
        recalcFinalTotal();
    },
);

// ===== Pagar =====
const scrollToFirstError = () => {
    setTimeout(() => {
        const firstErr = document.querySelector(".has-error");
        firstErr?.scrollIntoView({ behavior: "smooth", block: "center" });
    }, 100);
};

// Validación rápida en el front antes de pegar al backend
const validateBeforeSubmit = () => {
    const localErrors = {};

    if (isPickup.value && !form.value.pickup_local_id) {
        localErrors.pickup_local_id = [
            "Selecciona la tienda donde recogerás tu pedido.",
        ];
    }

    if (isDelivery.value && !form.value.delivery_district_id) {
        localErrors.delivery_district_id = [
            "Selecciona el distrito de entrega.",
        ];
    }

    if (Object.keys(localErrors).length > 0) {
        errors.value = localErrors;
        globalError.value = "Por favor completa los campos requeridos.";
        return false;
    }
    return true;
};

const handlePayClick = async () => {
    errors.value = {};
    globalError.value = null;

    if (!validateBeforeSubmit()) {
        scrollToFirstError();
        return;
    }

    submitting.value = true;

    try {
        // 1. Guardar info del cliente (con revalidación de cupón en el backend)
        const updateRes = await axios.post(
            `/checkout/${props.order.order_number}/update-info`,
            form.value,
        );
        if (updateRes.data.order) updateTotals(updateRes.data.order);

        // Si el cupón se quitó automáticamente, avisar y abortar
        if (updateRes.data.coupon_removed) {
            couponError.value =
                updateRes.data.coupon_message ||
                "El cupón fue removido porque ya no aplica con los nuevos datos.";
            couponInput.value = "";
            return; // no avanzamos a crear la preferencia
        }

        // 2. Crear preferencia en MP
        const prefRes = await axios.post(
            `/checkout/${props.order.order_number}/create-preference`,
        );

        // 3. Redirigir a MP
        if (prefRes.data.init_point) {
            window.location.href = prefRes.data.init_point;
        }
    } catch (e) {
        // Si createPreference detecta cupón inválido, también mostrar aviso
        if (e.response?.status === 422 && e.response.data?.coupon_removed) {
            couponError.value =
                e.response.data.error || "El cupón aplicado ya no es válido.";
            if (e.response.data.order) updateTotals(e.response.data.order);
            couponInput.value = "";
            return;
        }
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
            globalError.value =
                e.response.data.error ||
                "Por favor revisa los campos marcados.";
        } else {
            globalError.value =
                e.response?.data?.error || "Error procesando el pago.";
        }
        scrollToFirstError();
    } finally {
        submitting.value = false;
    }
};

const goEditCart = () => router.visit("/cart");
</script>

<template>
    <CheckoutLayout title="Finalizar Compra">
        <div class="paymentPageContainer">
            <div class="container-fluid2">
                <div class="layoudContainer">
                    <!-- ============= COLUMNA IZQUIERDA: FORMULARIO ============= -->
                    <div class="leftContainer">
                        <!-- Contacto -->
                        <section class="contactoContainer">
                            <h2>Contacto</h2>
                            <div class="topContacto">
                                <div class="inputContainer">
                                    <input
                                        v-model="form.guest_email"
                                        type="email"
                                        placeholder="Correo electrónico"
                                        class="emailContainer"
                                        :class="{
                                            'has-error border-red-500':
                                                errors.guest_email,
                                        }"
                                    />
                                </div>
                                <p v-if="errors.guest_email" class="errorForm">
                                    {{ errors.guest_email[0] }}
                                </p>
                            </div>
                            <div class="bottonContacto">
                                <div class="checkboxCustom">
                                    <label>
                                        <input
                                            type="checkbox"
                                            v-model="form.accepted_marketing"
                                        />
                                        <span class="checkmark"></span>
                                        <p>
                                            Envíame un correo electrónico con
                                            noticias y ofertas.
                                        </p>
                                    </label>
                                </div>
                            </div>
                        </section>

                        <!-- Entrega: delivery vs pickup -->
                        <section class="formContacto">
                            <h2>Entrega</h2>
                            <div class="tabEntregaContainer">
                                <label
                                    class="tabBox"
                                    :class="isDelivery ? 'active' : ''"
                                >
                                    <div>
                                        <label class="customRadio">
                                            <input
                                                type="radio"
                                                v-model="form.delivery_method"
                                                value="delivery"
                                            />
                                            <span class="checkmark"></span>
                                            <p>Envío delivery</p>
                                        </label>
                                    </div>
                                    <div class="iconContainer">
                                        <img
                                            :src="iconDelivery"
                                            alt="Envio de Delivery"
                                        />
                                    </div>
                                </label>
                                <label
                                    class="tabBox"
                                    :class="isPickup ? 'active' : ''"
                                >
                                    <div>
                                        <label class="customRadio">
                                            <input
                                                type="radio"
                                                v-model="form.delivery_method"
                                                value="pickup"
                                                class="text-emerald-600"
                                            />
                                            <span class="checkmark"></span>
                                            <p>Retiro en tienda</p>
                                        </label>
                                    </div>
                                    <div class="iconContainer">
                                        <img
                                            :src="iconTienda"
                                            alt="Envio de Delivery"
                                        />
                                    </div>
                                </label>
                            </div>
                        </section>

                        <!-- Si es PICKUP: lista de tiendas -->
                        <section v-if="isPickup" class="tiendasContainer">
                            <h2>Sucursales en tienda</h2>
                            <p>Seleccione la tienda de su preferencia.</p>
                            <div class="listadoTiendasContainer">
                                <div
                                    v-for="local in locals"
                                    :key="local.id"
                                    class="itemTienda"
                                    :class="
                                        form.pickup_local_id === local.id
                                            ? 'active'
                                            : ''
                                    "
                                >
                                    <label class="customRadio">
                                        <input
                                            type="radio"
                                            v-model.number="
                                                form.pickup_local_id
                                            "
                                            :value="local.id"
                                            class="mt-1 text-emerald-600"
                                        />
                                        <span class="checkmark"></span>
                                        <div class="addresLeft">
                                            <p
                                                class="bold"
                                                v-html="local.title"
                                            ></p>
                                            <p v-html="local.address"></p>
                                        </div>
                                    </label>

                                    <div class="addressBox">
                                        <div class="addresRight">
                                            <h4
                                                v-if="local.label"
                                                v-html="local.label"
                                            ></h4>
                                            <p
                                                v-if="local.short_description"
                                                v-html="local.short_description"
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-if="errors.pickup_local_id" class="has-error">
                                {{ errors.pickup_local_id[0] }}
                            </p>
                        </section>

                        <!-- Si es DELIVERY: dirección -->
                        <section v-if="isDelivery" class="deliveryContainer">
                            <h2>Dirección de envío</h2>

                            <div class="selectoresContainer">
                                <div class="rowForm">
                                    <div class="selectCustom">
                                        <select
                                            v-model="selectedDept"
                                            @change="
                                                selectedProvince = '';
                                                form.delivery_district_id =
                                                    null;
                                            "
                                        >
                                            <option value="">
                                                Departamento
                                            </option>
                                            <option
                                                v-for="d in departments"
                                                :key="d"
                                                :value="d"
                                            >
                                                {{ d }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="rowForm">
                                    <div class="selectCustom">
                                        <select
                                            v-model="selectedProvince"
                                            @change="
                                                form.delivery_district_id = null
                                            "
                                            :disabled="!selectedDept"
                                        >
                                            <option value="">Provincia</option>
                                            <option
                                                v-for="p in provinces"
                                                :key="p"
                                                :value="p"
                                            >
                                                {{ p }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="rowForm">
                                    <div class="selectCustom">
                                        <select
                                            v-model.number="
                                                form.delivery_district_id
                                            "
                                            :disabled="!selectedProvince"
                                            :class="{
                                                'has-error':
                                                    errors.delivery_district_id,
                                            }"
                                        >
                                            <option :value="null">
                                                Distrito
                                            </option>
                                            <option
                                                v-for="d in districtsForProvince"
                                                :key="d.id"
                                                :value="d.id"
                                            >
                                                {{ d.district }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="rowInputForm">
                                <input
                                    v-model="form.shipping_address"
                                    type="text"
                                    class="w-full border rounded-lg px-4 py-2.5"
                                    :class="{
                                        'has-error border-red-500':
                                            errors.shipping_address,
                                    }"
                                />
                                <label>Dirección</label>
                            </div>
                            <div class="rowInputForm">
                                <input
                                    v-model="form.delivery_reference"
                                    type="text"
                                    class="w-full border rounded-lg px-4 py-2.5"
                                />
                                <label
                                    >Referencia (opcional): casa, departamento,
                                    etc.</label
                                >
                            </div>
                        </section>

                        <!-- Datos personales -->
                        <section class="myDataContainer">
                            <h2>Tus datos</h2>
                            <div class="listDataBox">
                                <div class="bloqueRow">
                                    <div class="rowInputForm">
                                        <input
                                            v-model="form.guest_name"
                                            type="text"
                                            :class="{
                                                'has-error': errors.guest_name,
                                            }"
                                        />
                                        <label> Nombres </label>
                                    </div>
                                    <p v-if="errors.guest_name">
                                        {{ errors.guest_name[0] }}
                                    </p>
                                </div>
                                <div class="bloqueRow">
                                    <div class="rowInputForm">
                                        <input
                                            v-model="form.guest_last_name"
                                            type="text"
                                            :class="{
                                                'has-error':
                                                    errors.guest_last_name,
                                            }"
                                        />
                                        <label> Apellidos </label>
                                    </div>
                                    <p
                                        v-if="errors.guest_last_name"
                                        class="text-xs text-red-500 mt-1"
                                    >
                                        {{ errors.guest_last_name[0] }}
                                    </p>
                                </div>
                                <div class="bloqueRow">
                                    <div class="rowInputForm">
                                        <input
                                            v-model="form.dni"
                                            type="text"
                                            :class="{
                                                'has-error': errors.dni,
                                            }"
                                        />
                                        <label>DNI / CE </label>
                                    </div>
                                </div>
                                <div class="bloqueRow">
                                    <div class="rowInputForm">
                                        <input
                                            v-model="form.guest_phone"
                                            type="tel"
                                            placeholder="Celular"
                                            :class="{
                                                'has-error border-red-500':
                                                    errors.guest_phone,
                                            }"
                                        />
                                        <label>Celular</label>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Documento: boleta o factura -->
                        <section class="facturaContainer">
                            <h2>Documento de pago</h2>
                            <div class="tabTipoDocumentoContainer">
                                <label
                                    class="customRadio"
                                    :class="!isFactura ? 'active' : ''"
                                >
                                    <input
                                        type="radio"
                                        v-model="form.document_type"
                                        value="boleta"
                                    />
                                    <span class="checkmark"></span>
                                    <p>Boleta</p>
                                </label>

                                <label
                                    class="customRadio"
                                    :class="!isFactura ? 'active' : ''"
                                >
                                    <input
                                        type="radio"
                                        v-model="form.document_type"
                                        value="factura"
                                        class="text-emerald-600"
                                    />
                                    <span class="checkmark"></span>
                                    <p>Factura</p>
                                </label>
                            </div>

                            <div v-if="isFactura" class="tabTipoDocContentBox">
                                <div class="bloqueRow">
                                    <div class="rowInputForm">
                                        <input
                                            v-model="form.billing_ruc"
                                            type="text"
                                            maxlength="11"
                                            :class="{
                                                'has-error': errors.billing_ruc,
                                            }"
                                        />
                                        <label>RUC (11 dígitos)</label>
                                    </div>
                                    <p
                                        v-if="errors.billing_ruc"
                                        class="has-errorInfo"
                                    >
                                        {{ errors.billing_ruc[0] }}
                                    </p>
                                </div>
                                <div class="bloqueRow">
                                    <div class="rowInputForm">
                                        <input
                                            v-model="form.billing_business_name"
                                            type="text"
                                            :class="{
                                                'has-error':
                                                    errors.billing_business_name,
                                            }"
                                        />
                                        <label>Razón social</label>
                                    </div>
                                </div>
                                <div class="bloqueRow">
                                    <div class="rowInputForm">
                                        <input
                                            v-model="form.billing_address"
                                            type="text"
                                            :class="{
                                                'has-error':
                                                    errors.billing_address,
                                            }"
                                        />
                                        <label>Dirección fiscal</label>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Error global y botón pagar -->
                        <div v-if="globalError" class="errorGlobal">
                            {{ globalError }}
                        </div>
                        <div class="btnContainer">
                            <button
                                @click="handlePayClick"
                                :disabled="submitting"
                                class="btn"
                            >
                                {{
                                    submitting ? "Procesando..." : "PAGAR AHORA"
                                }}
                            </button>
                        </div>
                    </div>

                    <!-- ============= COLUMNA DERECHA: RESUMEN ============= -->
                    <div class="rightContainer">
                        <div class="viewOrdenContainer">
                            <!-- Productos -->
                            <div class="productBox">
                                <div
                                    v-for="item in items"
                                    :key="item.id"
                                    class="itemProduct"
                                >
                                    <div class="imgContainer">
                                        <img
                                            :src="`/storage/${item.product_image}`"
                                        />
                                        <span>
                                            {{ item.quantity }}
                                        </span>
                                    </div>
                                    <div class="infoContainer">
                                        <div class="infoLeft">
                                            <h2 v-html="item.product_name"></h2>
                                            <p v-if="item.ml">
                                                {{ item.ml }}
                                            </p>
                                        </div>
                                        <div class="infoRight">
                                            <h2>S/ {{ fmt(item.subtotal) }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cupón -->
                            <div class="cuponContainer">
                                <div
                                    v-if="!totals.coupon_code"
                                    class="boxCupon"
                                >
                                    <div class="rowInputForm">
                                        <input
                                            v-model="couponInput"
                                            type="text"
                                            placeholder="Código de descuento"
                                            class="flex-1 border rounded-lg px-3 py-2 text-sm"
                                            @keyup.enter="applyCoupon"
                                        />
                                    </div>

                                    <div class="btnCupon">
                                        <button
                                            @click="applyCoupon"
                                            :disabled="
                                                couponLoading || !couponInput
                                            "
                                            class="btn"
                                        >
                                            {{
                                                couponLoading
                                                    ? "..."
                                                    : "Aplicar"
                                            }}
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="cuponHabilitadoContainer">
                                    <div>
                                        <p>✓ {{ totals.coupon_code }}</p>
                                    </div>
                                    <div>
                                        <button
                                            @click="removeCoupon"
                                            :disabled="couponLoading"
                                            class="btn"
                                        >
                                            Quitar
                                        </button>
                                    </div>
                                </div>
                                <p v-if="couponError" class="errorMsg">
                                    {{ couponError }}
                                </p>
                            </div>

                            <!-- Totales -->
                            <div class="purchaseCostsContainer">
                                <div class="subTotalBox">
                                    <span>Subtotal</span>
                                    <span>S/ {{ fmt(totals.subtotal) }}</span>
                                </div>

                                <!-- Descuento automático (sin cupón) -->
                                <div
                                    v-if="
                                        totals.discount_rule_name &&
                                        !totals.coupon_code
                                    "
                                    class="descAutoBox"
                                >
                                    <span>
                                        {{ totals.discount_rule_name }}
                                        <span
                                            v-if="totals.discount_rule_percent"
                                            class="text-xs text-emerald-500"
                                        >
                                            ({{
                                                totals.discount_rule_percent
                                            }}%)
                                        </span>
                                    </span>
                                    <span>
                                        -S/
                                        {{ fmt(totals.discount_amount) }}
                                    </span>
                                </div>

                                <!-- Descuento por cupón -->
                                <div
                                    v-if="totals.coupon_code"
                                    class="dscCuponBox"
                                >
                                    <span>Cupón {{ totals.coupon_code }}</span>
                                    <span>
                                        -S/
                                        {{ fmt(totals.discount_amount) }}
                                    </span>
                                </div>
                                <div v-if="isDelivery" class="costoDeliveryBox">
                                    <span>Envío</span>
                                    <div>
                                        <span
                                            v-if="!form.delivery_district_id"
                                            class="text-gray-400"
                                        >
                                            Selecciona un distrito
                                        </span>
                                        <span v-else
                                            >S/
                                            {{
                                                fmt(totals.delivery_cost)
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <div v-else class="retiroTiendaBox">
                                    <span>Envío</span>
                                    <span>GRATIS (retiro en tienda)</span>
                                </div>

                                <div class="montoTotalBox">
                                    <p>
                                        Total <br />
                                        <span
                                            >Incluye S/ 29.14 de impuestos</span
                                        >
                                    </p>
                                    <h2>
                                        <span>PEN</span>
                                        S/ {{ fmt(totals.final_total) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CheckoutLayout>
</template>
