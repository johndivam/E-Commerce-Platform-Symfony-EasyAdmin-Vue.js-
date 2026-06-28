<template>
    <div class="flex min-h-screen flex-col bg-white">

        <!-- Navbar -->
        <header class="sticky top-0 z-50 border-b border-gray-200 bg-white">
            <nav class="mx-auto flex h-16 max-w-5xl items-center justify-between px-6">
                <RouterLink
                    to="/"
                    class="text-xl font-bold tracking-tight text-gray-900"
                >
                    MyApp
                </RouterLink>

                <ul class="flex items-center gap-6 text-sm font-medium text-gray-600">
                    <li>
                        <RouterLink
                            to="/"
                            class="transition-colors hover:text-gray-900"
                            active-class="border-b-2 border-gray-900 pb-0.5 text-gray-900"
                        >
                            Home
                        </RouterLink>
                    </li>

                    <li>
                        <RouterLink
                            to="/about"
                            class="transition-colors hover:text-gray-900"
                            active-class="border-b-2 border-gray-900 pb-0.5 text-gray-900"
                        >
                            About Us
                        </RouterLink>
                    </li>
                    <li>
                        <RouterLink
                            to="/products"
                            class="transition-colors hover:text-gray-900"
                            active-class="border-b-2 border-gray-900 pb-0.5 text-gray-900"
                        >
                            Products
                        </RouterLink>
                    </li>
                    <li v-if="!user">
                        <RouterLink
                            to="/login"
                            class="transition-colors hover:text-gray-900"
                            active-class="border-b-2 border-gray-900 pb-0.5 text-gray-900"
                        >
                            Login
                        </RouterLink>
                    </li>

                    <li v-if="user" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-1 text-gray-900 hover:text-gray-700">
                            {{ user.name }}
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': dropdownOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor" >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div v-if="dropdownOpen" v-click-outside="closeDropdown" class="absolute right-0 mt-2 w-44 rounded-md border border-gray-200 bg-white py-1 shadow-lg">
                            <RouterLink to="/user" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" @click="closeDropdown">
                                Profile
                            </RouterLink>
                            <button @click="logout" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">
                                Logout
                            </button>
                        </div>
                    </li>
                </ul>
            </nav>
        </header>

        <!-- Page content -->
        <main class="flex-1">
            <RouterView />
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 py-6 text-center text-sm text-gray-500">
            © {{ new Date().getFullYear() }} MyApp. All rights reserved.
        </footer>

    </div>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import authService from '../services/authService';

const user = ref(null);
const dropdownOpen = ref(false);
const router = useRouter();

onMounted(() => {
    const stored = localStorage.getItem('user');
    if (stored) {
        user.value = JSON.parse(stored);
    }
});

const closeDropdown = () => {
    dropdownOpen.value = false;
};

const logout = () => {
    authService.logout();
    user.value = null;
    dropdownOpen.value = false;
    window.location.href = '/'; 
};
</script>