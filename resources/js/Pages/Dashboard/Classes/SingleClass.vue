<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    kelas: Array,
    flash: Object
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
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// ============================================
// DATA DUMMY
// ============================================

// Data dummy peserta kelas
const dummyPesertaKelas = [
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
const dummyJadwalPerkuliahan = [
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
const dummyPresensiPerPertemuan = {
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
    ]
};

// Data dummy detail kelas
const dummyDetailKelas = {
    nama_kelas: 'AI',
    program_studi: 'Informatika',
    mata_kuliah: 'DEEP LEARNING - 3 SKS',
    kurikulum: '2023',
    kapasitas: 90,
    periode: '2025/2026 Genap',
    sistem_kuliah: 'Reguler',
    peserta: 26,
    peserta_belum_tervalidasi: 0,
};

// ============================================
// DATA REAKTIF
// ============================================
const classes = ref(props.kelas || []);
const selectedClass = ref(null);
const activeTab = ref('jadwal');
const selectedPertemuanId = ref(null);
const showPresensiModal = ref(false);
const showTambahJadwalModal = ref(false);
const showCekTabrakanModal = ref(false);

// Data untuk detail modal
const pesertaKelas = ref(dummyPesertaKelas);
const jadwalPerkuliahan = ref(dummyJadwalPerkuliahan);
const detailKelas = ref(dummyDetailKelas);
const presensiPerPertemuan = ref(dummyPresensiPerPertemuan);

// Form Tambah Jadwal
const formJadwal = ref({
    hari: '',
    jam_mulai: '',
    jam_selesai: '',
    frekuensi: '1 minggu sekali',
    fleksibilitas: 'Tidak',
    ruang: '',
});

// ============================================
// STATISTIK OTOMATIS
// ============================================
const stats = computed(() => {
    const totalKelas = classes.value.length;
    const totalSantri = classes.value.reduce((sum, cls) => sum + (cls.santris_count || 0), 0);
    const rataRata = totalKelas > 0 ? Math.round(totalSantri / totalKelas) : 0;
    
    return { 
        totalKelas, 
        totalSantri, 
        rataRata 
    };
});

// ============================================
// STATE INTERAKSI
// ============================================
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(5);
const showModal = ref(false);
const showDetailModal = ref(false);
const isEdit = ref(false);
const currentId = ref(null);
const isLoading = ref(false);

// Form data
const formData = ref({
    nama_kelas: '',
    tingkat: '',
});

// ============================================
// FILTER & PAGINATION
// ============================================
const filteredClasses = computed(() => {
    if (!searchQuery.value) {
        return classes.value;
    }
    const query = searchQuery.value.toLowerCase();
    return classes.value.filter(item => 
        item.nama_kelas?.toLowerCase().includes(query) ||
        item.tingkat?.toLowerCase().includes(query)
    );
});

const paginatedClasses = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredClasses.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredClasses.value.length / itemsPerPage.value);
});

// ============================================
// HELPERS
// ============================================
const getInitials = (name) => {
    if (!name) return 'K';
    return name.split(' ').map(word => word[0]).join('').toUpperCase();
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const getPresensiBadgeClass = (status) => {
    const map = {
        'Hadir': 'bg-green-100 text-green-700',
        'Izin': 'bg-yellow-100 text-yellow-700',
        'Sakit': 'bg-blue-100 text-blue-700',
        'Alpa': 'bg-red-100 text-red-700',
    };
    return map[status] || 'bg-gray-100 text-gray-700';
};

// ============================================
// FUNGSI CRUD KELAS
// ============================================
const resetForm = () => {
    formData.value = {
        nama_kelas: '',
        tingkat: '',
    };
};

const addClass = () => {
    isEdit.value = false;
    currentId.value = null;
    resetForm();
    showModal.value = true;
};

const editClass = (id) => {
    const item = classes.value.find(c => c.id === id);
    if (item) {
        isEdit.value = true;
        currentId.value = id;
        formData.value = {
            nama_kelas: item.nama_kelas,
            tingkat: item.tingkat,
        };
        showModal.value = true;
    }
};

const saveClass = () => {
    if (!formData.value.nama_kelas || !formData.value.tingkat) {
        Toast.fire({
            icon: 'warning',
            title: '⚠️ Nama Kelas dan Tingkat harus diisi!'
        });
        return;
    }

    isLoading.value = true;

    if (isEdit.value) {
        router.put(`/kelas/${currentId.value}`, formData.value, {
            onSuccess: () => {
                showModal.value = false;
                resetForm();
                isLoading.value = false;
                Toast.fire({
                    icon: 'success',
                    title: '✨ Data kelas berhasil diperbarui'
                });
            },
            onError: () => {
                isLoading.value = false;
                Toast.fire({
                    icon: 'error',
                    title: '❌ Gagal memperbarui data!'
                });
            }
        });
    } else {
        router.post('/kelas', formData.value, {
            onSuccess: () => {
                showModal.value = false;
                resetForm();
                isLoading.value = false;
                Toast.fire({
                    icon: 'success',
                    title: '🎉 Data kelas berhasil ditambahkan'
                });
            },
            onError: () => {
                isLoading.value = false;
                Toast.fire({
                    icon: 'error',
                    title: '❌ Gagal menyimpan data!'
                });
            }
        });
    }
};

const deleteClass = (id) => {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            isLoading.value = true;
            router.delete(`/kelas/${id}`, {
                onSuccess: () => {
                    isLoading.value = false;
                    Toast.fire({
                        icon: 'success',
                        title: '🗑️ Data kelas berhasil dihapus'
                    });
                },
                onError: () => {
                    isLoading.value = false;
                    Toast.fire({
                        icon: 'error',
                        title: '❌ Gagal menghapus data!'
                    });
                }
            });
        }
    });
};

// ============================================
// FUNGSI DETAIL KELAS
// ============================================
const viewDetail = (id) => {
    const kelas = classes.value.find(c => c.id === id);
    if (kelas) {
        selectedClass.value = kelas;
        detailKelas.value = {
            ...dummyDetailKelas,
            nama_kelas: kelas.nama_kelas,
        };
        showDetailModal.value = true;
        activeTab.value = 'jadwal';
    }
};

const closeDetailModal = () => {
    showDetailModal.value = false;
    selectedClass.value = null;
    showPresensiModal.value = false;
    selectedPertemuanId.value = null;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

// ============================================
// FUNGSI JADWAL PERKULIAHAN
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
    showTambahJadwalModal.value = true;
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
        id: jadwalPerkuliahan.value.length + 1,
        ...formJadwal.value,
        total_sks: '3.00',
    };

    jadwalPerkuliahan.value.push(newJadwal);
    showTambahJadwalModal.value = false;
    
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
            jadwalPerkuliahan.value = jadwalPerkuliahan.value.filter(j => j.id !== id);
            Toast.fire({
                icon: 'success',
                title: '🗑️ Jadwal berhasil dihapus!'
            });
        }
    });
};

const openCekTabrakan = () => {
    showCekTabrakanModal.value = true;
};

// ============================================
// FUNGSI PRESENSI
// ============================================
const openPresensi = (pertemuanId) => {
    selectedPertemuanId.value = pertemuanId;
    // Pastikan data presensi tersedia
    if (!presensiPerPertemuan.value[pertemuanId]) {
        presensiPerPertemuan.value[pertemuanId] = 
            pesertaKelas.value.map(p => ({ ...p, status: 'Hadir' }));
    }
    showPresensiModal.value = true;
};

const updatePresensi = (nim, newStatus) => {
    if (!selectedPertemuanId.value) return;
    
    const presensiList = presensiPerPertemuan.value[selectedPertemuanId.value];
    if (presensiList) {
        const index = presensiList.findIndex(p => p.nim === nim);
        if (index !== -1) {
            presensiList[index].status = newStatus;
            Toast.fire({
                icon: 'success',
                title: `✅ Status diperbarui menjadi ${newStatus}`
            });
        }
    }
};

const saveAllPresensi = () => {
    if (!selectedPertemuanId.value) return;
    
    Toast.fire({
        icon: 'success',
        title: '📋 Semua data presensi berhasil disimpan!'
    });
    showPresensiModal.value = false;
};

// ============================================
// WATCH
// ============================================
watch(() => props.kelas, (newData) => {
    classes.value = newData || [];
});

watch(() => props.flash, (newFlash) => {
    if (newFlash?.success) {
        Toast.fire({
            icon: 'success',
            title: newFlash.success
        });
    }
    if (newFlash?.error) {
        Toast.fire({
            icon: 'error',
            title: newFlash.error
        });
    }
}, { deep: true });
</script>

<template>
    <Head title="Data Kelas" />

    <DashboardLayout 
        title="Data Kelas"
        header-title="📚 Data Kelas & Santri"
        :header-subtitle="`Total Kelas: ${stats.totalKelas} | Total Santri: ${stats.totalSantri}`"
    >
        <!-- STATISTIK CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Kelas</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totalKelas }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Santri</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totalSantri }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Rata-rata/Kelas</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ stats.rataRata }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Tingkat</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totalKelas }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL KELAS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">📋 Daftar Kelas</h3>
                    <p class="text-sm text-gray-500">Menampilkan {{ filteredClasses.length }} dari {{ classes.length }} kelas</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Cari kelas..." 
                            class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-48"
                        >
                    </div>
                    <button 
                        @click="addClass"
                        :disabled="isLoading"
                        class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition flex items-center justify-center gap-1 disabled:opacity-50"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Kelas
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Santri</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="(item, index) in paginatedClasses" :key="item.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                        {{ getInitials(item.nama_kelas) }}
                                    </div>
                                    {{ item.nama_kelas }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                    {{ item.tingkat }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ item.santris_count || 0 }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ formatDate(item.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <button 
                                        @click="viewDetail(item.id)"
                                        class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition"
                                        title="Detail Kelas"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button 
                                        @click="editClass(item.id)"
                                        :disabled="isLoading"
                                        class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition disabled:opacity-50"
                                        title="Edit Kelas"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button 
                                        @click="deleteClass(item.id)"
                                        :disabled="isLoading"
                                        class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition disabled:opacity-50"
                                        title="Hapus Kelas"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="paginatedClasses.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <svg class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Tidak ada data kelas ditemukan</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-3 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-medium">{{ paginatedClasses.length }}</span> dari 
                    <span class="font-medium">{{ filteredClasses.length }}</span> data
                </p>
                <div class="flex gap-2">
                    <button 
                        @click="goToPage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    >
                        ← Prev
                    </button>
                    <button 
                        v-for="page in totalPages" 
                        :key="page"
                        @click="goToPage(page)"
                        class="px-3 py-1 text-sm rounded-lg transition"
                        :class="page === currentPage ? 'bg-blue-600 text-white' : 'border border-gray-300 hover:bg-gray-50'"
                    >
                        {{ page }}
                    </button>
                    <button 
                        @click="goToPage(currentPage + 1)"
                        :disabled="currentPage === totalPages || totalPages === 0"
                        class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    >
                        Next →
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL DETAIL KELAS                        -->
        <!-- ========================================== -->
        <div 
            v-if="showDetailModal" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="closeDetailModal"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">📚 Detail Kelas dan Jadwal Perkuliahan</h3>
                        <p class="text-sm text-gray-500">{{ selectedClass?.nama_kelas }} - Tingkat {{ selectedClass?.tingkat }}</p>
                    </div>
                    <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Info Detail Kelas -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-gray-50 border-b border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Program Studi</p>
                        <p class="text-sm font-semibold text-gray-800">{{ detailKelas.program_studi }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Mata Kuliah</p>
                        <p class="text-sm font-semibold text-gray-800">{{ detailKelas.mata_kuliah }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kurikulum</p>
                        <p class="text-sm font-semibold text-gray-800">{{ detailKelas.kurikulum }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kapasitas</p>
                        <p class="text-sm font-semibold text-gray-800">{{ detailKelas.kapasitas }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Periode</p>
                        <p class="text-sm font-semibold text-gray-800">{{ detailKelas.periode }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Nama Kelas</p>
                        <p class="text-sm font-semibold text-gray-800">{{ detailKelas.nama_kelas }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Sistem Kuliah</p>
                        <p class="text-sm font-semibold text-gray-800">{{ detailKelas.sistem_kuliah }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Peserta</p>
                        <p class="text-sm font-semibold text-gray-800">{{ detailKelas.peserta }}</p>
                    </div>
                </div>

                <!-- Tab Navigation -->
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

                <!-- Content -->
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
                                    @click="openCekTabrakan"
                                    class="px-4 py-2 text-sm bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition flex items-center gap-1"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Cek Tabrakan Jadwal
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
                                    <tr v-for="(jadwal, idx) in jadwalPerkuliahan" :key="jadwal.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-500 text-center">{{ idx + 1 }}</td>
                                        <td class="px-4 py-3 text-gray-700 font-medium">{{ jadwal.hari }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ jadwal.jam_mulai }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ jadwal.jam_selesai }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ jadwal.frekuensi }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full" 
                                                  :class="jadwal.fleksibilitas === 'Ya' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                                                {{ jadwal.fleksibilitas }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 font-medium">{{ jadwal.total_sks }} SKS</td>
                                        <td class="px-4 py-3 text-gray-600">{{ jadwal.ruang }}</td>
                                        <td class="px-4 py-3">
                                            <button 
                                                @click="deleteJadwal(jadwal.id)"
                                                class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition"
                                                title="Hapus Jadwal"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="jadwalPerkuliahan.length === 0">
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
                            <h4 class="text-md font-semibold text-gray-700">📋 Presensi Kelas</h4>
                            <span class="text-sm text-gray-500">Pilih pertemuan untuk mengisi presensi</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div v-for="(jadwal, idx) in jadwalPerkuliahan" :key="jadwal.id" 
                                 class="border rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                                 @click="openPresensi(jadwal.id)">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Pertemuan {{ idx + 1 }}</p>
                                        <p class="text-xs text-gray-500">{{ jadwal.hari }}, {{ jadwal.jam_mulai }} - {{ jadwal.jam_selesai }}</p>
                                        <p class="text-xs text-gray-500">Ruang: {{ jadwal.ruang }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                        {{ (presensiPerPertemuan[jadwal.id] || []).length }} Santri
                                    </span>
                                </div>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="text-xs text-gray-400">Klik untuk isi presensi</span>
                                    <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                            <div v-if="jadwalPerkuliahan.length === 0" class="col-span-3 text-center py-8 text-gray-400">
                                Belum ada jadwal perkuliahan. Silahkan tambah jadwal terlebih dahulu.
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Peserta Kelas -->
                    <div v-if="activeTab === 'peserta'">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-semibold text-gray-700">👥 Daftar Peserta Kelas</h4>
                            <span class="text-sm text-gray-500">Total: {{ pesertaKelas.length }} Santri</span>
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
                                    <tr v-for="(peserta, idx) in pesertaKelas" :key="idx" class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-500 text-center">{{ idx + 1 }}</td>
                                        <td class="px-4 py-2 text-gray-700 font-mono text-xs">{{ peserta.nim }}</td>
                                        <td class="px-4 py-2 text-gray-800 font-medium">{{ peserta.nama }}</td>
                                        <td class="px-4 py-2 text-gray-600">{{ peserta.angkatan }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                                {{ peserta.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end p-6 border-t border-gray-200">
                    <button 
                        @click="closeDetailModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH JADWAL                       -->
        <!-- ========================================== -->
        <div 
            v-if="showTambahJadwalModal" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showTambahJadwalModal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">📅 Tambah Jadwal</h3>
                    <button @click="showTambahJadwalModal = false" class="text-gray-400 hover:text-gray-600">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frekuensi</label>
                        <select v-model="formJadwal.frekuensi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1 minggu sekali">1 minggu sekali</option>
                            <option value="2 minggu sekali">2 minggu sekali</option>
                            <option value="Setiap hari">Setiap hari</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fleksibilitas</label>
                        <select v-model="formJadwal.fleksibilitas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Tidak">Tidak</option>
                            <option value="Ya">Ya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruang</label>
                        <input v-model="formJadwal.ruang" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Lab Saintek 1">
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button 
                        @click="showTambahJadwalModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                    >
                        Batal
                    </button>
                    <button 
                        @click="saveJadwal"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition"
                    >
                        💾 Simpan Jadwal
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL CEK TABRAKAN JADWAL                 -->
        <!-- ========================================== -->
        <div 
            v-if="showCekTabrakanModal" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showCekTabrakanModal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">⚠️ Cek Tabrakan Jadwal</h3>
                    <button @click="showCekTabrakanModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div v-if="jadwalPerkuliahan.length === 0" class="text-center py-8 text-gray-400">
                        Belum ada jadwal untuk dicek tabrakan.
                    </div>
                    <div v-else>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <p class="text-green-700 text-sm flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tidak ada tabrakan jadwal yang terdeteksi.
                            </p>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p class="font-medium mb-2">📋 Daftar Jadwal Saat Ini:</p>
                            <ul class="space-y-1">
                                <li v-for="(jadwal, idx) in jadwalPerkuliahan" :key="jadwal.id" class="flex justify-between border-b border-gray-100 py-1">
                                    <span>{{ idx + 1 }}. {{ jadwal.hari }}</span>
                                    <span>{{ jadwal.jam_mulai }} - {{ jadwal.jam_selesai }}</span>
                                    <span class="text-gray-400">{{ jadwal.ruang }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end p-6 border-t border-gray-200">
                    <button 
                        @click="showCekTabrakanModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL PRESENSI KELAS                       -->
        <!-- ========================================== -->
        <div 
            v-if="showPresensiModal" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="showPresensiModal = false"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">📋 Presensi Kelas</h3>
                        <p class="text-sm text-gray-500">
                            Pertemuan ke-{{ selectedPertemuanId }} - 
                            {{ jadwalPerkuliahan.find(j => j.id === selectedPertemuanId)?.hari || '' }}
                            {{ jadwalPerkuliahan.find(j => j.id === selectedPertemuanId)?.jam_mulai || '' }}
                        </p>
                    </div>
                    <button @click="showPresensiModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-4 bg-blue-50 border-b border-blue-100 flex justify-between items-center flex-wrap gap-2">
                    <div>
                        <p class="text-sm text-blue-700">
                            📌 Total Mahasiswa: <strong>{{ (presensiPerPertemuan[selectedPertemuanId] || []).length }}</strong>
                        </p>
                    </div>
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
                                <tr v-for="(presensi, idx) in (presensiPerPertemuan[selectedPertemuanId] || [])" :key="idx" class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-gray-500 text-center">{{ idx + 1 }}</td>
                                    <td class="px-4 py-2 text-gray-700 font-mono text-xs">{{ presensi.nim }}</td>
                                    <td class="px-4 py-2 text-gray-800 font-medium">{{ presensi.nama }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs rounded-full" :class="getPresensiBadgeClass(presensi.status)">
                                            {{ presensi.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex gap-1 flex-wrap">
                                            <button 
                                                @click="updatePresensi(presensi.nim, 'Hadir')"
                                                class="px-2 py-1 text-xs rounded border border-green-500 text-green-600 hover:bg-green-50 transition"
                                                :class="presensi.status === 'Hadir' ? 'bg-green-500 text-white' : ''"
                                            >
                                                Hadir
                                            </button>
                                            <button 
                                                @click="updatePresensi(presensi.nim, 'Izin')"
                                                class="px-2 py-1 text-xs rounded border border-yellow-500 text-yellow-600 hover:bg-yellow-50 transition"
                                                :class="presensi.status === 'Izin' ? 'bg-yellow-500 text-white' : ''"
                                            >
                                                Izin
                                            </button>
                                            <button 
                                                @click="updatePresensi(presensi.nim, 'Sakit')"
                                                class="px-2 py-1 text-xs rounded border border-blue-500 text-blue-600 hover:bg-blue-50 transition"
                                                :class="presensi.status === 'Sakit' ? 'bg-blue-500 text-white' : ''"
                                            >
                                                Sakit
                                            </button>
                                            <button 
                                                @click="updatePresensi(presensi.nim, 'Alpa')"
                                                class="px-2 py-1 text-xs rounded border border-red-500 text-red-600 hover:bg-red-50 transition"
                                                :class="presensi.status === 'Alpa' ? 'bg-red-500 text-white' : ''"
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

                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button 
                        @click="showPresensiModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                    >
                        Batal
                    </button>
                    <button 
                        @click="saveAllPresensi"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition"
                    >
                        💾 Simpan Semua
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH / EDIT KELAS                 -->
        <!-- ========================================== -->
        <div 
            v-if="showModal" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="closeModal"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ isEdit ? '✏️ Edit Kelas' : '📝 Tambah Kelas' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas *</label>
                        <input 
                            v-model="formData.nama_kelas"
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: Kelas 1A"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat *</label>
                        <select 
                            v-model="formData.tingkat"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Pilih Tingkat</option>
                            <option value="1">Tingkat 1</option>
                            <option value="2">Tingkat 2</option>
                            <option value="3">Tingkat 3</option>
                            <option value="4">Tingkat 4</option>
                            <option value="5">Tingkat 5</option>
                            <option value="6">Tingkat 6</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button 
                        @click="closeModal"
                        :disabled="isLoading"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition disabled:opacity-50"
                    >
                        Batal
                    </button>
                    <button 
                        @click="saveClass"
                        :disabled="isLoading"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <div v-if="isLoading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                        {{ isEdit ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>