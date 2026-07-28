<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background dengan efek blur -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <!-- Background Image dengan opacity -->
            <div 
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-10"
                style="background-image: url('https://www.emporioarchitect.com/upload/portofolio/1280/desain-pondok-pesantren-mediteran-2-lantai-55030223-443865277030223093318.jpg')"
            ></div>
            <!-- Overlay with blur effect -->
            <div class="absolute inset-0 backdrop-blur-sm bg-white/30"></div>
            <!-- Additional overlay for better readability -->
            <div class="absolute inset-0 bg-gradient-to-b from-white/50 via-white/30 to-white/60"></div>
        </div>

        <!-- Konten utama -->
        <div class="relative z-10 w-full max-w-md px-4">
            <!-- Logo -->
            <div class="mb-8 flex justify-center">
                <Link href="/" class="block">
                    <div class="flex items-center gap-3">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg">
                            <ApplicationLogo class="h-10 w-10 fill-current text-black" />
                        </div>
                        <span class="text-2xl font-bold text-black drop-shadow-lg">
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
                        <h2 class="text-2xl font-bold text-black drop-shadow-sm">Selamat Datang</h2>
                        <p class="mt-2 text-sm text-gray/70">Silakan masuk ke akun Anda</p>
                    </div>

                    <!-- Slot untuk form login -->
                    <slot />

                    <!-- Footer -->
                    <div class="mt-6 text-center border-t border-white/10 pt-4">
                        <p class="text-xs text-black/40">
                            © 2026 Pondok Pesantren
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 