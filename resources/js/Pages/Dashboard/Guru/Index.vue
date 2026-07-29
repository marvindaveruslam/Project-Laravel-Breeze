<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    gurus: Array,
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
const gurus = ref(props.gurus || []);

// ============================================
// STATISTIK
// ============================================
const stats = computed(() => {
    const totalGuru = gurus.value.length;
    const lakiLaki = gurus.value.filter(g => g.jenis_kelamin == '1' || g.jenis_kelamin == 'L').length;
    const perempuan = gurus.value.filter(g => g.jenis_kelamin == '2' || g.jenis_kelamin == 'P').length;
    
    return {
        totalGuru,
        lakiLaki,
        perempuan
    };
});

// ============================================
// STATE
// ============================================
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(5);
const showModal = ref(false);
const isEdit = ref(false);
const currentId = ref(null);
const isLoading = ref(false);

const formData = ref({
    nama: '',
    nip: '',
    jenis_kelamin: '',
    no_hp: '',
    alamat: '',
});

// ============================================
// FILTER & PAGINATION
// ============================================
const filteredGurus = computed(() => {
    if (!searchQuery.value) {
        return gurus.value;
    }
    const query = searchQuery.value.toLowerCase();
    return gurus.value.filter(item =>
        item.nama?.toLowerCase().includes(query) ||
        item.nip?.toLowerCase().includes(query)
    );
});

const paginatedGurus = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredGurus.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredGurus.value.length / itemsPerPage.value);
});

// ============================================
// HELPERS
// ============================================
const getInitials = (name) => {
    if (!name) return 'G';
    return name.split(' ').map(word => word[0]).join('').toUpperCase();
};

const getJenisKelaminLabel = (jk) => {
    if (jk == '1' || jk == 1) return 'Laki-laki';
    if (jk == '2' || jk == 2) return 'Perempuan';
    if (jk === 'L') return 'Laki-laki';
    if (jk === 'P') return 'Perempuan';
    return '-';
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

// ============================================
// CRUD FUNCTIONS - SAMA SEPERTI KELAS
// ============================================
const resetForm = () => {
    formData.value = {
        nama: '',
        nip: '',
        jenis_kelamin: '',
        no_hp: '',
        alamat: '',
    };
};

const addGuru = () => {
    isEdit.value = false;
    currentId.value = null;
    resetForm();
    showModal.value = true;
};

const editGuru = (id) => {
    const item = gurus.value.find(g => g.id === id);
    if (item) {
        isEdit.value = true;
        currentId.value = id;
        formData.value = {
            nama: item.nama || '',
            nip: item.nip || '',
            jenis_kelamin: item.jenis_kelamin || '',
            no_hp: item.no_hp || '',
            alamat: item.alamat || '',
        };
        showModal.value = true;
    }
};

const saveGuru = () => {
    if (!formData.value.nama || !formData.value.jenis_kelamin) {
        Toast.fire({
            icon: 'warning',
            title: '⚠️ Nama dan Jenis Kelamin wajib diisi!'
        });
        return;
    }

    isLoading.value = true;

    if (isEdit.value) {
        // ✅ SAMA SEPERTI KELAS: PAKAI ROUTE GURU.UPDATE
        router.put(route('guru.update', currentId.value), formData.value, {
            onSuccess: () => {
                showModal.value = false;
                resetForm();
                isLoading.value = false;
                
                // ✅ Redirect ke dashboard.guru (sama seperti kelas)
                router.get(route('dashboard.guru'));
                
                Toast.fire({
                    icon: 'success',
                    title: '✨ Data guru berhasil diperbarui'
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
        // ✅ SAMA SEPERTI KELAS: PAKAI ROUTE GURU.STORE
        router.post(route('guru.store'), formData.value, {
            onSuccess: () => {
                showModal.value = false;
                resetForm();
                isLoading.value = false;
                
                // ✅ Redirect ke dashboard.guru (sama seperti kelas)
                router.get(route('dashboard.guru'));
                
                Toast.fire({
                    icon: 'success',
                    title: '🎉 Data guru berhasil ditambahkan'
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

const deleteGuru = (id) => {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data guru yang dihapus tidak dapat dikembalikan!',
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
            // ✅ SAMA SEPERTI KELAS: PAKAI ROUTE GURU.DESTROY
            router.delete(route('guru.destroy', id), {
                onSuccess: () => {
                    isLoading.value = false;
                    
                    // ✅ Redirect ke dashboard.guru (sama seperti kelas)
                    router.get(route('dashboard.guru'));
                    
                    Toast.fire({
                        icon: 'success',
                        title: '🗑️ Data guru berhasil dihapus'
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
// WATCH
// ============================================
watch(() => props.gurus, (newData) => {
    console.log('Data guru updated:', newData);
    gurus.value = newData || [];
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
    <Head title="Data Guru" />

    <DashboardLayout 
        title="Data Guru"
        header-title="👨‍🏫 Data Guru"
        :header-subtitle="`Total Guru: ${stats.totalGuru} | L: ${stats.lakiLaki} | P: ${stats.perempuan}`"
    >
        <!-- STATISTIK CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Guru</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totalGuru }}</p>
                        <p class="text-xs text-gray-400 mt-1">Semua guru</p>
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
                        <p class="text-sm text-gray-500 font-medium">Laki-laki</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ stats.lakiLaki }}</p>
                        <p class="text-xs text-gray-400 mt-1">Guru putra</p>
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
                        <p class="text-xs text-gray-400 mt-1">Guru putri</p>
                    </div>
                    <div class="bg-pink-100 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL GURU -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">📋 Daftar Guru</h3>
                    <p class="text-sm text-gray-500">Menampilkan {{ filteredGurus.length }} dari {{ gurus.length }} guru</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Cari nama atau NIP..." 
                            class="pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-48"
                        >
                    </div>
                    <button 
                        @click="addGuru"
                        :disabled="isLoading"
                        class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition flex items-center justify-center gap-1 disabled:opacity-50"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Guru
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No HP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="(item, index) in paginatedGurus" :key="item.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                        {{ getInitials(item.nama) }}
                                    </div>
                                    {{ item.nama }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ item.nip || '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span :class="item.jenis_kelamin == '1' || item.jenis_kelamin == 'L' ? 'text-blue-600' : 'text-pink-600'">
                                    {{ getJenisKelaminLabel(item.jenis_kelamin) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ item.no_hp || '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(item.created_at) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <button 
                                        @click="editGuru(item.id)"
                                        :disabled="isLoading"
                                        class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition disabled:opacity-50"
                                        title="Edit Guru"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button 
                                        @click="deleteGuru(item.id)"
                                        :disabled="isLoading"
                                        class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition disabled:opacity-50"
                                        title="Hapus Guru"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="paginatedGurus.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <svg class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Tidak ada data guru ditemukan</p>
                                <p class="text-sm text-gray-400">Coba ubah kata kunci pencarian</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-medium">{{ paginatedGurus.length }}</span> dari 
                    <span class="font-medium">{{ filteredGurus.length }}</span> data
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

        <!-- INFO CARD -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-200 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 font-medium">Total Guru</p>
                        <p class="text-sm font-bold text-blue-800">{{ stats.totalGuru }} Guru</p>
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
                        <p class="text-sm font-bold text-green-800">{{ stats.lakiLaki }} Guru</p>
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
                        <p class="text-sm font-bold text-purple-800">{{ stats.perempuan }} Guru</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL -->
        <div 
            v-if="showModal" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="closeModal"
        >
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ isEdit ? '✏️ Edit Guru' : '📝 Tambah Guru' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input 
                                v-model="formData.nama"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nama lengkap guru"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                            <input 
                                v-model="formData.nip"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nomor Induk Pegawai"
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

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                            <input 
                                v-model="formData.no_hp"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nomor handphone"
                            >
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea 
                                v-model="formData.alamat"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Alamat lengkap guru"
                            ></textarea>
                        </div>
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
                        @click="saveGuru"
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