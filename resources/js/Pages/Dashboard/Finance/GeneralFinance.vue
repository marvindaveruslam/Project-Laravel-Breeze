<script setup>
import { ref, computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

// ============================================
// DATA TRANSAKSI
// ============================================
const transactions = ref([
    { id: 1, nama: 'Ahmad Fauzi', kelas: '3A', tagihan: 150000, terbayar: 150000, status: 'Lunas', tanggal: '2026-01-15' },
    { id: 2, nama: 'Budi Santoso', kelas: '2B', tagihan: 150000, terbayar: 100000, status: 'Kurang', tanggal: '2026-01-14' },
    { id: 3, nama: 'Citra Dewi', kelas: '1A', tagihan: 150000, terbayar: 0, status: 'Belum Bayar', tanggal: '2026-01-13' },
    { id: 4, nama: 'Dian Pratama', kelas: '3B', tagihan: 150000, terbayar: 150000, status: 'Lunas', tanggal: '2026-01-12' },
    { id: 5, nama: 'Eka Putri', kelas: '2A', tagihan: 150000, terbayar: 75000, status: 'Kurang', tanggal: '2026-01-11' },
    { id: 6, nama: 'Fajar Ramadhan', kelas: '1B', tagihan: 150000, terbayar: 150000, status: 'Lunas', tanggal: '2026-01-10' },
    { id: 7, nama: 'Gita Maharani', kelas: '3A', tagihan: 150000, terbayar: 50000, status: 'Kurang', tanggal: '2026-01-09' },
]);

// ============================================
// STATE MODAL
// ============================================
const showModal = ref(false);
const isEdit = ref(false);
const currentId = ref(null);

// Form data
const formData = ref({
    nama: '',
    kelas: '',
    tagihan: 0,
    terbayar: 0,
    status: 'Belum Bayar',
    tanggal: new Date().toISOString().split('T')[0]
});

// ============================================
// SEMUA STATISTIK OTOMATIS (COMPUTED)
// ============================================
const stats = computed(() => {
    const totalSantri = transactions.value.length;
    const totalTagihan = transactions.value.reduce((sum, item) => sum + item.tagihan, 0);
    const totalTerbayar = transactions.value.reduce((sum, item) => sum + item.terbayar, 0);
    const totalKurang = totalTagihan - totalTerbayar;
    
    const lunas = transactions.value.filter(item => item.status === 'Lunas').length;
    const belumLunas = transactions.value.filter(item => item.status !== 'Lunas').length;
    
    const persentase = totalTagihan > 0 ? (totalTerbayar / totalTagihan) * 100 : 0;
    
    return {
        totalSantri,
        totalTagihan,
        totalTerbayar,
        totalKurang,
        lunas,
        belumLunas,
        persentase
    };
});

// ============================================
// STATE INTERAKSI
// ============================================
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(5);

// ============================================
// FORMATTERS
// ============================================
const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
};

const getStatusBadge = (status) => {
    const map = {
        'Lunas': 'bg-green-100 text-green-700',
        'Kurang': 'bg-yellow-100 text-yellow-700',
        'Belum Bayar': 'bg-red-100 text-red-700'
    };
    return map[status] || 'bg-gray-100 text-gray-700';
};

// ============================================
// FILTER & PAGINATION
// ============================================
const filteredTransactions = computed(() => {
    if (!searchQuery.value) {
        return transactions.value;
    }
    const query = searchQuery.value.toLowerCase();
    return transactions.value.filter(item => 
        item.nama.toLowerCase().includes(query) ||
        item.kelas.toLowerCase().includes(query)
    );
});

const paginatedTransactions = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredTransactions.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredTransactions.value.length / itemsPerPage.value);
});

// ============================================
// FUNGSI CRUD DENGAN MODAL
// ============================================

// Reset form
const resetForm = () => {
    formData.value = {
        nama: '',
        kelas: '',
        tagihan: 0,
        terbayar: 0,
        status: 'Belum Bayar',
        tanggal: new Date().toISOString().split('T')[0]
    };
};

// Buka modal Tambah
const addTransaction = () => {
    isEdit.value = false;
    currentId.value = null;
    resetForm();
    showModal.value = true;
};

// Buka modal Edit
const editTransaction = (id) => {
    const item = transactions.value.find(t => t.id === id);
    if (item) {
        isEdit.value = true;
        currentId.value = id;
        formData.value = {
            nama: item.nama,
            kelas: item.kelas,
            tagihan: item.tagihan,
            terbayar: item.terbayar,
            status: item.status,
            tanggal: item.tanggal
        };
        showModal.value = true;
    }
};

// Simpan data (Tambah atau Edit)
const saveTransaction = () => {
    // Validasi
    if (!formData.value.nama || !formData.value.kelas || formData.value.tagihan <= 0) {
        alert('⚠️ Nama, Kelas, dan Tagihan harus diisi!');
        return;
    }

    if (isEdit.value) {
        // EDIT: Update data
        const index = transactions.value.findIndex(t => t.id === currentId.value);
        if (index !== -1) {
            transactions.value[index] = {
                id: currentId.value,
                ...formData.value
            };
        }
    } else {
        // TAMBAH: Buat ID baru
        const maxId = transactions.value.reduce((max, item) => Math.max(max, item.id), 0);
        transactions.value.push({
            id: maxId + 1,
            ...formData.value
        });
    }

    // Tutup modal
    showModal.value = false;
    resetForm();
};

// Hapus data
const deleteTransaction = (id) => {
    if (confirm('⚠️ Yakin ingin menghapus data ini?')) {
        const index = transactions.value.findIndex(t => t.id === id);
        if (index !== -1) {
            transactions.value.splice(index, 1);
        }
    }
};

// Tutup modal
const closeModal = () => {
    showModal.value = false;
    resetForm();
};

// Pagination
const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};
</script>

<template>
    <Head title="Keuangan SPP" />

    <DashboardLayout 
        title="Dashboard Utama"
        header-title="💰 Manajemen Keuangan & SPP"
        :header-subtitle="`Total Tagihan: ${formatRupiah(stats.totalTagihan)} | Lunas: ${stats.lunas} Santri`"
    >
        
        <!-- ========================================== -->
        <!-- STATISTIK CARDS (4 KARTU)                  -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Card 1: Total Tagihan -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Tagihan</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ formatRupiah(stats.totalTagihan) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ stats.totalSantri }} santri</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Terbayar -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Terbayar</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ formatRupiah(stats.totalTerbayar) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ stats.lunas }} santri lunas</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card 3: Sisa Tagihan -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Sisa Tagihan</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ formatRupiah(stats.totalKurang) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ stats.belumLunas }} santri belum lunas</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card 4: Total Santri -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Santri</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totalSantri }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            <span class="text-green-600">{{ stats.lunas }}</span> lunas · 
                            <span class="text-red-600">{{ stats.belumLunas }}</span> belum
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PROGRESS RINGKASAN                         -->
        <!-- ========================================== -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Ringkasan Pembayaran</h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ stats.lunas }} dari {{ stats.totalSantri }} santri telah lunas
                    </p>
                </div>
                <span class="text-sm font-semibold text-gray-700">
                    {{ Math.round(stats.persentase) }}% Terbayar
                </span>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                <div 
                    class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500"
                    :style="`width: ${stats.persentase}%`"
                ></div>
            </div>
            
            <div class="flex justify-between text-sm text-gray-600">
                <span>💳 {{ formatRupiah(stats.totalTerbayar) }} terkumpul</span>
                <span>🎯 {{ formatRupiah(stats.totalTagihan) }} target</span>
                <span>📊 {{ stats.belumLunas }} santri tersisa</span>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TABEL TRANSAKSI                           -->
        <!-- ========================================== -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">📋 Transaksi Terbaru</h3>
                    <p class="text-sm text-gray-500">Menampilkan {{ filteredTransactions.length }} dari {{ transactions.length }} transaksi</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Cari santri atau kelas..." 
                            class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-48"
                        >
                    </div>
                    <button 
                        @click="addTransaction"
                        class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition flex items-center justify-center gap-1"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Santri</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terbayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kekurangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="(item, index) in paginatedTransactions" :key="item.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ item.nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ item.kelas }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ formatRupiah(item.tagihan) }}</td>
                            <td class="px-6 py-4 text-sm text-green-600 font-medium">{{ formatRupiah(item.terbayar) }}</td>
                            <td class="px-6 py-4 text-sm text-red-600 font-medium">{{ formatRupiah(item.tagihan - item.terbayar) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getStatusBadge(item.status)">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ item.tanggal }}</td>
                            <td class="px-6 py-4">
                                <button 
                                    @click="editTransaction(item.id)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-2"
                                >
                                    Edit
                                </button>
                                <button 
                                    @click="deleteTransaction(item.id)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="paginatedTransactions.length === 0">
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <svg class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Tidak ada data yang ditemukan</p>
                                <p class="text-sm text-gray-400">Coba ubah kata kunci pencarian</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-3 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-medium">{{ paginatedTransactions.length }}</span> dari 
                    <span class="font-medium">{{ filteredTransactions.length }}</span> data
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
        <!-- INFO TAMBAHAN DI BAWAH                     -->
        <!-- ========================================== -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Periode Aktif</p>
                        <p class="text-xs text-blue-600">Januari - Juni 2026</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-green-800">Tingkat Pembayaran</p>
                        <p class="text-xs text-green-600">{{ Math.round(stats.persentase) }}% dari target</p>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-purple-800">Total Transaksi</p>
                        <p class="text-xs text-purple-600">{{ transactions.length }} transaksi tercatat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH / EDIT                        -->
        <!-- ========================================== -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <!-- Header Modal -->
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ isEdit ? '✏️ Edit Transaksi' : '📝 Tambah Transaksi' }}
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Santri *</label>
                        <input 
                            v-model="formData.nama"
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan nama santri"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas *</label>
                        <input 
                            v-model="formData.kelas"
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: 3A, 2B"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tagihan *</label>
                        <input 
                            v-model.number="formData.tagihan"
                            type="number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan nominal tagihan"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Terbayar</label>
                        <input 
                            v-model.number="formData.terbayar"
                            type="number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan nominal terbayar"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select 
                            v-model="formData.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="Lunas">Lunas</option>
                            <option value="Kurang">Kurang</option>
                            <option value="Belum Bayar">Belum Bayar</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input 
                            v-model="formData.tanggal"
                            type="date" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
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
                        @click="saveTransaction"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition"
                    >
                        {{ isEdit ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>