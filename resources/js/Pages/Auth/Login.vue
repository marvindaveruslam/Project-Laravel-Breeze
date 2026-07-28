<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 rounded-lg bg-green-500/20 p-3 text-sm font-medium text-green-200 backdrop-blur-sm border border-green-500/30">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" class="text-white/80 text-sm font-medium" />
                <div class="relative mt-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-white/40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full bg-white/10 border-white/20 text-white placeholder-white/50 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-transparent pl-10"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Masukkan email Anda"
                    />
                </div>
                <InputError class="mt-2 text-red-300" :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div>
                <InputLabel for="password" value="Password" class="text-white/80 text-sm font-medium" />
                <div class="relative mt-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-white/40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full bg-white/10 border-white/20 text-white placeholder-white/50 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-transparent pl-10"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan password Anda"
                    />
                </div>
                <InputError class="mt-2 text-red-300" :message="form.errors.password" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-white/70 cursor-pointer hover:text-white transition">
                    <Checkbox 
                        name="remember" 
                        v-model:checked="form.remember"
                        class="rounded border-white/20 bg-white/10 text-emerald-400 focus:ring-2 focus:ring-emerald-400 focus:ring-offset-0"
                    />
                    <span>Ingat saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-white/60 hover:text-white transition font-medium"
                >
                    Lupa password?
                </Link>
            </div>

            <!-- Submit Button -->
            <PrimaryButton
                class="w-full justify-center bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-3 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl border-0"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
                <span v-else class="flex items-center justify-center gap-2">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Masuk
                </span>
            </PrimaryButton>

            <!-- Register Link -->
            <div class="mt-4 text-center">
                <p class="text-sm text-white/60">
                    Belum punya akun?
                    <Link
                        :href="route('register')"
                        class="font-medium text-white hover:text-emerald-300 transition"
                    >
                        Daftar sekarang
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
/* Animasi fade untuk form */
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

/* Custom scrollbar untuk input */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-background-clip: text;
    -webkit-text-fill-color: white;
    transition: background-color 5000s ease-in-out 0s;
    box-shadow: inset 0 0 20px 20px rgba(255, 255, 255, 0.1);
}
</style>