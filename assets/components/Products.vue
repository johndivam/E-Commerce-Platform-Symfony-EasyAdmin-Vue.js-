<template>
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6 gap-4">
      <h1 class="text-2xl font-semibold text-gray-900">Products</h1>

      <form @submit.prevent="submitSearch" class="flex items-center gap-2 w-full max-w-xs">
        <div class="relative flex-1">
          <input
            v-model="searchInput"
            type="text"
            placeholder="Search products..."
            class="w-full pl-9 pr-9 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
          <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
          </svg>
          <button
            v-if="searchInput"
            type="button"
            @click="clearSearch"
            class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <button
          type="submit"
          class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Search
        </button>
      </form>
    </div>

    <div v-if="isLoading" class="flex justify-center py-16">
      <div class="h-8 w-8 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3">
      {{ error }}
    </div>

    <div v-else-if="products.length === 0" class="text-center text-gray-500 py-16">
      {{ route.query.q ? `No products match "${route.query.q}".` : 'No products found.' }}
    </div>

    <template v-else>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="product in products"
          :key="product.id"
          class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow"
        >
          <RouterLink :to="{ name: 'product-show', params: { slug: product.slug } }">
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
          </RouterLink>
          <div class="p-4">
            <h2 class="font-medium text-gray-900 truncate">{{ product.name }}</h2>
            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ product.description }}</p>
            <div class="flex items-center justify-between mt-3">
              <span class="font-semibold text-gray-900">${{ Number(product.price).toFixed(2) }}</span>
              <span v-if="product.stock <= 0" class="text-xs text-red-600 font-medium">
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

      <nav v-if="totalPages > 1" class="flex items-center justify-center gap-2 mt-10" aria-label="Pagination">
        <button
          :disabled="page <= 1"
          @click="goToPage(page - 1)"
          class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
        >
          Previous
        </button>

        <button
          v-for="p in pageNumbers"
          :key="p"
          @click="goToPage(p)"
          :class="[
            'px-3 py-2 text-sm font-medium rounded-lg min-w-[2.5rem] transition-colors',
            p === page ? 'bg-indigo-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
          ]"
        >
          {{ p }}
        </button>

        <button
          :disabled="page >= totalPages"
          @click="goToPage(page + 1)"
          class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
        >
          Next
        </button>
      </nav>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../axios.js';

const route = useRoute();
const router = useRouter();

const products = ref([]);
const isLoading = ref(true);
const error = ref(null);

const page = ref(parseInt(route.query.page) || 1);
const total = ref(0);
const totalPages = ref(0);

const searchInput = ref(route.query.q || '');

async function fetchProducts() {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/products', {
      params: {
        page: page.value,
        q: route.query.q || undefined,
      },
    });
    products.value = response.data.items;
    total.value = response.data.total;
    totalPages.value = response.data.totalPages;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load products.';
  } finally {
    isLoading.value = false;
  }
}

function submitSearch() {
  const query = { ...route.query, page: 1 }; // reset to page 1 on new search
  const trimmed = searchInput.value.trim();
  if (trimmed) {
    query.q = trimmed;
  } else {
    delete query.q;
  }
  router.push({ query });
}

function clearSearch() {
  searchInput.value = '';
  submitSearch();
}

function goToPage(p) {
  if (p < 1 || p > totalPages.value || p === page.value) return;
  router.push({ query: { ...route.query, page: p } });
}

const pageNumbers = computed(() => {
  const windowSize = 5;
  let start = Math.max(1, page.value - Math.floor(windowSize / 2));
  let end = Math.min(totalPages.value, start + windowSize - 1);
  start = Math.max(1, end - windowSize + 1);
  const pages = [];
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});

// Single source of truth: any change to page or q in the URL triggers a refetch
watch(
  () => [route.query.page, route.query.q],
  ([newPage]) => {
    page.value = parseInt(newPage) || 1;
    fetchProducts();
  }
);

onMounted(fetchProducts);
</script>