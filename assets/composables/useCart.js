import { reactive, computed, readonly } from 'vue';
import cartService from '../services/cartService.js';
import authService from '../services/authService.js';

const STORAGE_KEY = 'guest_cart';

const state = reactive({
  items: [], // { productId, slug, name, imageUrl, price, quantity, lineTotal }
  isLoading: false,
});

function readGuestCart() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
  } catch {
    return [];
  }
}

function writeGuestCart(items) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
}

function applyServerCart(data) {
  state.items = data.items;
}

async function loadCart() {
  if (authService.isLoggedIn()) {
    state.isLoading = true;
    try {
      const { data } = await cartService.getCart();
      applyServerCart(data);
    } finally {
      state.isLoading = false;
    }
  } else {
    state.items = readGuestCart();
  }
}

function upsertGuestItem(product, quantity) {
  const items = readGuestCart();
  const existing = items.find((i) => i.productId === product.id);
  if (existing) {
    existing.quantity += quantity;
    existing.lineTotal = existing.quantity * existing.price;
  } else {
    items.push({
      productId: product.id,
      slug: product.slug,
      name: product.name,
      imageUrl: product.imageUrl,
      price: product.price,
      quantity,
      lineTotal: product.price * quantity,
    });
  }

  writeGuestCart(items);
  state.items = items;
}

async function addToCart(product, quantity = 1) {
  if (authService.isLoggedIn()) {
    const { data } = await cartService.addItem(product.id, quantity);
    applyServerCart(data);
  } else {
    upsertGuestItem(product, quantity);
  }
}

async function updateQuantity(productId, quantity) {
  if (authService.isLoggedIn()) {
    const { data } = await cartService.updateItem(productId, quantity);
    applyServerCart(data);
  } else {
    let items = readGuestCart();
    if (quantity <= 0) {
      items = items.filter((i) => i.productId !== productId);
    } else {
      const item = items.find((i) => i.productId === productId);
      if (item) {
        item.quantity = quantity;
        item.lineTotal = item.quantity * item.price;
      }
    }
    writeGuestCart(items);
    state.items = items;
  }
}

async function removeFromCart(productId) {
  await updateQuantity(productId, 0);
}

// Called once, right after a successful login (from authService / LoginController flow)
async function mergeGuestCartIntoAccount() {
  const guestItems = readGuestCart();
  if (guestItems.length > 0) {
    const { data } = await cartService.mergeCart(
      guestItems.map((i) => ({ productId: i.productId, quantity: i.quantity }))
    );
    applyServerCart(data);
    localStorage.removeItem(STORAGE_KEY);
  } else {
    await loadCart();
  }
}

const totalItems = computed(() => state.items.reduce((sum, i) => sum + i.quantity, 0));
const totalPrice = computed(() => state.items.reduce((sum, i) => sum + Number(i.lineTotal), 0));

export function useCart() {
  return {
    items: readonly(state.items),
    isLoading: readonly(state).isLoading,
    totalItems,
    totalPrice,
    loadCart,
    addToCart,
    updateQuantity,
    removeFromCart,
    mergeGuestCartIntoAccount,
  };
}