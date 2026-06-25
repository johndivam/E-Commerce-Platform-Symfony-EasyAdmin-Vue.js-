<template>
  <div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Products</h1>

    <!-- Loading state -->
    <div v-if="isLoading" class="flex justify-center py-16">
      <div class="h-8 w-8 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3">
      {{ error }}
    </div>

    <div v-else-if="products.length === 0" class="text-center text-gray-500 py-16">
      No products found.
    </div>

    <!-- Product grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="product in products"
        :key="product.id"
        class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow"
      >
        <div class="aspect-square bg-gray-100">
          <img
            v-if="product.imageUrl"
            :src="product.imageUrl"
            :alt="product.name"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
            No image
          </div>
        </div>

        <div class="p-4">
          <h2 class="font-medium text-gray-900 truncate">{{ product.name }}</h2>
          <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ product.description }}</p>

          <div class="flex items-center justify-between mt-3">
            <span class="font-semibold text-gray-900">${{ Number(product.price).toFixed(2) }}</span>
            <span
              v-if="product.stock <= 0"
              class="text-xs text-red-600 font-medium"
            >
              Out of stock
            </span>
          </div>

          <button
            :disabled="product.stock <= 0"
            class="mt-3 w-full bg-indigo-600 text-white text-sm font-medium py-2 rounded-lg hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
          >
            Add to cart
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../axios.js';

const products = ref([]);
const isLoading = ref(true);
const error = ref(null);

async function fetchProducts() {
  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.get('/products');
    products.value = response.data;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load products.';
  } finally {
    isLoading.value = false;
  }
}

onMounted(fetchProducts);
</script>