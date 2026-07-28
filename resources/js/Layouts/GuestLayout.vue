<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background dengan efek blur -->
        <div class="absolute inset-0 z-0">
            <!-- Background Image -->
            <div 
                class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                style="background-image: url('https://images.unsplash.com/photo-1580582932707-520aed937b7b?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80')"
            ></div>
            
            <!-- Overlay gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/70 via-emerald-800/50 to-emerald-900/70 backdrop-blur-sm"></div>
            
            <!-- Decorative orbs -->
            <div class="absolute top-[-20%] right-[-10%] h-80 w-80 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="absolute bottom-[-20%] left-[-10%] h-80 w-80 rounded-full bg-emerald-400/20 blur-3xl"></div>
        </div>

        <!-- Konten utama -->
        <div class="relative z-10 w-full max-w-md px-4">
            <!-- Logo -->
            <div class="mb-8 flex justify-center">
                <Link href="/" class="block">
                    <div class="flex items-center gap-3">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg">
                            <ApplicationLogo class="h-10 w-10 fill-current text-white" />
                        </div>
                        <span class="text-2xl font-bold text-white drop-shadow-lg">
                            Pesantren<span class="text-emerald-300">.</span>
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Card Login dengan efek glassmorphism -->
            <div class="relative overflow-hidden rounded-2xl bg-white/10 backdrop-blur-xl shadow-2xl border border-white/20">
                <!-- Decorative gradient line di atas -->
                <div class="h-1 w-full bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-400"></div>
                
                <!-- Card Content -->
                <div class="p-8">
                    <!-- Header -->
                    <div class="mb-6 text-center">
                        <h2 class="text-2xl font-bold text-white drop-shadow-sm">Selamat Datang</h2>
                        <p class="mt-2 text-sm text-white/70">Silakan masuk ke akun Anda</p>
                    </div>

                    <!-- Slot untuk form login -->
                    <slot />

                    <!-- Footer -->
                    <div class="mt-6 text-center border-t border-white/10 pt-4">
                        <p class="text-xs text-white/40">
                            © 2026 Pondok Pesantren
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>