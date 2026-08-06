<!-- resources/js/Pages/Dashboard/Classes/Absensi.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    kelas: {
        type: Object,
        default: () => ({
            id: 1,
            nama_kelas: 'AI',
            tingkat: '1'
        })
    },
    pertemuan: {
        type: Object,
        default: () => ({
            id: 1,
            hari: 'Kamis',
            jam_mulai: '12:30',
            jam_selesai: '15:00',
            ruang: 'Lab Saintek 1'
        })
    },
    peserta: {
        type: Array,
        default: () => []
    },
    absensi: {
        type: Array,
        default: () => []
    }
});

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

// ============================================
// DATA DUMMY (jika tidak ada data dari backend)
// ============================================

// Data dummy peserta
const dummyPeserta = [
    { nim: '236250001', nama: 'Muh. Fauzi Taufiq' },
    { nim: '236250004', nama: 'Andi Muhammad Yusuf Qadri' },
    { nim: '236250005', nama: 'M. Alif Darul Firdaus' },
    { nim: '236250010', nama: 'Muhammad Iqbal' },
    { nim: '236250015', nama: 'Nailul Wafaaah' },
    { nim: '236250025', nama: 'Muh. Fauzan Muqasith' },
    { nim: '236250026', nama: 'Mohammad Ichsan' },
    { nim: '236250027', nama: 'Vira Veriska I Ngadi' },
    { nim: '236250028', nama: 'Abdul Razak' },
    { nim: '236250029', nama: 'Nur Fadilah' },
    { nim: '236250030', nama: 'Febianty' },
    { nim: '236250032', nama: 'Adila Nurramadhan Mg Larase' },
    { nim: '236250039', nama: 'Ghina Aghniya Khairunnisa' },
    { nim: '236250043', nama: 'Mupida' },
    { nim: '236250052', nama: 'Muammar Syakli Darmawan Djallo' },
    { nim: '236250055', nama: 'Nursafira Kadar' },
    { nim: '236250057', nama: 'Intan Salam' },
];

// Data dummy absensi
const dummyAbsensi = [
    { nim: '236250001', status: 'Hadir' },
    { nim: '236250004', status: 'Hadir' },
    { nim: '236250005', status: 'Hadir' },
    { nim: '236250010', status: 'Hadir' },
    { nim: '236250015', status: 'Hadir' },
    { nim: '236250025', status: 'Hadir' },
    { nim: '236250026', status: 'Hadir' },
    { nim: '236250027', status: 'Izin' },
    { nim: '236250028', status: 'Hadir' },
    { nim: '236250029', status: 'Hadir' },
    { nim: '236250030', status: 'Hadir' },
    { nim: '236250032', status: 'Sakit' },
    { nim: '236250039', status: 'Hadir' },
    { nim: '236250043', status: 'Hadir' },
    { nim: '236250052', status: 'Hadir' },
    { nim: '236250055', status: 'Hadir' },
    { nim: '236250057', status: 'Hadir' },
];

// ============================================
// STATE REAKTIF
// ============================================
const pesertaList = ref(props.peserta.length ? props.peserta : dummyPeserta);
const absensiList = ref(props.absensi.length ? props.absensi : dummyAbsensi);
const isLoading = ref(false);
const selectedStatus = ref({});

// ============================================
// COMPUTED
// ============================================
const stats = computed(() => {
    const total = pesertaList.value.length;
    const hadir = absensiList.value.filter(a => a.status === 'Hadir').length;
    const izin = absensiList.value.filter(a => a.status === 'Izin').length;
    const sakit = absensiList.value.filter(a => a.status === 'Sakit').length;
    const alpa = absensiList.value.filter(a => a.status === 'Alpa').length;
    
    return { total, hadir, izin, sakit, alpa };
});

// ============================================
// HELPERS
// ============================================
const getStatusBadge = (status) => {
    const map = {
        'Hadir': 'bg-green-100 text-green-700',
        'Izin': 'bg-yellow-100 text-yellow-700',
        'Sakit': 'bg-blue-100 text-blue-700',
        'Alpa': 'bg-red-100 text-red-700',
    };
    return map[status] || 'bg-gray-100 text-gray-700';
};

const getStatusColor = (status) => {
    const map = {
        'Hadir': 'border-green-500 bg-green-50 hover:bg-green-100',
        'Izin': 'border-yellow-500 bg-yellow-50 hover:bg-yellow-100',
        'Sakit': 'border-blue-500 bg-blue-50 hover:bg-blue-100',
        'Alpa': 'border-red-500 bg-red-50 hover:bg-red-100',
    };
    return map[status] || 'border-gray-500 bg-gray-50 hover:bg-gray-100';
};

const getStatusTextColor = (status) => {
    const map = {
        'Hadir': 'text-green-600',
        'Izin': 'text-yellow-600',
        'Sakit': 'text-blue-600',
        'Alpa': 'text-red-600',
    };
    return map[status] || 'text-gray-600';
};

// ============================================
// FUNGSI ABSENSI
// ============================================
const updateAbsensi = (nim, status) => {
    const index = absensiList.value.findIndex(a => a.nim === nim);
    if (index !== -1) {
        absensiList.value[index].status = status;
    } else {
        absensiList.value.push({ nim, status });
    }
    
    Toast.fire({
        icon: 'success',
        title: `✅ Status ${status} untuk ${pesertaList.value.find(p => p.nim === nim)?.nama || nim}`,
        timer: 2000
    });
};

const saveAllAbsensi = () => {
    isLoading.value = true;
    
    // Simulasi simpan ke database
    setTimeout(() => {
        isLoading.value = false;
        Toast.fire({
            icon: 'success',
            title: '📋 Semua data absensi berhasil disimpan!'
        });
        
        // Redirect kembali ke halaman detail
        router.visit(`/dashboard/classes/${props.kelas.id}/detail`);
    }, 1000);
};

const resetAbsensi = () => {
    Swal.fire({
        title: 'Reset Absensi?',
        text: 'Semua data absensi akan direset ke status Hadir!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            absensiList.value = pesertaList.value.map(p => ({
                nim: p.nim,
                status: 'Hadir'
            }));
            Toast.fire({
                icon: 'success',
                title: '🔄 Absensi berhasil direset!'
            });
        }
    });
};
</script>

<template>
    <Head :title="`Absensi - ${props.kelas?.nama_kelas || 'Kelas'}`" />

    <DashboardLayout 
        :title="`📋 Absensi Kelas`"
        :header-title="`Absensi - ${props.kelas?.nama_kelas || 'Kelas'}`"
        :header-subtitle="`${props.pertemuan?.hari || ''} - ${props.pertemuan?.jam_mulai || ''} s/d ${props.pertemuan?.jam_selesai || ''}`"
    >
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4 flex-wrap">
            <Link href="/dashboard/classes" class="hover:text-blue-600">Data Kelas</Link>
            <span>/</span>
            <Link :href="`/dashboard/classes/${props.kelas.id}/detail`" class="hover:text-blue-600">
                {{ props.kelas?.nama_kelas || 'Detail' }}
            </Link>
            <span>/</span>
            <Link :href="`/dashboard/classes/${props.kelas.id}/detail?tab=absensi`" class="hover:text-blue-600">
                Absensi
            </Link>
            <span>/</span>
            <span class="text-gray-700 font-medium">Pertemuan {{ props.pertemuan?.id || 1 }}</span>
        </div>

        <!-- Info Pertemuan -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Kelas</p>
                <p class="text-sm font-semibold text-gray-800">{{ props.kelas?.nama_kelas || '-' }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Hari</p>
                <p class="text-sm font-semibold text-gray-800">{{ props.pertemuan?.hari || '-' }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Waktu</p>
                <p class="text-sm font-semibold text-gray-800">{{ props.pertemuan?.jam_mulai || '-' }} - {{ props.pertemuan?.jam_selesai || '-' }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Ruang</p>
                <p class="text-sm font-semibold text-gray-800">{{ props.pertemuan?.ruang || '-' }}</p>
            </div>
        </div>

        <!-- Statistik Absensi -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-gray-800">{{ stats.total }}</p>
                <p class="text-xs text-gray-500">Total Santri</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-green-200 text-center">
                <p class="text-2xl font-bold text-green-600">{{ stats.hadir }}</p>
                <p class="text-xs text-green-500">Hadir</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-yellow-200 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ stats.izin }}</p>
                <p class="text-xs text-yellow-500">Izin</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-blue-200 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ stats.sakit }}</p>
                <p class="text-xs text-blue-500">Sakit</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-red-200 text-center">
                <p class="text-2xl font-bold text-red-600">{{ stats.alpa }}</p>
                <p class="text-xs text-red-500">Alpa</p>
            </div>
        </div>

        <!-- Tabel Absensi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">📋 Daftar Hadir Mahasiswa</h3>
                    <p class="text-sm text-gray-500">Total: {{ pesertaList.length }} Santri</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button 
                        @click="resetAbsensi"
                        :disabled="isLoading"
                        class="px-4 py-2 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition disabled:opacity-50"
                    >
                        🔄 Reset
                    </button>
                    <button 
                        @click="saveAllAbsensi"
                        :disabled="isLoading"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 flex items-center gap-2"
                    >
                        <div v-if="isLoading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                        💾 Simpan Semua
                    </button>
                    <Link 
                        :href="`/dashboard/classes/${props.kelas.id}/detail?tab=absensi`"
                        class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                    >
                        ← Kembali
                    </Link>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Mahasiswa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="(peserta, idx) in pesertaList" :key="peserta.nim" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500 text-center">{{ idx + 1 }}</td>
                            <td class="px-4 py-3 text-gray-700 font-mono text-xs">{{ peserta.nim }}</td>
                            <td class="px-4 py-3 text-gray-800 font-medium">{{ peserta.nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 text-xs rounded-full font-medium" 
                                      :class="getStatusBadge(absensiList.find(a => a.nim === peserta.nim)?.status || 'Hadir')">
                                    {{ absensiList.find(a => a.nim === peserta.nim)?.status || 'Hadir' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1 flex-wrap">
                                    <button 
                                        @click="updateAbsensi(peserta.nim, 'Hadir')"
                                        class="px-3 py-1 text-xs rounded border font-medium transition"
                                        :class="absensiList.find(a => a.nim === peserta.nim)?.status === 'Hadir' 
                                            ? 'bg-green-500 text-white border-green-500' 
                                            : 'border-green-500 text-green-600 hover:bg-green-50'"
                                    >
                                        Hadir
                                    </button>
                                    <button 
                                        @click="updateAbsensi(peserta.nim, 'Izin')"
                                        class="px-3 py-1 text-xs rounded border font-medium transition"
                                        :class="absensiList.find(a => a.nim === peserta.nim)?.status === 'Izin' 
                                            ? 'bg-yellow-500 text-white border-yellow-500' 
                                            : 'border-yellow-500 text-yellow-600 hover:bg-yellow-50'"
                                    >
                                        Izin
                                    </button>
                                    <button 
                                        @click="updateAbsensi(peserta.nim, 'Sakit')"
                                        class="px-3 py-1 text-xs rounded border font-medium transition"
                                        :class="absensiList.find(a => a.nim === peserta.nim)?.status === 'Sakit' 
                                            ? 'bg-blue-500 text-white border-blue-500' 
                                            : 'border-blue-500 text-blue-600 hover:bg-blue-50'"
                                    >
                                        Sakit
                                    </button>
                                    <button 
                                        @click="updateAbsensi(peserta.nim, 'Alpa')"
                                        class="px-3 py-1 text-xs rounded border font-medium transition"
                                        :class="absensiList.find(a => a.nim === peserta.nim)?.status === 'Alpa' 
                                            ? 'bg-red-500 text-white border-red-500' 
                                            : 'border-red-500 text-red-600 hover:bg-red-50'"
                                    >
                                        Alpa
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="pesertaList.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                Belum ada peserta kelas
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </DashboardLayout>
</template>