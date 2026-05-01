<template>
    <AppLayout :title_meta="title_meta" :description_meta="description_meta">
        <section class="legalPageContainer">
            <HeroComponent type="legal" :slides="banners" />
            <div class="container-fluid">
                <div class="layoutContainer">
                    <div>
                        <h1>POLÍTICA DE ENTREGA - BRANA PERÚ</h1>
                        <p>
                            En Brana Perú trabajamos para que tus productos
                            lleguen de forma segura, clara y oportuna. A
                            continuación, detallamos nuestras condiciones de
                            entrega:
                        </p>
                        <h2>1. TIEMPOS DE ENTREGA</h2>
                        <h3>Lima Metropolitana</h3>
                        <ul>
                            <li>
                                Los pedidos se entregan al courier al día
                                siguiente de realizada la compra.
                            </li>
                            <li>
                                El tiempo estimado de entrega es de 2 días
                                hábiles, por lo que el plazo total es de hasta 3
                                días hábiles.
                            </li>
                            <li>No incluye domingos ni feriados.</li>
                        </ul>
                        <h3>Provincias</h3>
                        <ul>
                            <li>
                                El pedido se entrega al courier al día siguiente
                                de realizada la compra.
                            </li>
                            <li>
                                El tiempo estimado de entrega es de 3 días
                                hábiles, pudiendo variar según la zona y la
                                agencia de transporte.
                            </li>
                        </ul>

                        <h2>2. COSTOS DE ENVÍO</h2>
                        <h3>Lima Metropolitana</h3>

                        <ul>
                            <li>Zona urbana: S/ 9.90</li>
                            <li>Zonas periféricas*: S/ 12.90 – S/. 14.9</li>
                        </ul>
                        <p>*Zonas periféricas incluyen:</p>
                        <p>
                            Villa El Salvador, Lurín, Villa María del Triunfo,
                            Carabayllo, Ancón, Ventanilla, Ate, Santa Anita,
                            Vitarte, Chaclacayo y Cieneguilla, Chosica
                            (Lurigancho), Chorrillos, La Molina, Lurin,
                            Pachacamac, Pucusana, Puente Piedra, Punta Negra,
                            Punta Hermosa, San Bartolo, San Juan de Miraflores,
                            Santa Rosa, Santa Maria del Mar, Santiago de Surco,
                            La Perla, La Punta.
                        </p>
                        <h3>Provincias</h3>
                        <ul>
                            <li>
                                Envío por agencia (ej. Shalom): el costo es
                                asumido por el cliente al momento del recojo.
                            </li>
                            <li>
                                Envío a domicilio: S/ 12.90 – S/. 14.9, sujeto a
                                cobertura del courier.
                            </li>
                        </ul>
                        <h3>Envío gratis</h3>
                        <ul>
                            <li>
                                En compras mayores a S/ 300.00, el envío es
                                gratuito (no aplica para envíos por agencia en
                                provincias).
                            </li>
                        </ul>
                        <h2>3. PEDIDOS DE ALTO VALOR O MAYORISTAS</h2>
                        <ul>
                            <li>
                                El costo de envío no se define por monto, sino
                                por tipo de cliente.
                            </li>
                            <li>
                                En el caso de clientes mayoristas, el envío se
                                cotiza de manera independiente y generalmente se
                                realiza por agencias de transporte (ej.
                                Marvisur).
                            </li>
                            <li>
                                Brana se comunicará previamente para coordinar
                                condiciones logísticas.
                            </li>
                        </ul>
                        <h2>4. INTENTOS DE ENTREGA Y REPROGRAMACIÓN</h2>

                        <ul>
                            <li>
                                Cada pedido cuenta con hasta 2 intentos de
                                entrega.
                            </li>
                            <li>
                                La reprogramación no tiene costo adicional,
                                siempre que no sea por negligencia del cliente
                                (ej. dirección incorrecta o ausencia reiterada).
                            </li>
                            <li>
                                En caso de error en la información
                                proporcionada, el costo de una nueva entrega
                                podrá ser asumido por el cliente.
                            </li>
                        </ul>
                        <h2>5. RECOJO EN TIENDA</h2>
                        <ul>
                            <li>
                                Brana ofrece la opción de recojo en tienda en
                                todos sus locales.
                            </li>
                            <li>
                                El pedido podrá permanecer en tienda por un
                                máximo de 30 días calendario.
                            </li>
                            <li>
                                Pasado este plazo, el pedido será retornado a
                                nuestro almacén.
                            </li>
                        </ul>
                        <h2>6. ANULACIÓN DE COMPRA</h2>
                        <ul>
                            <li>
                                El cliente puede solicitar la anulación de su
                                compra dentro de un plazo máximo de 48 horas
                                desde realizada.
                            </li>
                            <li>
                                No se retendrá el costo de envío, siempre que no
                                exista negligencia por parte del cliente (daño o
                                manipulación indebida del producto).
                            </li>
                        </ul>
                        <h2>7. ATENCIÓN AL CLIENTE</h2>
                        <p>
                            Para cualquier consulta relacionada con tu pedido,
                            puedes comunicarte con nosotros a través de:
                        </p>
                        <ul>
                            <li>WhatsApp: 955128016</li>
                            <li>Correo electrónico: brana.ventas@gmail.com</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import HeroComponent from "@/Components/Hero/Hero.vue";
const banners = [
    {
        titulo: "Alquiler de maquinaria pesada",
        accion: "Ver equipos",
        imagepc: "/images/bt2.webp",
        imagemobile: "/images/bt2.webp",
    },
];
defineOptions({
    layout: null, // ← Agrega esta línea
});
defineProps({
    title_meta: String,
    description_meta: String,
});
</script>
