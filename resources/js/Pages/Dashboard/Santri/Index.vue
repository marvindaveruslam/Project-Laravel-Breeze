<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    santris: Array,
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
// DATA REAKTIF
// ============================================
const santris = ref(props.santris || []);
const kelasList = ref(props.kelas || []);

// ============================================
// FUNGSI HELPER UNTUK JENIS KELAMIN
// ============================================
const isLakiLaki = (val) => {
    if (val === null || val === undefined) return false;
    const str = String(val).toLowerCase().trim();
    return ['1', 'l', 'laki', 'laki-laki', 'laki laki', 'male', 'm'].includes(str);
};

const isPerempuan = (val) => {
    if (val === null || val === undefined) return false;
    const str = String(val).toLowerCase().trim();
    return ['2', 'p', 'perempuan', 'female', 'f'].includes(str);
};

// ============================================
// STATISTIK OTOMATIS - PERBAIKAN
// ============================================
const stats = computed(() => {
    const totalSantri = santris.value.length;
    const lakiLaki = santris.value.filter(s => isLakiLaki(s.jenis_kelamin)).length;
    const perempuan = santris.value.filter(s => isPerempuan(s.jenis_kelamin)).length;
    
    // Hitung berdasarkan kelas
    const kelasCount = {};
    santris.value.forEach(s => {
        const namaKelas = s.kelas?.nama_kelas || 'Tanpa Kelas';
        kelasCount[namaKelas] = (kelasCount[namaKelas] || 0) + 1;
    });
    
    // Debug
    console.log('📊 STATISTIK DATA SANTRI:');
    console.log('Total:', totalSantri);
    console.log('Laki-laki:', lakiLaki);
    console.log('Perempuan:', perempuan);
    console.log('Data mentah:', santris.value.map(s => ({
        nama: s.nama,
        jenis_kelamin: s.jenis_kelamin,
        tipe: typeof s.jenis_kelamin
    })));
    
    return {
        totalSantri,
        lakiLaki,
        perempuan,
        kelasCount
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
const isLoading = ref(false);
const filterKelas = ref('');

// Form data - SESUAI DENGAN MODEL
const formData = ref({
    nis: '',
    nama: '',
    jenis_kelamin: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    alamat: '',
    no_hp: '',
    kelas_id: '',
});

// ============================================
// FILTER & PAGINATION
// ============================================
const filteredSantris = computed(() => {
    let data = santris.value;

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        data = data.filter(item =>
            item.nama?.toLowerCase().includes(query) ||
            item.nis?.toLowerCase().includes(query)
        );
    }

    // Filter by kelas
    if (filterKelas.value) {
        data = data.filter(item => item.kelas_id == filterKelas.value);
    }

    return data;
});

const paginatedSantris = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredSantris.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredSantris.value.length / itemsPerPage.value);
});

// ============================================
// HELPERS - PERBAIKAN
// ============================================
const getInitials = (name) => {
    if (!name) return 'S';
    return name.split(' ').map(word => word[0]).join('').toUpperCase();
};

// ✅ PERBAIKAN: Support multiple format
const getJenisKelaminLabel = (jk) => {
    if (jk === null || jk === undefined || jk === '') {
        return '-';
    }
    
    if (isLakiLaki(jk)) {
        return 'Laki-laki';
    }
    if (isPerempuan(jk)) {
        return 'Perempuan';
    }
    
    console.warn('⚠️ Nilai jenis_kelamin tidak dikenal:', jk);
    return String(jk);
};

// ✅ PERBAIKAN: Class binding support multiple format
const getJenisKelaminClass = (jk) => {
    if (isLakiLaki(jk)) {
        return 'text-blue-600';
    }
    if (isPerempuan(jk)) {
        return 'text-pink-600';
    }
    return 'text-gray-600';
};

const getKelasName = (kelasId) => {
    const kelas = kelasList.value.find(k => k.id == kelasId);
    return kelas ? kelas.nama_kelas : '-';
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const resetFilters = () => {
    searchQuery.value = '';
    filterKelas.value = '';
    currentPage.value = 1;
};

// ============================================
// FUNGSI CRUD DENGAN TOAST
// ============================================
const resetForm = () => {
    formData.value = {
        nis: '',
        nama: '',
        jenis_kelamin: '',
        tempat_lahir: '',
        tanggal_lahir: '',
        alamat: '',
        no_hp: '',
        kelas_id: '',
    };
};

const addSantri = () => {
    isEdit.value = false;
    currentId.value = null;
    resetForm();
    showModal.value = true;
};

const editSantri = (id) => {
    const item = santris.value.find(s => s.id === id);
    if (item) {
        isEdit.value = true;
        currentId.value = id;
        formData.value = {
            nis: item.nis || '',
            nama: item.nama || '',
            jenis_kelamin: item.jenis_kelamin || '',
            tempat_lahir: item.tempat_lahir || '',
            tanggal_lahir: item.tanggal_lahir || '',
            alamat: item.alamat || '',
            no_hp: item.no_hp || '',
            kelas_id: item.kelas_id || '',
        };
        showModal.value = true;
    }
};

const saveSantri = () => {
    if (!formData.value.nis || !formData.value.nama || !formData.value.jenis_kelamin) {
        Toast.fire({
            icon: 'warning',
            title: '⚠️ NIS, Nama, dan Jenis Kelamin wajib diisi!'
        });
        return;
    }

    isLoading.value = true;

    if (isEdit.value) {
        router.put(route('santri.update', currentId.value), formData.value, {
            onSuccess: () => {
                showModal.value = false;
                resetForm();
                isLoading.value = false;
                
                Toast.fire({
                    icon: 'success',
                    title: '✨ Data santri berhasil diperbarui'
                });
            },
            onError: (errors) => {
                console.error('Error update:', errors);
                isLoading.value = false;
                
                let errorMsg = '❌ Gagal memperbarui data!';
                if (errors && typeof errors === 'object') {
                    const messages = Object.values(errors).flat();
                    if (messages.length > 0) {
                        errorMsg = messages.join('\n');
                    }
                }
                Toast.fire({
                    icon: 'error',
                    title: errorMsg
                });
            }
        });
    } else {
        router.post(route('santri.store'), formData.value, {
            onSuccess: () => {
                showModal.value = false;
                resetForm();
                isLoading.value = false;
                
                Toast.fire({
                    icon: 'success',
                    title: '🎉 Data santri berhasil ditambahkan'
                });
            },
            onError: (errors) => {
                console.error('Error save:', errors);
                isLoading.value = false;
                
                let errorMsg = '❌ Gagal menyimpan data!';
                if (errors && typeof errors === 'object') {
                    const messages = Object.values(errors).flat();
                    if (messages.length > 0) {
                        errorMsg = messages.join('\n');
                    }
                }
                Toast.fire({
                    icon: 'error',
                    title: errorMsg
                });
            }
        });
    }
};

const deleteSantri = (id) => {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data santri yang dihapus tidak dapat dikembalikan!',
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
            router.delete(route('santri.destroy', id), {
                onSuccess: () => {
                    isLoading.value = false;
                    
                    Toast.fire({
                        icon: 'success',
                        title: '🗑️ Data santri berhasil dihapus'
                    });
                },
                onError: (errors) => {
                    console.error('Error delete:', errors);
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
// WATCH UNTUK UPDATE DATA
// ============================================
watch(() => props.santris, (newData) => {
    santris.value = newData || [];
});

watch(() => props.kelas, (newData) => {
    kelasList.value = newData || [];
});

// ============================================
// FLASH MESSAGE DARI SERVER
// ============================================
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
    <Head title="Data Santri" />

    <DashboardLayout 
        title="Data Santri"
        header-title="👨‍🎓 Data Santri"
        :header-subtitle="`Total Santri: ${stats.totalSantri} | L: ${stats.lakiLaki} | P: ${stats.perempuan}`"
    >
        <!-- ========================================== -->
        <!-- STATISTIK CARDS (4 KARTU)                  -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Santri</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totalSantri }}</p>
                        <p class="text-xs text-gray-400 mt-1">Semua santri</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Laki-laki</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ stats.lakiLaki }}</p>
                        <p class="text-xs text-gray-400 mt-1">Santri putra</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Perempuan</p>
                        <p class="text-2xl font-bold text-pink-600 mt-1">{{ stats.perempuan }}</p>
                        <p class="text-xs text-gray-400 mt-1">Santri putri</p>
                    </div>
                    <div class="bg-pink-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Kelas</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ Object.keys(stats.kelasCount).length }}</p>
                        <p class="text-xs text-gray-400 mt-1">Total kelas terisi</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TABEL SANTRI                              -->
        <!-- ========================================== -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Tabel -->
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">📋 Daftar Santri</h3>
                    <p class="text-sm text-gray-500">Menampilkan {{ filteredSantris.length }} dari {{ santris.length }} santri</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Cari nama atau NIS..." 
                            class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-48"
                        >
                    </div>
                    <select 
                        v-model="filterKelas"
                        class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Semua Kelas</option>
                        <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
                            {{ kelas.nama_kelas }}
                        </option>
                    </select>
                    <button 
                        @click="resetFilters"
                        class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                        title="Reset Filter"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <button 
                        @click="addSantri"
                        :disabled="isLoading"
                        class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition flex items-center justify-center gap-1 disabled:opacity-50"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Santri
                    </button>
                </div>
            </div>

            <!-- Tabel -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No HP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="(item, index) in paginatedSantris" :key="item.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ item.nis }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                        {{ getInitials(item.nama) }}
                                    </div>
                                    {{ item.nama }}
                                </div>
                            </td>
                            <!-- ✅ PERBAIKAN: Pakai fungsi getJenisKelaminClass -->
                            <td class="px-6 py-4 text-sm">
                                <span :class="getJenisKelaminClass(item.jenis_kelamin)">
                                    {{ getJenisKelaminLabel(item.jenis_kelamin) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span v-if="item.kelas" class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">
                                    {{ item.kelas.nama_kelas }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ item.no_hp || '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <button 
                                        @click="editSantri(item.id)"
                                        :disabled="isLoading"
                                        class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition disabled:opacity-50"
                                        title="Edit Santri"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button 
                                        @click="deleteSantri(item.id)"
                                        :disabled="isLoading"
                                        class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition disabled:opacity-50"
                                        title="Hapus Santri"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="paginatedSantris.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <svg class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Tidak ada data santri ditemukan</p>
                                <p class="text-sm text-gray-400">Coba ubah filter atau tambah data baru</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-medium">{{ paginatedSantris.length }}</span> dari 
                    <span class="font-medium">{{ filteredSantris.length }}</span> data
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
        <!-- INFO CARD TAMBAHAN                         -->
        <!-- ========================================== -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-200 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 font-medium">Total Santri</p>
                        <p class="text-sm font-bold text-blue-800">{{ stats.totalSantri }} Santri</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center gap-3">
                    <div class="bg-green-200 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-green-700 font-medium">Laki-laki</p>
                        <p class="text-sm font-bold text-green-800">{{ stats.lakiLaki }} Santri</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-200 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-purple-700 font-medium">Perempuan</p>
                        <p class="text-sm font-bold text-purple-800">{{ stats.perempuan }} Santri</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH / EDIT SANTRI                 -->
        <!-- ========================================== -->
        <div 
            v-if="showModal" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="closeModal"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <!-- Header Modal -->
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ isEdit ? '✏️ Edit Santri' : '📝 Tambah Santri' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIS *</label>
                            <input 
                                v-model="formData.nis"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nomor Induk Santri"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input 
                                v-model="formData.nama"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nama lengkap santri"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                            <select 
                                v-model="formData.jenis_kelamin"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Pilih</option>
                                <option value="1">Laki-laki</option>
                                <option value="2">Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                            <select 
                                v-model="formData.kelas_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Pilih Kelas</option>
                                <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
                                    {{ kelas.nama_kelas }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input 
                                v-model="formData.tempat_lahir"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Tempat lahir"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input 
                                v-model="formData.tanggal_lahir"
                                type="date" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea 
                                v-model="formData.alamat"
                                rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Alamat lengkap santri"
                            ></textarea>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                            <input 
                                v-model="formData.no_hp"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nomor handphone"
                            >
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button 
                        @click="closeModal"
                        :disabled="isLoading"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition disabled:opacity-50"
                    >
                        Batal
                    </button>
                    <button 
                        @click="saveSantri"
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