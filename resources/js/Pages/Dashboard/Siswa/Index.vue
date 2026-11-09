<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    siswas: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({ total: 0, laki_laki: 0, perempuan: 0 })
    }
});

// --- LOGIKA PENCARIAN ---
const search = ref('');
const filteredSiswas = computed(() => {
    return props.siswas.filter(siswa => 
        siswa.nama_lengkap.toLowerCase().includes(search.value.toLowerCase()) ||
        siswa.nis.includes(search.value)
    );
});

// --- LOGIKA MODAL CATATAN ---
const isModalOpen = ref(false);

const form = useForm({
    siswa_id: '',
    jenis: 'izin',
    tanggal: new Date().toISOString().split('T')[0],
    keterangan: ''
});

const openModal = () => {
    isModalOpen.value = true;
    form.reset();
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitCatatan = () => {
    form.post(route('dashboard.siswa.catatan.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            alert('Catatan berhasil disimpan!');
        },
    });
};
</script>

<template>
    <Head title="Data Siswa" />

    <DashboardLayout title="Data Siswa">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- KOTAK STATISTIK -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Siswa</p>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.total }}</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Laki-laki</p>
                            <p class="text-2xl font-bold text-blue-600">{{ stats.laki_laki }}</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Perempuan</p>
                            <p class="text-2xl font-bold text-pink-600">{{ stats.perempuan }}</p>
                        </div>
                        <div class="h-12 w-12 bg-pink-50 text-pink-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Kelas</p>
                            <p class="text-2xl font-bold text-yellow-500">-</p>
                        </div>
                        <div class="h-12 w-12 bg-yellow-50 text-yellow-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- TABEL UTAMA -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6">
                        
                        <!-- Header Tabel -->
                        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Daftar Siswa
                                </h3>
                                <p class="text-sm text-gray-500">Menampilkan {{ filteredSiswas.length }} dari {{ siswas.length }} siswa</p>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                                <!-- Input Pencarian -->
                                <div class="relative w-full md:w-64">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input v-model="search" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2" placeholder="Cari nama atau NIS...">
                                </div>
                                
                                <!-- Tombol Aksi -->
                                <button @click="openModal" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Catatan Siswa
                                </button>
                                
                                <button class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">
                                    + Tambah Siswa
                                </button>
                            </div>
                        </div>

                        <!-- Tabel Data -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-6 py-3">No</th>
                                        <th class="px-6 py-3">NIS</th>
                                        <th class="px-6 py-3">Nama</th>
                                        <th class="px-6 py-3">JK</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="filteredSiswas.length === 0">
                                        <td colspan="6" class="px-6 py-10 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-400">
                                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p class="text-lg font-medium">Tidak ada data siswa ditemukan</p>
                                                <p class="text-sm">Coba ubah kata kunci pencarian</p>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <tr v-for="(siswa, index) in filteredSiswas" :key="siswa.id" class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4">{{ index + 1 }}</td>
                                        <td class="px-6 py-4">{{ siswa.nis }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                                {{ siswa.nama_lengkap.charAt(0).toUpperCase() }}
                                            </div>
                                            {{ siswa.nama_lengkap }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span :class="siswa.jenis_kelamin === 'L' ? 'text-blue-600' : 'text-pink-600'">
                                                {{ siswa.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-0.5 rounded-full border border-blue-200">
                                                {{ siswa.kelas }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 flex gap-3">
                                            <button class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Footer Tabel -->
                        <div class="flex justify-between items-center mt-4 text-sm text-gray-500">
                            <p>Menampilkan {{ filteredSiswas.length }} data</p>
                            <div class="flex gap-2">
                                <button class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50" disabled>Prev</button>
                                <button class="px-3 py-1 border rounded bg-blue-600 text-white">1</button>
                                <button class="px-3 py-1 border rounded hover:bg-gray-50">Next</button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH CATATAN (MUNCUL SAAT DIKLIK)  -->
        <!-- ========================================== -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            <!-- Overlay Gelap (Hitam Transparan) - MENGGUNAKAN BG-BLACK -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                <!-- Kotak Modal -->
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg z-10">
                    
                    <!-- Header Modal -->
                    <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Tambah Catatan Siswa</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Body Modal (Form) -->
                    <div class="bg-white px-6 py-5">
                        <form @submit.prevent="submitCatatan" class="space-y-4">
                            
                            <!-- Pilih Siswa -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Siswa <span class="text-red-500">*</span></label>
                                <select v-model="form.siswa_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                    <option value="" disabled>-- Pilih Siswa --</option>
                                    <option v-for="siswa in siswas" :key="siswa.id" :value="siswa.id">
                                        {{ siswa.nis }} - {{ siswa.nama_lengkap }}
                                    </option>
                                </select>
                                <div v-if="form.errors.siswa_id" class="text-red-500 text-xs mt-1">{{ form.errors.siswa_id }}</div>
                            </div>

                            <!-- Jenis Catatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Catatan <span class="text-red-500">*</span></label>
                                <select v-model="form.jenis" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpha">Alpha (Tanpa Keterangan)</option>
                                    <option value="pelanggaran">Pelanggaran</option>
                                </select>
                                <div v-if="form.errors.jenis" class="text-red-500 text-xs mt-1">{{ form.errors.jenis }}</div>
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" v-model="form.tanggal" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                <div v-if="form.errors.tanggal" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal }}</div>
                            </div>

                            <!-- Keterangan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan <span class="text-red-500">*</span></label>
                                <textarea v-model="form.keterangan" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Contoh: Sakit demam, Terlambat 15 menit, dll..." required></textarea>
                                <div v-if="form.errors.keterangan" class="text-red-500 text-xs mt-1">{{ form.errors.keterangan }}</div>
                            </div>

                            <!-- Footer Modal (Tombol) -->
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 -mx-6 -mb-5 mt-6">
                                <button type="submit" :disabled="form.processing" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-colors">
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                                </button>
                                <button type="button" @click="closeModal" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- END OF MODAL -->

    </DashboardLayout>
</template>