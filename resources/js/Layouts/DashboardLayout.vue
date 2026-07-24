<!-- resources/js/Layouts/DashboardLayout.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

// Props
defineProps({
    title: {
        type: String,
        default: 'Dashboard Utama'
    }
});

// Ambil data user dari Inertia
const page = usePage();
const user = computed(() => page.props.auth.user || {});

// Ambil inisial untuk avatar
const getInitials = (name) => {
    if (!name) return 'U';
    const words = name.split(' ');
    if (words.length >= 2) {
        return (words[0][0] + words[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};

// Ambil nama untuk sapaan
const getGreeting = () => {
    if (user.value.name) {
        return `Selamat datang kembali, ${user.value.name}`;
    }
    return 'Selamat datang kembali';
};

// Ambil inisial untuk logo
const getLogoInitials = () => {
    const name = user.value.name || 'User';
    return getInitials(name);
};

// Ambil nama lengkap untuk logo
const getLogoName = () => {
    return user.value.name || 'User';
};

// Ambil role/title user
const getRole = () => {
    return user.value.role || 'PIMPINAN';
};

// FUNGSI LOGOUT - LANGSUNG KE LOGIN
const handleLogout = () => {
    router.post('/logout', {}, {
        onSuccess: () => {
            window.location.href = '/login';
        },
        onError: () => {
            window.location.href = '/login';
        }
    });
};

// Tahun saat ini untuk footer
const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex flex-col">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg flex flex-col fixed h-full">
            <!-- Logo -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                        {{ getLogoInitials() }}
                    </div>
                    <div>
                        <div class="font-bold text-gray-800 text-sm">{{ getLogoName() }}</div>
                        <div class="text-xs text-gray-500">{{ getRole() }}</div>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="mb-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">UTAMA</div>
                    <div class="space-y-1">
                        <Link href="/dashboard" class="flex items-center px-3 py-2 text-sm rounded-lg bg-blue-50 text-blue-700">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </Link>
                        <Link href="#" class="flex items-center px-3 py-2 text-sm rounded-lg text-gray-700 hover:bg-gray-100">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Keuangan / SPP
                        </Link>
                        <Link href="#" class="flex items-center px-3 py-2 text-sm rounded-lg text-gray-700 hover:bg-gray-100">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Data Kelas
                        </Link>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">AKADEMIK & SANTRI</div>
                    <div class="space-y-1">
                        <Link href="#" class="flex items-center px-3 py-2 text-sm rounded-lg text-gray-700 hover:bg-gray-100">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Data Santri
                        </Link>
                    </div>
                </div>
            </div>

            <!-- LOGOUT -->
            <div class="p-4 border-t border-gray-200">
                <button 
                    @click="handleLogout"
                    class="flex items-center w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors"
                >
                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar Akun
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1 flex flex-col min-h-screen">
            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="px-6 py-4">
                    <h2 class="text-xl font-semibold text-gray-800">{{ title }}</h2>
                    <p class="text-sm text-gray-500">{{ getGreeting() }}</p>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 p-6">
                <slot>
                   
                </slot>
            </div>

            <!-- FOOTER -->
            <footer class="bg-white border-t border-gray-200 mt-auto">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600">
                        <div>
                            &copy; {{ currentYear }}  All rights reserved.
                        </div>
                        <div class="flex space-x-4 mt-2 sm:mt-0">
                            <Link href="#" class="hover:text-gray-900 transition-colors">
                                Privacy Policy
                            </Link>
                            <Link href="#" class="hover:text-gray-900 transition-colors">
                                Terms of Service
                            </Link>
                            <Link href="#" class="hover:text-gray-900 transition-colors">
                                Contact
                            </Link>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>