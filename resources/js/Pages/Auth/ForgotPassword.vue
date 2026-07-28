<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Password" />

        <!-- Status Message -->
        <div
            v-if="status"
            class="mb-4 rounded-lg bg-emerald-500/30 p-3 text-sm font-medium text-emerald-700 backdrop-blur-sm border border-emerald-400/30"
        >
            {{ status }}
        </div>

        <!-- Deskripsi -->
        <div class="mb-6 text-sm text-gray-600 leading-relaxed bg-gray-50/80 p-4 rounded-lg border border-gray-200">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-emerald-500 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-700">Lupa password? Tenang saja. Masukkan alamat email Anda dan kami akan mengirimkan link reset password untuk membuat password baru.</span>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" class="text-gray-700 text-sm font-medium" />
                <div class="relative mt-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full bg-white/95 border-gray-300 text-gray-900 placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent pl-10"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Masukkan email Anda"
                    />
                </div>
                <InputError class="mt-2 text-red-500" :message="form.errors.email" />
            </div>

            <!-- Tombol Submit -->
            <PrimaryButton
                class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3.5 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl border-0"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengirim...
                </span>
                <span v-else class="flex items-center justify-center gap-2 text-lg tracking-wider">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    Kirim Link Reset Password
                </span>
            </PrimaryButton>

            <!-- Link Kembali ke Login -->
            <div class="mt-4 text-center">
                <Link
                    :href="route('login')"
                    class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-emerald-600 transition font-medium"
                >
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke halaman login
                </Link>
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
    -webkit-text-fill-color: #1a1a1a;
    transition: background-color 5000s ease-in-out 0s;
    box-shadow: inset 0 0 20px 20px rgba(255, 255, 255, 0.9);
}
</style>