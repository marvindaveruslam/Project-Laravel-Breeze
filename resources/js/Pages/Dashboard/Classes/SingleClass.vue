<script setup>
import { ref, computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

// ============================================
// DATA KELAS
// ============================================
const classes = ref([
    { 
        id: 1, 
        name: 'Kelas 1A', 
        waliKelas: 'Ustadz Ahmad',
        totalSantri: 28,
        aktif: 24,
        nonAktif: 4,
        tahunAjaran: '2025/2026',
        ruangan: 'Ruang 101',
        status: 'Aktif'
    },
    { 
        id: 2, 
        name: 'Kelas 1B', 
        waliKelas: 'Ustadzah Fatimah',
        totalSantri: 26,
        aktif: 22,
        nonAktif: 4,
        tahunAjaran: '2025/2026',
        ruangan: 'Ruang 102',
        status: 'Aktif'
    },
    { 
        id: 3, 
        name: 'Kelas 2A', 
        waliKelas: 'Ustadz Budi',
        totalSantri: 30,
        aktif: 26,
        nonAktif: 4,
        tahunAjaran: '2025/2026',
        ruangan: 'Ruang 201',
        status: 'Aktif'
    },
    { 
        id: 4, 
        name: 'Kelas 2B', 
        waliKelas: 'Ustadzah Siti',
        totalSantri: 27,
        aktif: 23,
        nonAktif: 4,
        tahunAjaran: '2025/2026',
        ruangan: 'Ruang 202',
        status: 'Aktif'
    },
    { 
        id: 5, 
        name: 'Kelas 3A', 
        waliKelas: 'Ustadz Rahman',
        totalSantri: 29,
        aktif: 25,
        nonAktif: 4,
        tahunAjaran: '2025/2026',
        ruangan: 'Ruang 301',
        status: 'Aktif'
    },
    { 
        id: 6, 
        name: 'Kelas 3B', 
        waliKelas: 'Ustadzah Aisyah',
        totalSantri: 28,
        aktif: 24,
        nonAktif: 4,
        tahunAjaran: '2025/2026',
        ruangan: 'Ruang 302',
        status: 'Aktif'
    },
]);

// ============================================
// STATISTIK OTOMATIS
// ============================================
const stats = computed(() => {
    const totalKelas = classes.value.length;
    const totalSantri = classes.value.reduce((sum, cls) => sum + cls.totalSantri, 0);
    const totalAktif = classes.value.reduce((sum, cls) => sum + cls.aktif, 0);
    const totalNonAktif = classes.value.reduce((sum, cls) => sum + cls.nonAktif, 0);
    const rataRata = Math.round(totalSantri / totalKelas);
    
    return {
        totalKelas,
        totalSantri,
        totalAktif,
        totalNonAktif,
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
const isEdit = ref(false);
const currentId = ref(null);

// Form data
const formData = ref({
    name: '',
    waliKelas: '',
    totalSantri: 0,
    aktif: 0,
    nonAktif: 0,
    tahunAjaran: '2025/2026',
    ruangan: '',
    status: 'Aktif'
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
        item.name.toLowerCase().includes(query) ||
        item.waliKelas.toLowerCase().includes(query) ||
        item.ruangan.toLowerCase().includes(query)
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
// FUNGSI CRUD
// ============================================
const resetForm = () => {
    formData.value = {
        name: '',
        waliKelas: '',
        totalSantri: 0,
        aktif: 0,
        nonAktif: 0,
        tahunAjaran: '2025/2026',
        ruangan: '',
        status: 'Aktif'
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
        formData.value = { ...item };
        showModal.value = true;
    }
};

const saveClass = () => {
    if (!formData.value.name || !formData.value.waliKelas || !formData.value.ruangan) {
        alert('⚠️ Nama Kelas, Wali Kelas, dan Ruangan harus diisi!');
        return;
    }

    if (isEdit.value) {
        const index = classes.value.findIndex(c => c.id === currentId.value);
        if (index !== -1) {
            classes.value[index] = {
                id: currentId.value,
                ...formData.value
            };
        }
    } else {
        const maxId = classes.value.reduce((max, item) => Math.max(max, item.id), 0);
        classes.value.push({
            id: maxId + 1,
            ...formData.value
        });
    }

    showModal.value = false;
    resetForm();
};

const deleteClass = (id) => {
    if (confirm('⚠️ Yakin ingin menghapus kelas ini?')) {
        const index = classes.value.findIndex(c => c.id === id);
        if (index !== -1) {
            classes.value.splice(index, 1);
        }
    }
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
// HELPERS
// ============================================
const getStatusBadge = (status) => {
    return status === 'Aktif' 
        ? 'bg-green-100 text-green-700' 
        : 'bg-red-100 text-red-700';
};

const getInitials = (name) => {
    return name.split(' ').map(word => word[0]).join('').toUpperCase();
};
</script>

<template>
    <Head title="Data Kelas" />

    <DashboardLayout 
        title="Data Kelas"
        header-title="📚 Data Kelas & Santri"
        :header-subtitle="`Total Kelas: ${stats.totalKelas} | Total Santri: ${stats.totalSantri}`"
    >
        <!-- ========================================== -->
        <!-- STATISTIK CARDS                           -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kelas</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totalKelas }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Santri</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totalSantri }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Santri Aktif</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ stats.totalAktif }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Santri Non-Aktif</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ stats.totalNonAktif }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata/Kelas</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.rataRata }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TABEL KELAS                               -->
        <!-- ========================================== -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Tabel -->
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
                            placeholder="Cari kelas atau wali kelas..." 
                            class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-48"
                        >
                    </div>
                    <button 
                        @click="addClass"
                        class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition flex items-center justify-center gap-1"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Kelas
                    </button>
                </div>
            </div>

            <!-- Tabel -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wali Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Non-Aktif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="(item, index) in paginatedClasses" :key="item.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ item.name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                        {{ getInitials(item.waliKelas) }}
                                    </div>
                                    {{ item.waliKelas }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ item.ruangan }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ item.totalSantri }}</td>
                            <td class="px-6 py-4 text-sm text-green-600">{{ item.aktif }}</td>
                            <td class="px-6 py-4 text-sm text-red-600">{{ item.nonAktif }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getStatusBadge(item.status)">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button 
                                    @click="editClass(item.id)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-2"
                                >
                                    Edit
                                </button>
                                <button 
                                    @click="deleteClass(item.id)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="paginatedClasses.length === 0">
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <svg class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Tidak ada data kelas ditemukan</p>
                                <p class="text-sm text-gray-400">Coba ubah kata kunci pencarian</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
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
        <!-- INFO CARD TAMBAHAN                        -->
        <!-- ========================================== -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-200 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 font-medium">Tahun Ajaran</p>
                        <p class="text-sm font-bold text-blue-800">2025/2026</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center gap-3">
                    <div class="bg-green-200 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-green-700 font-medium">Total Wali Kelas</p>
                        <p class="text-sm font-bold text-green-800">{{ stats.totalKelas }} Guru</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-200 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-purple-700 font-medium">Rata-rata Santri/Kelas</p>
                        <p class="text-sm font-bold text-purple-800">{{ stats.rataRata }} Santri</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH / EDIT KELAS                 -->
        <!-- ========================================== -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <!-- Header Modal -->
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

                <!-- Form -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas *</label>
                        <input 
                            v-model="formData.name"
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: Kelas 1A"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas *</label>
                        <input 
                            v-model="formData.waliKelas"
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nama wali kelas"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan *</label>
                        <input 
                            v-model="formData.ruangan"
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: Ruang 101"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Santri</label>
                        <input 
                            v-model.number="formData.totalSantri"
                            type="number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Jumlah total santri"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Santri Aktif</label>
                        <input 
                            v-model.number="formData.aktif"
                            type="number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Jumlah santri aktif"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Santri Non-Aktif</label>
                        <input 
                            v-model.number="formData.nonAktif"
                            type="number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Jumlah santri non-aktif"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                        <input 
                            v-model="formData.tahunAjaran"
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: 2025/2026"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select 
                            v-model="formData.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="Aktif">Aktif</option>
                            <option value="Non-Aktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button 
                        @click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                    >
                        Batal
                    </button>
                    <button 
                        @click="saveClass"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition"
                    >
                        {{ isEdit ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>