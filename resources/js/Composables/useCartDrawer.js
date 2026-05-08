import { ref } from "vue";

const isOpen = ref(false);
const currentCart = ref({});
const currentTotal = ref(0);

export function useCartDrawer() {
    const openDrawer = () => {
        isOpen.value = true;
    };

    const closeDrawer = () => {
        isOpen.value = false;
    };

    const setCartData = (cart, total = 0) => {
        currentCart.value = { ...(cart || {}) };
        currentTotal.value = total;
    };

    return {
        isOpen,
        cart: currentCart,
        total: currentTotal,
        openDrawer,
        closeDrawer,
        setCartData,
    };
}
