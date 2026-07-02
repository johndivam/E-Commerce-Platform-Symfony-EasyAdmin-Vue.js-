<template>
  <div class="max-w-5xl mx-auto px-4 py-8">
    <RouterLink
      to="/products"
      class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6"
    >
      ← Back to products
    </RouterLink>

    <div v-if="isLoading" class="flex justify-center py-24">
      <div class="h-8 w-8 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3">
      {{ error }}
    </div>

    <div v-else-if="product" class="grid grid-cols-1 md:grid-cols-2 gap-10">
      <!-- Image -->
      <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">
        <img
          v-if="product.imageUrl"
          :src="product.imageUrl"
          :alt="product.name"
          class="w-full h-full object-cover"
        />
        <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
          No image
        </div>
      </div>

      <!-- Details -->
      <div>
        <p v-if="product.brand" class="text-sm font-medium text-indigo-600 mb-1">
          {{ product.brand.name }}
        </p>
        <h1 class="text-2xl font-semibold text-gray-900">{{ product.name }}</h1>

        <p v-if="product.category" class="text-sm text-gray-500 mt-1">
          {{ product.category.name }}
        </p>

        <div class="flex items-baseline gap-3 mt-4">
          <span class="text-2xl font-bold text-gray-900">
            ${{ Number(product.price).toFixed(2) }}
          </span>
          <span
            v-if="product.oldPrice"
            class="text-base text-gray-400 line-through"
          >
            ${{ Number(product.oldPrice).toFixed(2) }}
          </span>
        </div>

        <p
          v-if="product.stock > 0"
          class="text-sm text-green-600 font-medium mt-2"
        >
          In stock ({{ product.stock }} available)
        </p>
        <p v-else class="text-sm text-red-600 font-medium mt-2">
          Out of stock
        </p>

        <p v-if="product.shortDescription" class="text-gray-600 mt-4">
          {{ product.shortDescription }}
        </p>

        <button
          :disabled="product.stock <= 0 || isAdding"
          @click="handleAddToCart"
          class="mt-6 w-full md:w-auto px-8 bg-indigo-600 text-white text-sm font-medium py-3 rounded-lg hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
        >
          {{ isAdding ? 'Adding…' : 'Add to cart' }}
        </button>

        <div v-if="product.description" class="mt-8 pt-6 border-t border-gray-200">
          <h2 class="text-sm font-semibold text-gray-900 mb-2">Description</h2>
          <p class="text-sm text-gray-600 whitespace-pre-line">{{ product.description }}</p>
        </div>

        <p v-if="product.sku" class="text-xs text-gray-400 mt-6">
          SKU: {{ product.sku }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../axios.js';
import { useCart } from '../composables/useCart.js';

const route = useRoute();

const product = ref(null);
const isLoading = ref(true);
const error = ref(null);

const { addToCart } = useCart();
const isAdding = ref(false);

async function fetchProduct(slug) {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await axios.get(`/products/${slug}`);
    product.value = response.data;
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'Product not found.';
    } else {
      error.value = err.response?.data?.message || 'Failed to load product.';
    }
  } finally {
    isLoading.value = false;
  }
}

async function handleAddToCart() {
  isAdding.value = true;
  try {
    await addToCart(product.value, 1);
  } finally {
    isAdding.value = false;
  }
}

// Re-fetch if the user navigates from one product page directly to another
watch(
  () => route.params.slug,
  (newSlug) => {
    if (newSlug) fetchProduct(newSlug);
  }
);

onMounted(() => fetchProduct(route.params.slug));
</script>