<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    santris: Array,
    gurus: Array,
    kelas: Array,
    stats: Object,
    recent_activities: Array,
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
const gurus = ref(props.gurus || []);
const kelas = ref(props.kelas || []);
const recentActivities = ref(props.recent_activities || []);

// ============================================
// MODAL STATE
// ============================================
const showSantriModal = ref(false);
const showGuruModal = ref(false);
const isSubmitting = ref(false);

// ============================================
// FORM TAMBAH SANTRI
// ============================================
const formSantri = ref({
    nis: '',
    nama: '',
    jenis_kelamin: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    alamat: '',
    no_hp: '',
    kelas_id: '',
});

const resetFormSantri = () => {
    formSantri.value = {
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

// ============================================
// FORM TAMBAH GURU
// ============================================
const formGuru = ref({
    nama: '',
    nip: '',
    jenis_kelamin: '',
    no_hp: '',
    alamat: '',
});

const resetFormGuru = () => {
    formGuru.value = {
        nama: '',
        nip: '',
        jenis_kelamin: '',
        no_hp: '',
        alamat: '',
    };
};

// ============================================
// STATISTIK CARDS
// ============================================
const statsCards = computed(() => {
    const s = props.stats || {};
    return [
        { 
            label: 'Total Santri', 
            value: s.total_santri || 0,
            icon: 'users',
            bgColor: 'bg-blue-50',
            textColor: 'text-blue-600',
            iconBg: 'bg-blue-100'
        },
        { 
            label: 'Santri Laki-laki', 
            value: s.laki_laki || 0,
            icon: 'user-check',
            bgColor: 'bg-green-50',
            textColor: 'text-green-600',
            iconBg: 'bg-green-100'
        },
        { 
            label: 'Santri Perempuan', 
            value: s.perempuan || 0,
            icon: 'user-x',
            bgColor: 'bg-pink-50',
            textColor: 'text-pink-600',
            iconBg: 'bg-pink-100'
        },
        { 
            label: 'Total Guru', 
            value: s.total_guru || 0,
            icon: 'file-text',
            bgColor: 'bg-purple-50',
            textColor: 'text-purple-600',
            iconBg: 'bg-purple-100'
        },
    ];
});

// ============================================
// SEARCH
// ============================================
const searchQuery = ref('');
const searchResults = ref([]);

const searchAll = () => {
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        return;
    }

    const query = searchQuery.value.toLowerCase().trim();
    
    const santriResults = santris.value.filter(s => 
        s.nama?.toLowerCase().includes(query) ||
        s.nis?.toLowerCase().includes(query) ||
        s.kelas?.nama_kelas?.toLowerCase().includes(query)
    ).map(s => ({ ...s, type: 'santri' }));

    const guruResults = gurus.value.filter(g =>
        g.nama?.toLowerCase().includes(query)
    ).map(g => ({ ...g, type: 'guru' }));

    searchResults.value = [...santriResults, ...guruResults];
};

const clearSearch = () => {
    searchQuery.value = '';
    searchResults.value = [];
};

// ============================================
// FUNGSI TAMBAH SANTRI
// ============================================
const openSantriModal = () => {
    resetFormSantri();
    showSantriModal.value = true;
};

const submitSantri = () => {
    // Validasi
    if (!formSantri.value.nis || !formSantri.value.nama || !formSantri.value.jenis_kelamin) {
        Toast.fire({
            icon: 'warning',
            title: '⚠️ NIS, Nama, dan Jenis Kelamin wajib diisi!'
        });
        return;
    }

    isSubmitting.value = true;

    router.post(route('santri.store'), formSantri.value, {
        onSuccess: () => {
            showSantriModal.value = false;
            resetFormSantri();
            isSubmitting.value = false;
            
            Toast.fire({
                icon: 'success',
                title: '🎉 Santri berhasil ditambahkan!'
            });
            
            setTimeout(() => {
                router.visit(route('dashboard.santri'));
            }, 1000);
        },
        onError: (errors) => {
            isSubmitting.value = false;
            console.error('Error:', errors);
            
            let errorMsg = '❌ Gagal menambahkan santri!';
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
};

// ============================================
// FUNGSI TAMBAH GURU
// ============================================
const openGuruModal = () => {
    resetFormGuru();
    showGuruModal.value = true;
};

const submitGuru = () => {
    // Validasi
    if (!formGuru.value.nama || !formGuru.value.jenis_kelamin) {
        Toast.fire({
            icon: 'warning',
            title: '⚠️ Nama dan Jenis Kelamin wajib diisi!'
        });
        return;
    }

    isSubmitting.value = true;

    router.post(route('guru.store'), formGuru.value, {
        onSuccess: () => {
            showGuruModal.value = false;
            resetFormGuru();
            isSubmitting.value = false;
            
            Toast.fire({
                icon: 'success',
                title: '🎉 Guru berhasil ditambahkan!'
            });
            
            setTimeout(() => {
                router.visit(route('dashboard.guru'));
            }, 1000);
        },
        onError: (errors) => {
            isSubmitting.value = false;
            console.error('Error:', errors);
            
            let errorMsg = '❌ Gagal menambahkan guru!';
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
};

// ============================================
// NAVIGASI
// ============================================
const goToSantri = () => {
    router.visit(route('dashboard.santri'));
};

const goToGuru = () => {
    router.visit(route('dashboard.guru'));
};

const goToKelas = () => {
    router.visit(route('dashboard.classes'));
};

// ============================================
// HELPERS
// ============================================
const getStatusBadge = (type) => {
    return type === 'santri' 
        ? 'bg-blue-100 text-blue-700' 
        : 'bg-purple-100 text-purple-700';
};

const getIconPath = (icon) => {
    const icons = {
        'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'user-check': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'user-x': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'file-text': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'search': 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'x': 'M6 18L18 6M6 6l12 12',
        'plus': 'M12 4v16m8-8H4'
    };
    return icons[icon] || '';
};

const getJenisKelaminLabel = (jk) => {
    if (jk === 'L' || jk === '1') return 'Laki-laki';
    if (jk === 'P' || jk === '2') return 'Perempuan';
    return jk || '-';
};
</script>

<template>
    <Head title="Dashboard" />

    <DashboardLayout 
        title="Dashboard Utama"
        header-title="📊 Dashboard Utama"
        header-subtitle="Selamat datang kembali, Adi | Pimpinan"
    >
        <!-- ========================================== -->
        <!-- SEARCH BAR                                -->
        <!-- ========================================== -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('search')" />
                        </svg>
                    </div>
                    <input 
                        v-model="searchQuery"
                        @input="searchAll"
                        type="text"
                        placeholder="Cari nama santri, guru, atau kelas..."
                        class="w-full pl-10 pr-24 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm text-sm"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                        <span v-if="searchQuery" class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                            {{ searchResults.length }} hasil
                        </span>
                        <button 
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="p-1 hover:bg-gray-100 rounded-full transition"
                        >
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('x')" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button 
                        @click="openSantriModal"
                        class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition shadow-sm flex items-center gap-2 whitespace-nowrap text-sm"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('plus')" />
                        </svg>
                        Tambah Santri
                    </button>
                    <button 
                        @click="openGuruModal"
                        class="px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-xl transition shadow-sm flex items-center gap-2 whitespace-nowrap text-sm"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('plus')" />
                        </svg>
                        Tambah Guru
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEARCH RESULTS                            -->
        <!-- ========================================== -->
        <div v-if="searchResults.length > 0" class="mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-700">
                        Hasil Pencarian "{{ searchQuery }}"
                        <span class="text-xs font-normal text-gray-400 ml-2">({{ searchResults.length }} ditemukan)</span>
                    </h4>
                </div>
                
                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                    <div v-for="result in searchResults" :key="result.id + result.type" 
                         class="px-6 py-3 hover:bg-gray-50 transition cursor-pointer"
                         @click="result.type === 'santri' ? goToSantri() : goToGuru()">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                     :class="result.type === 'santri' ? 'bg-blue-500' : 'bg-purple-500'">
                                    {{ result.type === 'santri' ? 'S' : 'G' }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-gray-800">{{ result.nama || result.name }}</p>
                                        <span class="text-xs px-2 py-0.5 rounded-full" :class="getStatusBadge(result.type)">
                                            {{ result.type === 'santri' ? 'Santri' : 'Guru' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                                        <span v-if="result.type === 'santri'">
                                            Kelas: <span class="font-medium text-gray-700">{{ result.kelas?.nama_kelas || '-' }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">Klik untuk detail →</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- STATISTIK CARDS                           -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            <div 
                v-for="(stat, idx) in statsCards" 
                :key="idx" 
                class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all duration-200"
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ stat.label }}</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stat.value }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" :class="stat.iconBg">
                        <svg class="h-6 w-6" :class="stat.textColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(stat.icon)" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- DAFTAR KELAS                              -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">📚 Daftar Kelas</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Total {{ kelas.length }} kelas</p>
                    </div>
                    <button @click="goToKelas" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Semua →
                    </button>
                </div>

                <div class="divide-y divide-gray-100">
                    <div v-for="cls in kelas" :key="cls.id" 
                         class="px-6 py-4 hover:bg-gray-50 transition cursor-pointer"
                         @click="goToKelas">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                    {{ cls.nama_kelas }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-semibold text-gray-800">{{ cls.nama_kelas }}</span>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                            {{ cls.santris_count || 0 }} Santri
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-500">Tingkat {{ cls.tingkat }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">Klik untuk detail →</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- AKTIVITAS TERBARU                         -->
        <!-- ========================================== -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">🕐 Aktivitas Terbaru</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Update aktivitas terkini di sistem</p>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                <div v-for="activity in recentActivities" :key="activity.name + activity.time" 
                     class="px-6 py-3 hover:bg-gray-50 transition flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full flex-shrink-0"
                             :class="{
                                 'bg-green-500': activity.status === 'success',
                                 'bg-blue-500': activity.status === 'info',
                                 'bg-yellow-500': activity.status === 'warning',
                                 'bg-red-500': activity.status === 'danger'
                             }">
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ activity.name }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">{{ activity.action }}</span>
                                <span class="text-xs text-gray-300">•</span>
                                <span class="text-xs text-gray-400">{{ activity.class }}</span>
                            </div>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">{{ activity.time }}</span>
                </div>
                <div v-if="recentActivities.length === 0" class="px-6 py-8 text-center text-gray-400">
                    <p>Belum ada aktivitas</p>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- INFO CARD                                 -->
        <!-- ========================================== -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-green-700 font-medium">Total Kelas</p>
                        <p class="text-sm font-bold text-green-800">{{ stats.total_kelas || 0 }} Kelas</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-200 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-purple-700 font-medium">Total Guru</p>
                        <p class="text-sm font-bold text-purple-800">{{ stats.total_guru || 0 }} Orang</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                <div class="flex items-center gap-3">
                    <div class="bg-orange-200 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-orange-700 font-medium">Total Santri</p>
                        <p class="text-sm font-bold text-orange-800">{{ stats.total_santri || 0 }} Santri</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH SANTRI                       -->
        <!-- ========================================== -->
        <div v-if="showSantriModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showSantriModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-auto p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">📝 Tambah Santri Baru</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Isi data santri dengan lengkap</p>
                        </div>
                        <button @click="showSantriModal = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitSantri" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIS <span class="text-red-500">*</span></label>
                                <input v-model="formSantri.nis" type="text" placeholder="Nomor Induk" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input v-model="formSantri.nama" type="text" placeholder="Nama lengkap" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select v-model="formSantri.jenis_kelamin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <option value="">Pilih</option>
                                    <option value="1">Laki-laki</option>
                                    <option value="2">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                                <select v-model="formSantri.kelas_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <option value="">Pilih Kelas</option>
                                    <option v-for="k in kelas" :key="k.id" :value="k.id">
                                        {{ k.nama_kelas }} (Tingkat {{ k.tingkat }})
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                                <input v-model="formSantri.tempat_lahir" type="text" placeholder="Tempat lahir" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                <input v-model="formSantri.tanggal_lahir" type="date" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea v-model="formSantri.alamat" rows="2" placeholder="Alamat lengkap" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                            <input v-model="formSantri.no_hp" type="text" placeholder="Nomor handphone" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showSantriModal = false" 
                                    class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmitting" 
                                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Santri' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH GURU                         -->
        <!-- ========================================== -->
        <div v-if="showGuruModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showGuruModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-auto p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">👨‍🏫 Tambah Guru</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Isi data guru dengan lengkap</p>
                        </div>
                        <button @click="showGuruModal = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitGuru" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="formGuru.nama" 
                                type="text" 
                                placeholder="Nama lengkap guru" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                                required 
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                NIP <span class="text-gray-400 text-xs">(opsional)</span>
                            </label>
                            <input 
                                v-model="formGuru.nip" 
                                type="text" 
                                placeholder="Nomor Induk Pegawai" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select 
                                v-model="formGuru.jenis_kelamin" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                                required
                            >
                                <option value="">Pilih</option>
                                <option value="1">Laki-laki</option>
                                <option value="2">Perempuan</option>
                            </select>
                        </div>  

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                No HP <span class="text-gray-400 text-xs">(opsional)</span>
                            </label>
                            <input 
                                v-model="formGuru.no_hp" 
                                type="text" 
                                placeholder="Nomor handphone" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat <span class="text-gray-400 text-xs">(opsional)</span>
                            </label>
                            <textarea 
                                v-model="formGuru.alamat" 
                                rows="2" 
                                placeholder="Alamat lengkap guru" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            ></textarea>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                            <button 
                                type="button" 
                                @click="showGuruModal = false" 
                                class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                :disabled="isSubmitting" 
                                class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>