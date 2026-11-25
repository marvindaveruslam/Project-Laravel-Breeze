<!-- resources/js/Pages/Dashboard/Classes/Absensi.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    kelas: { type: Object, required: true },
    pertemuan: { type: Object, required: true }, // Data dari pivot jadwal
    peserta: { type: Array, default: () => [] },
    absensi: { type: Array, default: () => [] } // Data absensi yang sudah ada (jika ada)
});

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

// ============================================
// STATE REAKTIF
// ============================================
// Kita langsung pakai data dari props
const pesertaList = ref(props.peserta);
const isLoading = ref(false);

// Menggabungkan data peserta dengan status absensi
// Jika di database belum ada absensi, default-nya 'Hadir'
const absensiList = ref(
    props.peserta.map(p => {
        const existing = props.absensi.find(a => a.santri_id === p.id || a.nis === p.nis);
        return {
            nis: p.nis,
            santri_id: p.id,
            status: existing ? existing.status : 'Hadir'
        };
    })
);

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

// ============================================
// FUNGSI ABSENSI
// ============================================
const updateAbsensi = (nis, status) => {
    const index = absensiList.value.findIndex(a => a.nis === nis);
    if (index !== -1) {
        absensiList.value[index].status = status;
    } else {
        absensiList.value.push({ nis, status });
    }
    
    const namaSantri = pesertaList.value.find(p => p.nis === nis)?.name || nis;
    
    Toast.fire({
        icon: 'success',
        title: `✅ Status ${status} untuk ${namaSantri}`,
        timer: 2000
    });
};

const saveAllAbsensi = () => {
    isLoading.value = true;
    
    // Kirim data ke backend menggunakan Inertia router
    // Pastikan rute 'absensi.store' sudah dibuat di web.php
    router.post(route('absensi.store', props.pertemuan.id), {
        absensi: absensiList.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isLoading.value = false;
            Toast.fire({
                icon: 'success',
                title: '📋 Semua data absensi berhasil disimpan!'
            });
            // Kembali ke halaman detail kelas
            router.visit(route('dashboard.detail', props.kelas.id));
        },
        onError: () => {
            isLoading.value = false;
            Toast.fire({
                icon: 'error',
                title: 'Gagal menyimpan data absensi.'
            });
        }
    });
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
                nis: p.nis,
                santri_id: p.id,
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
    <Head :title="`Absensi - ${kelas.nama_kelas}`" />

    <DashboardLayout 
        title="Absensi Kelas"
        :header-title="`Absensi - ${kelas.nama_kelas}`"
        :header-subtitle="`${pertemuan?.hari || ''} - ${pertemuan?.jam_mulai || ''} s/d ${pertemuan?.jam_selesai || ''}`"
    >
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4 flex-wrap">
            <Link :href="route('dashboard.classes')" class="hover:text-blue-600">Data Kelas</Link>
            <span>/</span>
            <Link :href="route('dashboard.detail', kelas.id)" class="hover:text-blue-600">
                {{ kelas.nama_kelas }}
            </Link>
            <span>/</span>
            <span class="text-gray-700 font-medium">Absensi Pertemuan {{ pertemuan?.id }}</span>
        </div>

        <!-- Info Pertemuan -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Kelas</p>
                <p class="text-sm font-semibold text-gray-900">{{ kelas.nama_kelas }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Hari</p>
                <p class="text-sm font-semibold text-gray-900">{{ pertemuan?.hari || '-' }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Waktu</p>
                <p class="text-sm font-semibold text-gray-900">{{ pertemuan?.jam_mulai || '-' }} - {{ pertemuan?.jam_selesai || '-' }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Mata Pelajaran</p>
                <p class="text-sm font-semibold text-gray-900">{{ pertemuan?.mata_pelajaran || '-' }}</p>
            </div>
        </div>

        <!-- Statistik Absensi -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 border border-gray-200 text-center shadow-sm">
                <p class="text-2xl font-bold text-gray-800">{{ stats.total }}</p>
                <p class="text-xs text-gray-500">Total Santri</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-green-200 text-center shadow-sm">
                <p class="text-2xl font-bold text-green-600">{{ stats.hadir }}</p>
                <p class="text-xs text-green-500">Hadir</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-yellow-200 text-center shadow-sm">
                <p class="text-2xl font-bold text-yellow-600">{{ stats.izin }}</p>
                <p class="text-xs text-yellow-500">Izin</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-blue-200 text-center shadow-sm">
                <p class="text-2xl font-bold text-blue-600">{{ stats.sakit }}</p>
                <p class="text-xs text-blue-500">Sakit</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-red-200 text-center shadow-sm">
                <p class="text-2xl font-bold text-red-600">{{ stats.alpa }}</p>
                <p class="text-xs text-red-500">Alpa</p>
            </div>
        </div>

        <!-- Tabel Absensi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-gray-50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">📋 Daftar Hadir Santri</h3>
                    <p class="text-sm text-gray-500">Total: {{ pesertaList.length }} Santri</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button 
                        @click="resetAbsensi"
                        :disabled="isLoading || pesertaList.length === 0"
                        class="px-4 py-2 text-sm font-medium bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition disabled:opacity-50"
                    >
                        🔄 Reset
                    </button>
                    <button 
                        @click="saveAllAbsensi"
                        :disabled="isLoading || pesertaList.length === 0"
                        class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 flex items-center gap-2 shadow-sm"
                    >
                        <div v-if="isLoading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                        💾 Simpan Absensi
                    </button>
                    <Link 
                        :href="route('dashboard.detail', kelas.id)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        ← Kembali
                    </Link>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">NIS</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Santri</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="pesertaList.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Belum ada santri di kelas ini.
                            </td>
                        </tr>
                        <tr v-for="(santri, idx) in pesertaList" :key="santri.id" class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500 text-center">{{ idx + 1 }}</td>
                            <td class="px-4 py-3 text-gray-700 font-mono text-xs">{{ santri.nis }}</td>
                            <td class="px-4 py-3 text-gray-900 font-medium">{{ santri.name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 text-xs rounded-full font-medium" 
                                      :class="getStatusBadge(absensiList.find(a => a.nis === santri.nis)?.status || 'Hadir')">
                                    {{ absensiList.find(a => a.nis === santri.nis)?.status || 'Hadir' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1 flex-wrap">
                                    <!-- Tombol Hadir -->
                                    <button 
                                        @click="updateAbsensi(santri.nis, 'Hadir')"
                                        class="px-2.5 py-1 text-xs rounded border font-medium transition"
                                        :class="absensiList.find(a => a.nis === santri.nis)?.status === 'Hadir' 
                                            ? 'bg-green-500 text-white border-green-500' 
                                            : 'border-green-500 text-green-600 hover:bg-green-50'"
                                    >
                                        Hadir
                                    </button>
                                    <!-- Tombol Izin -->
                                    <button 
                                        @click="updateAbsensi(santri.nis, 'Izin')"
                                        class="px-2.5 py-1 text-xs rounded border font-medium transition"
                                        :class="absensiList.find(a => a.nis === santri.nis)?.status === 'Izin' 
                                            ? 'bg-yellow-500 text-white border-yellow-500' 
                                            : 'border-yellow-500 text-yellow-600 hover:bg-yellow-50'"
                                    >
                                        Izin
                                    </button>
                                    <!-- Tombol Sakit -->
                                    <button 
                                        @click="updateAbsensi(santri.nis, 'Sakit')"
                                        class="px-2.5 py-1 text-xs rounded border font-medium transition"
                                        :class="absensiList.find(a => a.nis === santri.nis)?.status === 'Sakit' 
                                            ? 'bg-blue-500 text-white border-blue-500' 
                                            : 'border-blue-500 text-blue-600 hover:bg-blue-50'"
                                    >
                                        Sakit
                                    </button>
                                    <!-- Tombol Alpa -->
                                    <button 
                                        @click="updateAbsensi(santri.nis, 'Alpa')"
                                        class="px-2.5 py-1 text-xs rounded border font-medium transition"
                                        :class="absensiList.find(a => a.nis === santri.nis)?.status === 'Alpa' 
                                            ? 'bg-red-500 text-white border-red-500' 
                                            : 'border-red-500 text-red-600 hover:bg-red-50'"
                                    >
                                        Alpa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </DashboardLayout>
</template>