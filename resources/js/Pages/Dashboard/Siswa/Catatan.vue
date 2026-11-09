<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Menerima data dari CatatanSiswaController
const props = defineProps({
    catatans: {
        type: Array,
        default: () => []
    },
    siswas: {
        type: Array,
        default: () => []
    }
});

// Form untuk input catatan baru
const form = useForm({
    siswa_id: '',
    jenis: 'izin', // Default value
    tanggal: new Date().toISOString().split('T')[0], // Tanggal hari ini
    keterangan: ''
});

// Fungsi simpan
const submit = () => {
    form.post(route('dashboard.siswa.catatan.store'), {
        onSuccess: () => {
            form.reset('keterangan'); // Kosongkan keterangan setelah sukses
            alert('Catatan berhasil disimpan!');
        }
    });
};

// Warna badge berdasarkan jenis catatan
const getBadgeClass = (jenis) => {
    switch (jenis) {
        case 'izin': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'sakit': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'alpha': return 'bg-red-100 text-red-800 border-red-200';
        case 'pelanggaran': return 'bg-purple-100 text-purple-800 border-purple-200';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="Catatan Siswa" />

    <DashboardLayout title="Catatan Siswa (Izin/Sakit/Alpha/Pelanggaran)">
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Tombol Kembali ke Data Siswa -->
                <div class="mb-4">
                    <Link href="/dashboard/siswa" class="text-blue-600 hover:text-blue-800 flex items-center gap-1 font-medium text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Data Siswa
                    </Link>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- KOLOM KIRI: FORM INPUT -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 h-fit">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tambah Catatan Baru
                        </h3>
                        
                        <form @submit.prevent="submit" class="space-y-4">
                            <!-- Pilih Siswa -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Siswa</label>
                                <select v-model="form.siswa_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="" disabled>-- Pilih Siswa --</option>
                                    <option v-for="siswa in siswas" :key="siswa.id" :value="siswa.id">
                                        {{ siswa.nis }} - {{ siswa.nama_lengkap }}
                                    </option>
                                </select>
                                <div v-if="form.errors.siswa_id" class="text-red-500 text-xs mt-1">{{ form.errors.siswa_id }}</div>
                            </div>

                            <!-- Jenis Catatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Catatan</label>
                                <select v-model="form.jenis" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpha">Alpha (Tanpa Keterangan)</option>
                                    <option value="pelanggaran">Pelanggaran</option>
                                </select>
                                <div v-if="form.errors.jenis" class="text-red-500 text-xs mt-1">{{ form.errors.jenis }}</div>
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <input type="date" v-model="form.tanggal" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <div v-if="form.errors.tanggal" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal }}</div>
                            </div>

                            <!-- Keterangan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan</label>
                                <textarea v-model="form.keterangan" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Contoh: Sakit demam, Terlambat 15 menit, dll..."></textarea>
                                <div v-if="form.errors.keterangan" class="text-red-500 text-xs mt-1">{{ form.errors.keterangan }}</div>
                            </div>

                            <!-- Tombol Simpan -->
                            <button type="submit" :disabled="form.processing" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors flex justify-center items-center gap-2">
                                <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Catatan' }}
                            </button>
                        </form>
                    </div>

                    <!-- KOLOM KANAN: TABEL RIWAYAT -->
                    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                Riwayat Catatan Siswa
                            </h3>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                        <tr>
                                            <th class="px-4 py-3">Tanggal</th>
                                            <th class="px-4 py-3">Nama Siswa</th>
                                            <th class="px-4 py-3">Jenis</th>
                                            <th class="px-4 py-3">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="catatans.length === 0">
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                                Belum ada riwayat catatan.
                                            </td>
                                        </tr>
                                        <tr v-for="catatan in catatans" :key="catatan.id" class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ catatan.tanggal }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">
                                                {{ catatan.siswa?.nama_lengkap }} 
                                                <span class="text-xs text-gray-400">({{ catatan.siswa?.kelas }})</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span :class="['px-2.5 py-1 text-xs font-semibold rounded-full border', getBadgeClass(catatan.jenis)]">
                                                    {{ catatan.jenis.toUpperCase() }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-600">{{ catatan.keterangan }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </DashboardLayout>
</template>