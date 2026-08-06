<!-- resources/js/Pages/Dashboard/Classes/DetailClass.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    kelas: {
        type: Object,
        default: () => ({
            id: 1,
            nama_kelas: 'AI',
            tingkat: '1',
            santris_count: 26
        })
    }
});

// ============================================
// TOAST NOTIFICATION
// ============================================
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

// ============================================
// DATA DUMMY
// ============================================

// Data dummy peserta kelas
const dummyPeserta = [
    { nim: '236250001', nama: 'Muh. Fauzi Taufiq', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250004', nama: 'Andi Muhammad Yusuf Qadri', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250005', nama: 'M. Alif Darul Firdaus', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250010', nama: 'Muhammad Iqbal', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250015', nama: 'Nailul Wafaaah', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250025', nama: 'Muh. Fauzan Muqasith', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250026', nama: 'Mohammad Ichsan', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250027', nama: 'Vira Veriska I Ngadi', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250028', nama: 'Abdul Razak', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250029', nama: 'Nur Fadilah', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250030', nama: 'Febianty', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250032', nama: 'Adila Nurramadhan Mg Larase', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250039', nama: 'Ghina Aghniya Khairunnisa', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250043', nama: 'Mupida', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250052', nama: 'Muammar Syakli Darmawan Djallo', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250055', nama: 'Nursafira Kadar', angkatan: '2023/2024', status: 'Aktif' },
    { nim: '236250057', nama: 'Intan Salam', angkatan: '2023/2024', status: 'Aktif' },
];

// Data dummy jadwal perkuliahan
const dummyJadwal = [
    { 
        id: 1,
        hari: 'Kamis', 
        jam_mulai: '12:30', 
        jam_selesai: '15:00', 
        frekuensi: '1 minggu sekali', 
        fleksibilitas: 'Tidak',
        total_sks: '3.00',
        ruang: 'Lab Saintek 1'
    },
    { 
        id: 2,
        hari: 'Selasa', 
        jam_mulai: '09:00', 
        jam_selesai: '11:30', 
        frekuensi: '1 minggu sekali', 
        fleksibilitas: 'Tidak',
        total_sks: '3.00',
        ruang: 'Lab Saintek 2'
    },
];

// Data dummy presensi per pertemuan
const dummyPresensi = {
    1: [
        { nim: '236250001', nama: 'Muh. Fauzi Taufiq', status: 'Hadir' },
        { nim: '236250004', nama: 'Andi Muhammad Yusuf Qadri', status: 'Hadir' },
        { nim: '236250005', nama: 'M. Alif Darul Firdaus', status: 'Hadir' },
        { nim: '236250010', nama: 'Muhammad Iqbal', status: 'Hadir' },
        { nim: '236250015', nama: 'Nailul Wafaaah', status: 'Hadir' },
        { nim: '236250025', nama: 'Muh. Fauzan Muqasith', status: 'Hadir' },
        { nim: '236250026', nama: 'Mohammad Ichsan', status: 'Hadir' },
        { nim: '236250027', nama: 'Vira Veriska I Ngadi', status: 'Izin' },
        { nim: '236250028', nama: 'Abdul Razak', status: 'Hadir' },
        { nim: '236250029', nama: 'Nur Fadilah', status: 'Hadir' },
        { nim: '236250030', nama: 'Febianty', status: 'Hadir' },
        { nim: '236250032', nama: 'Adila Nurramadhan Mg Larase', status: 'Sakit' },
        { nim: '236250039', nama: 'Ghina Aghniya Khairunnisa', status: 'Hadir' },
        { nim: '236250043', nama: 'Mupida', status: 'Hadir' },
        { nim: '236250052', nama: 'Muammar Syakli Darmawan Djallo', status: 'Hadir' },
        { nim: '236250055', nama: 'Nursafira Kadar', status: 'Hadir' },
        { nim: '236250057', nama: 'Intan Salam', status: 'Hadir' },
    ],
    2: [
        { nim: '236250001', nama: 'Muh. Fauzi Taufiq', status: 'Hadir' },
        { nim: '236250004', nama: 'Andi Muhammad Yusuf Qadri', status: 'Izin' },
        { nim: '236250005', nama: 'M. Alif Darul Firdaus', status: 'Hadir' },
        { nim: '236250010', nama: 'Muhammad Iqbal', status: 'Hadir' },
        { nim: '236250015', nama: 'Nailul Wafaaah', status: 'Hadir' },
        { nim: '236250025', nama: 'Muh. Fauzan Muqasith', status: 'Hadir' },
        { nim: '236250026', nama: 'Mohammad Ichsan', status: 'Sakit' },
        { nim: '236250027', nama: 'Vira Veriska I Ngadi', status: 'Alpa' },
        { nim: '236250028', nama: 'Abdul Razak', status: 'Hadir' },
        { nim: '236250029', nama: 'Nur Fadilah', status: 'Hadir' },
        { nim: '236250030', nama: 'Febianty', status: 'Hadir' },
        { nim: '236250032', nama: 'Adila Nurramadhan Mg Larase', status: 'Izin' },
        { nim: '236250039', nama: 'Ghina Aghniya Khairunnisa', status: 'Hadir' },
        { nim: '236250043', nama: 'Mupida', status: 'Hadir' },
        { nim: '236250052', nama: 'Muammar Syakli Darmawan Djallo', status: 'Hadir' },
        { nim: '236250055', nama: 'Nursafira Kadar', status: 'Hadir' },
        { nim: '236250057', nama: 'Intan Salam', status: 'Hadir' },
    ]
};

// ============================================
// STATE REAKTIF
// ============================================
const activeTab = ref('jadwal');

// Data dummy yang akan digunakan
const peserta = ref(dummyPeserta);
const jadwal = ref(dummyJadwal);
const presensi = ref(dummyPresensi);

// State untuk modal
const showTambahJadwal = ref(false);
const showPresensiModal = ref(false);
const selectedPertemuan = ref(null);

// Form tambah jadwal
const formJadwal = ref({
    hari: '',
    jam_mulai: '',
    jam_selesai: '',
    frekuensi: '1 minggu sekali',
    fleksibilitas: 'Tidak',
    ruang: '',
});

// ============================================
// COMPUTED
// ============================================
const detailKelas = computed(() => ({
    program_studi: 'Informatika',
    mata_kuliah: 'DEEP LEARNING - 3 SKS',
    kurikulum: '2023',
    kapasitas: 90,
    periode: '2025/2026 Genap',
    sistem_kuliah: 'Reguler',
    peserta: peserta.value.length,
    peserta_belum_tervalidasi: 0,
}));

// ============================================
// HELPERS
// ============================================
const getPresensiBadge = (status) => {
    const map = {
        'Hadir': 'bg-green-100 text-green-700',
        'Izin': 'bg-yellow-100 text-yellow-700',
        'Sakit': 'bg-blue-100 text-blue-700',
        'Alpa': 'bg-red-100 text-red-700',
    };
    return map[status] || 'bg-gray-100 text-gray-700';
};

const getPresensiBadgeColor = (status) => {
    const map = {
        'Hadir': 'bg-green-500',
        'Izin': 'bg-yellow-500',
        'Sakit': 'bg-blue-500',
        'Alpa': 'bg-red-500',
    };
    return map[status] || 'bg-gray-500';
};

const formatJam = (jam) => {
    if (!jam) return '-';
    return jam;
};

// ============================================
// FUNGSI JADWAL
// ============================================
const openTambahJadwal = () => {
    formJadwal.value = {
        hari: '',
        jam_mulai: '',
        jam_selesai: '',
        frekuensi: '1 minggu sekali',
        fleksibilitas: 'Tidak',
        ruang: '',
    };
    showTambahJadwal.value = true;
};

const saveJadwal = () => {
    if (!formJadwal.value.hari || !formJadwal.value.jam_mulai || !formJadwal.value.jam_selesai) {
        Toast.fire({
            icon: 'warning',
            title: '⚠️ Hari, Jam Mulai, dan Jam Selesai harus diisi!'
        });
        return;
    }

    const newJadwal = {
        id: jadwal.value.length + 1,
        ...formJadwal.value,
        total_sks: '3.00',
    };

    jadwal.value.push(newJadwal);
    showTambahJadwal.value = false;
    
    Toast.fire({
        icon: 'success',
        title: '✅ Jadwal berhasil ditambahkan!'
    });
};

const deleteJadwal = (id) => {
    Swal.fire({
        title: 'Hapus Jadwal?',
        text: 'Data jadwal yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            jadwal.value = jadwal.value.filter(j => j.id !== id);
            // Hapus juga presensi terkait
            delete presensi.value[id];
            Toast.fire({
                icon: 'success',
                title: '🗑️ Jadwal berhasil dihapus!'
            });
        }
    });
};

// ============================================
// FUNGSI PRESENSI
// ============================================
const openPresensi = (pertemuan) => {
    selectedPertemuan.value = pertemuan;
    // Jika belum ada data presensi untuk pertemuan ini, buat baru
    if (!presensi.value[pertemuan.id]) {
        presensi.value[pertemuan.id] = peserta.value.map(p => ({
            ...p,
            status: 'Hadir'
        }));
    }
    showPresensiModal.value = true;
};

const updatePresensi = (nim, status) => {
    if (!selectedPertemuan.value) return;
    
    const presensiList = presensi.value[selectedPertemuan.value.id];
    if (presensiList) {
        const index = presensiList.findIndex(p => p.nim === nim);
        if (index !== -1) {
            presensiList[index].status = status;
            Toast.fire({
                icon: 'success',
                title: `✅ Status diperbarui menjadi ${status}`
            });
        }
    }
};

const saveAllPresensi = () => {
    Toast.fire({
        icon: 'success',
        title: '📋 Semua data presensi berhasil disimpan!'
    });
    showPresensiModal.value = false;
};

// ============================================
// FUNGSI CEK TABRAKAN (dummy)
// ============================================
const cekTabrakan = () => {
    // Cek tabrakan sederhana
    const grouped = {};
    jadwal.value.forEach(j => {
        if (!grouped[j.hari]) grouped[j.hari] = [];
        grouped[j.hari].push(j);
    });

    let found = false;
    let pesan = '✅ Tidak ada tabrakan jadwal yang terdeteksi.';
    
    Object.keys(grouped).forEach(hari => {
        const items = grouped[hari];
        for (let i = 0; i < items.length; i++) {
            for (let j = i + 1; j < items.length; j++) {
                const a = items[i];
                const b = items[j];
                if (a.jam_mulai < b.jam_selesai && b.jam_mulai < a.jam_selesai) {
                    found = true;
                    pesan = `⚠️ Terdeteksi tabrakan pada hari ${hari} antara jam ${a.jam_mulai}-${a.jam_selesai} dan ${b.jam_mulai}-${b.jam_selesai}`;
                }
            }
        }
    });

    Toast.fire({
        icon: found ? 'warning' : 'success',
        title: pesan
    });
};
</script>

<template>
    <Head :title="`Detail Kelas - ${props.kelas?.nama_kelas || 'Kelas'}`" />

    <DashboardLayout 
        :title="`📚 Detail Kelas`"
        :header-title="props.kelas?.nama_kelas || 'Detail Kelas'"
        :header-subtitle="`Tingkat ${props.kelas?.tingkat || '-'} | ${peserta.length} Santri`"
    >
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
            <Link href="/dashboard/classes" class="hover:text-blue-600">Data Kelas</Link>
            <span>/</span>
            <span class="text-gray-700 font-medium">{{ props.kelas?.nama_kelas || 'Detail' }}</span>
        </div>

        <!-- Info Kelas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Program Studi</p>
                <p class="text-sm font-semibold text-gray-800">{{ detailKelas.program_studi }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Mata Kuliah</p>
                <p class="text-sm font-semibold text-gray-800">{{ detailKelas.mata_kuliah }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Periode</p>
                <p class="text-sm font-semibold text-gray-800">{{ detailKelas.periode }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Kapasitas</p>
                <p class="text-sm font-semibold text-gray-800">{{ detailKelas.kapasitas }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Nama Kelas</p>
                <p class="text-sm font-semibold text-gray-800">{{ detailKelas.mata_kuliah }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Sistem Kuliah</p>
                <p class="text-sm font-semibold text-gray-800">{{ detailKelas.sistem_kuliah }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Peserta</p>
                <p class="text-sm font-semibold text-gray-800">{{ detailKelas.peserta }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <p class="text-xs text-gray-500">Peserta Belum Tervalidasi</p>
                <p class="text-sm font-semibold text-gray-800">{{ detailKelas.peserta_belum_tervalidasi }}</p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-200 px-6 overflow-x-auto">
                <nav class="flex gap-1 -mb-px">
                    <button 
                        @click="activeTab = 'jadwal'"
                        class="px-4 py-3 text-sm font-medium transition whitespace-nowrap"
                        :class="activeTab === 'jadwal' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'"
                    >
                        📅 Jadwal Perkuliahan
                    </button>
                    <button 
                        @click="activeTab = 'presensi'"
                        class="px-4 py-3 text-sm font-medium transition whitespace-nowrap"
                        :class="activeTab === 'presensi' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'"
                    >
                        📋 Presensi Kelas
                    </button>
                    <button 
                        @click="activeTab = 'peserta'"
                        class="px-4 py-3 text-sm font-medium transition whitespace-nowrap"
                        :class="activeTab === 'peserta' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'"
                    >
                        👥 Peserta Kelas
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- TAB 1: Jadwal Perkuliahan -->
                <div v-if="activeTab === 'jadwal'">
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                        <div class="flex gap-2">
                            <button 
                                @click="openTambahJadwal"
                                class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Jadwal
                            </button>
                            <button 
                                @click="cekTabrakan"
                                class="px-4 py-2 text-sm bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition flex items-center gap-1"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Cek Tabrakan
                            </button>
                        </div>
                        <span class="text-sm text-gray-500">Total SKS: 3.00 SKS</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hari</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Mulai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Selesai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frekuensi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fleksibilitas</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total SKS</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ruang</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(item, idx) in jadwal" :key="item.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500 text-center">{{ idx + 1 }}</td>
                                    <td class="px-4 py-3 text-gray-700 font-medium">{{ item.hari }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ formatJam(item.jam_mulai) }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ formatJam(item.jam_selesai) }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ item.frekuensi }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full" 
                                              :class="item.fleksibilitas === 'Ya' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                                            {{ item.fleksibilitas }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 font-medium">{{ item.total_sks }} SKS</td>
                                    <td class="px-4 py-3 text-gray-600">{{ item.ruang }}</td>
                                    <td class="px-4 py-3">
                                        <button 
                                            @click="deleteJadwal(item.id)"
                                            class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus Jadwal"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="jadwal.length === 0">
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                                        Belum ada jadwal perkuliahan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: Presensi Kelas -->
                <div v-if="activeTab === 'presensi'">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-semibold text-gray-700">📋 Daftar Pertemuan</h4>
                        <span class="text-sm text-gray-500">Klik card untuk mengisi presensi</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="(item, idx) in jadwal" :key="item.id" 
                             class="border rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                             @click="openPresensi(item)">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Pertemuan {{ idx + 1 }}</p>
                                    <p class="text-xs text-gray-500">{{ item.hari }}, {{ item.jam_mulai }} - {{ item.jam_selesai }}</p>
                                    <p class="text-xs text-gray-500">Ruang: {{ item.ruang }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                    {{ presensi[item.id] ? presensi[item.id].length : 0 }} Santri
                                </span>
                            </div>
                            <div class="mt-2 flex justify-between items-center">
                                <span class="text-xs text-gray-400">Klik untuk isi presensi</span>
                                <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        <div v-if="jadwal.length === 0" class="col-span-3 text-center py-8 text-gray-400">
                            Belum ada jadwal perkuliahan
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Peserta Kelas -->
                <div v-if="activeTab === 'peserta'">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-semibold text-gray-700">👥 Daftar Peserta Kelas</h4>
                        <span class="text-sm text-gray-500">Total: {{ peserta.length }} Santri</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Santri</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Angkatan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(item, idx) in peserta" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-gray-500 text-center">{{ idx + 1 }}</td>
                                    <td class="px-4 py-2 text-gray-700 font-mono text-xs">{{ item.nim }}</td>
                                    <td class="px-4 py-2 text-gray-800 font-medium">{{ item.nama }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ item.angkatan }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                            {{ item.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="peserta.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                        Belum ada peserta kelas
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH JADWAL                       -->
        <!-- ========================================== -->
        <div 
            v-if="showTambahJadwal" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showTambahJadwal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-semibold text-gray-800">📅 Tambah Jadwal</h3>
                    <button @click="showTambahJadwal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hari *</label>
                        <select v-model="formJadwal.hari" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai *</label>
                            <input v-model="formJadwal.jam_mulai" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai *</label>
                            <input v-model="formJadwal.jam_selesai" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruang</label>
                        <input v-model="formJadwal.ruang" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Lab Saintek 1">
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button @click="showTambahJadwal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button @click="saveJadwal" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                        💾 Simpan
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL PRESENSI KELAS                       -->
        <!-- ========================================== -->
        <div 
            v-if="showPresensiModal && selectedPertemuan" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showPresensiModal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">📋 Presensi Kelas</h3>
                        <p class="text-sm text-gray-500">
                            {{ selectedPertemuan.hari }} - {{ selectedPertemuan.jam_mulai }} s/d {{ selectedPertemuan.jam_selesai }}
                        </p>
                    </div>
                    <button @click="showPresensiModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-4 bg-blue-50 border-b border-blue-100 flex justify-between items-center flex-wrap gap-2">
                    <p class="text-sm text-blue-700">
                        📌 Total: <strong>{{ presensi[selectedPertemuan.id] ? presensi[selectedPertemuan.id].length : 0 }}</strong> Santri
                    </p>
                    <div class="flex gap-3 text-xs flex-wrap">
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Hadir</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-500 inline-block"></span> Izin</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Sakit</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Alpa</span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Santri</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(item, idx) in (presensi[selectedPertemuan.id] || [])" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-gray-500 text-center">{{ idx + 1 }}</td>
                                    <td class="px-4 py-2 text-gray-700 font-mono text-xs">{{ item.nim }}</td>
                                    <td class="px-4 py-2 text-gray-800 font-medium">{{ item.nama }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs rounded-full" :class="getPresensiBadge(item.status)">
                                            {{ item.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex gap-1 flex-wrap">
                                            <button @click="updatePresensi(item.nim, 'Hadir')"
                                                class="px-2 py-1 text-xs rounded border border-green-500 text-green-600 hover:bg-green-50 transition"
                                                :class="item.status === 'Hadir' ? 'bg-green-500 text-white' : ''">
                                                Hadir
                                            </button>
                                            <button @click="updatePresensi(item.nim, 'Izin')"
                                                class="px-2 py-1 text-xs rounded border border-yellow-500 text-yellow-600 hover:bg-yellow-50 transition"
                                                :class="item.status === 'Izin' ? 'bg-yellow-500 text-white' : ''">
                                                Izin
                                            </button>
                                            <button @click="updatePresensi(item.nim, 'Sakit')"
                                                class="px-2 py-1 text-xs rounded border border-blue-500 text-blue-600 hover:bg-blue-50 transition"
                                                :class="item.status === 'Sakit' ? 'bg-blue-500 text-white' : ''">
                                                Sakit
                                            </button>
                                            <button @click="updatePresensi(item.nim, 'Alpa')"
                                                class="px-2 py-1 text-xs rounded border border-red-500 text-red-600 hover:bg-red-50 transition"
                                                :class="item.status === 'Alpa' ? 'bg-red-500 text-white' : ''">
                                                Alpa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button @click="showPresensiModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Tutup
                    </button>
                    <button @click="saveAllPresensi" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                        💾 Simpan Semua
                    </button>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>