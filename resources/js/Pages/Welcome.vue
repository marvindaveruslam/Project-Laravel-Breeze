<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Pondok Pesantren" />
    
    <div class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-emerald-50/30">
        <!-- Ornamen Atas -->
        <div class="fixed top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-600 via-green-500 to-emerald-600 z-50"></div>

        <div class="relative min-h-screen flex flex-col">
            <!-- ========================================== -->
            <!-- BACKGROUND IMAGE WITH BLUR & OPACITY      -->
            <!-- ========================================== -->
            <div class="absolute inset-0 z-0 overflow-hidden">
                <!-- Background Image dengan opacity -->
                <div 
                    class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20"
                    style="background-image: url('https://images.unsplash.com/photo-1580582932707-520aed937b7b?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80')"
                ></div>
                <!-- Overlay with blur effect -->
                <div class="absolute inset-0 backdrop-blur-sm bg-white/30"></div>
                <!-- Additional overlay for better readability -->
                <div class="absolute inset-0 bg-gradient-to-b from-white/50 via-white/30 to-white/60"></div>
            </div>

            <!-- ========================================== -->
            <!-- NAVIGATION                                -->
            <!-- ========================================== -->
            <nav class="relative z-10 container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-600 shadow-md">
                            <span class="text-lg font-bold text-white">P</span>
                        </div>
                        <span class="text-xl font-semibold text-gray-800">
                            Pesantren<span class="text-emerald-600">.</span>
                        </span>
                    </div>

                    <!-- Auth Buttons -->
                    <div v-if="canLogin" class="flex items-center space-x-3">
                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="route('dashboard')"
                                class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition shadow-lg"
                            >
                                Dashboard
                            </Link>
                        </template>

                    </div>
                </div>
            </nav>

            <!-- ========================================== -->
            <!-- Main                                      -->
            <!-- ========================================== -->
            <main class="relative z-10 container mx-auto flex-1 flex flex-col items-center justify-center px-6 py-16">
                <div class="max-w-3xl text-center">
                    <!-- Icon -->
                    <div class="mb-6 inline-flex items-center justify-center rounded-full bg-emerald-100/80 backdrop-blur-sm p-4 shadow-lg">
                        <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>

                    <!-- Title -->
                    <h1 class="mb-4 text-5xl font-light leading-tight text-gray-800 md:text-6xl drop-shadow-sm">
                        Pondok 
                        <span class="font-bold text-emerald-600">Pesantren</span>
                    </h1>

                    <!-- Subtitle -->
                    <p class="mx-auto mb-8 max-w-xl text-base text-gray-700 leading-relaxed bg-white/30 backdrop-blur-sm px-6 py-3 rounded-xl inline-block">
                        Mencetak generasi rabbani yang berilmu, beramal, dan berakhlak mulia
                    </p>

                    <!-- CTA - Only Login Button -->
                    <div v-if="!$page.props.auth.user" class="flex flex-wrap items-center justify-center gap-3">
                        <Link
                            :href="route('login')"
                            class="rounded-lg bg-emerald-600 px-8 py-3 text-sm font-medium text-white hover:bg-emerald-700 transition shadow-lg hover:shadow-xl"
                        >
                            Masuk
                        </Link>
                    </div>

                    <!-- Divider -->
                    <div class="mx-auto mt-12 flex w-16 items-center justify-center">
                        <div class="h-px flex-1 bg-gray-300"></div>
                        <span class="px-3 text-xs text-gray-500">✦</span>
                        <div class="h-px flex-1 bg-gray-300"></div>
                    </div>

                    <!-- 3 Nilai Utama -->
                    <div class="mt-8 grid grid-cols-3 gap-8">
                        <div class="bg-white/40 backdrop-blur-sm rounded-lg p-4 shadow-sm">
                            <div class="text-xl font-bold text-emerald-600">Iman</div>
                            <div class="mt-1 text-xs text-gray-600">Landasan utama</div>
                        </div>
                        <div class="bg-white/40 backdrop-blur-sm rounded-lg p-4 shadow-sm">
                            <div class="text-xl font-bold text-emerald-600">Ilmu</div>
                            <div class="mt-1 text-xs text-gray-600">Bekal kehidupan</div>
                        </div>
                        <div class="bg-white/40 backdrop-blur-sm rounded-lg p-4 shadow-sm">
                            <div class="text-xl font-bold text-emerald-600">Amal</div>
                            <div class="mt-1 text-xs text-gray-600">Implementasi nyata</div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- ========================================== -->
            <!-- FOOTER                                   -->
            <!-- ========================================== -->
            <footer class="relative z-10 border-t border-gray-200/50 bg-white/30 backdrop-blur-sm">
                <div class="container mx-auto px-6 py-5">
                    <div class="flex flex-col items-center justify-between gap-3 md:flex-row">
                        <p class="text-xs text-gray-600">
                            © 2026 Pondok Pesantren
                        </p>
                        <div class="flex space-x-5 text-xs text-gray-600">
                            <a href="#" class="hover:text-emerald-600 transition">Tentang</a>
                            <a href="#" class="hover:text-emerald-600 transition">Program</a>
                            <a href="#" class="hover:text-emerald-600 transition">Kontak</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>