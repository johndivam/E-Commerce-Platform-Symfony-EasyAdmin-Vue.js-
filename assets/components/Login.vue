<template>
  <section class="py-16 px-6 max-w-md mx-auto">
    <h1 class="text-4xl font-bold text-gray-900 mb-4">Login </h1>
    <div v-if="isLoggedIn">
      <router-link
        :to="{ name: 'user' }"
        class="inline-block bg-gray-900 text-white px-6 py-2 rounded-md hover:bg-gray-800"
      >
        Go to my panel
      </router-link>
    </div>
    <form v-else @submit.prevent="handleLogin" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          v-model="email"
          type="email"
          required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input
          v-model="password"
          type="password"
          required
          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-gray-900 text-white py-2 rounded-md hover:bg-gray-800 disabled:opacity-50"
      >
        {{ loading ? 'Logging in...' : 'Login' }}
      </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-4">
        Don't have an account?
        <router-link :to="{ name: 'register' }" class="text-gray-900 font-medium hover:underline">
          Register
        </router-link>
      </p>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import authService from '../services/authService';
import { useCart } from '../composables/useCart.js';

const isLoggedIn = computed(() => authService.isLoggedIn());

const email = ref('');
const password = ref('');
const error = ref(null);
const loading = ref(false);
const router = useRouter();

const handleLogin = async () => {
  error.value = null;
  loading.value = true;
  try {
    const response = await authService.login(email.value, password.value);
    authService.setSession(response.data.token, response.data.user);

    await useCart().mergeGuestCartIntoAccount();

    window.location.href = '/user'; 
  } catch (err) {
    if (err.response?.status === 401) {
      error.value = 'Invalid email or password';
    } else {
      error.value = 'Something went wrong. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>