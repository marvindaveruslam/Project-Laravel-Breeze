<!-- resources/js/Pages/Dashboard/Classes/SingleClass.vue -->
<script setup>
import { ref, computed, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
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
// DATA REAKTIF
// ============================================
const classes = ref(props.kelas || []);

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

// ============================================
// FUNGSI CRUD
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
                                    <!-- Tombol Detail -> ke halaman Detail Kelas -->
                                    <Link 
                                        :href="`/dashboard/classes/${item.id}/detail`"
                                        class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition"
                                        title="Detail Kelas"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                    <!-- Tombol Edit -->
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
                                    <!-- Tombol Hapus -->
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