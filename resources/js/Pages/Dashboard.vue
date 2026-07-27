<script setup>
import { ref, computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

// ============================================
// DATA STATISTIK
// ============================================
const stats = ref([
    { 
        label: 'Total Santri', 
        value: '442',
        icon: 'users',
        change: '+12%',
        changeType: 'increase',
        borderColor: 'border-blue-200',
        bgColor: 'bg-blue-50',
        textColor: 'text-blue-600',
        iconBg: 'bg-blue-100'
    },
    { 
        label: 'Santri Aktif', 
        value: '365',
        icon: 'user-check',
        change: '+5%',
        changeType: 'increase',
        borderColor: 'border-green-200',
        bgColor: 'bg-green-50',
        textColor: 'text-green-600',
        iconBg: 'bg-green-100'
    },
    { 
        label: 'Santri Non-Aktif', 
        value: '77',
        icon: 'user-x',
        change: '-2%',
        changeType: 'decrease',
        borderColor: 'border-red-200',
        bgColor: 'bg-red-50',
        textColor: 'text-red-600',
        iconBg: 'bg-red-100'
    },
    { 
        label: 'Catatan Khusus', 
        value: '102',
        icon: 'file-text',
        change: '+8%',
        changeType: 'increase',
        borderColor: 'border-yellow-200',
        bgColor: 'bg-yellow-50',
        textColor: 'text-yellow-600',
        iconBg: 'bg-yellow-100'
    },
]);

// ============================================
// DATA TRANSAKSI AKADEMIK
// ============================================
const academicStatus = ref([
    { 
        label: 'Naik Kelas', 
        value: 0,
        target: 400,
        percentage: 0,
        icon: 'arrow-up',
        color: 'green'
    },
    { 
        label: 'Tidak Naik Kelas', 
        value: 0,
        target: 42,
        percentage: 0,
        icon: 'arrow-down',
        color: 'red'
    },
]);

// ============================================
// DATA KELAS (RINGKASAN)
// ============================================
const classSummary = ref([
    { name: 'Kelas 1A', total: 28, active: 24, inactive: 4 },
    { name: 'Kelas 1B', total: 26, active: 22, inactive: 4 },
    { name: 'Kelas 2A', total: 30, active: 26, inactive: 4 },
    { name: 'Kelas 2B', total: 27, active: 23, inactive: 4 },
    { name: 'Kelas 3A', total: 29, active: 25, inactive: 4 },
    { name: 'Kelas 3B', total: 28, active: 24, inactive: 4 },
]);

// ============================================
// DATA AKTIVITAS TERBARU
// ============================================
const recentActivities = ref([
    { 
        id: 1, 
        student: 'Ahmad Fauzi', 
        class: '3A', 
        action: 'Menambah Transaksi SPP', 
        time: '2 jam lalu',
        amount: 'Rp 150.000',
        status: 'success'
    },
    { 
        id: 2, 
        student: 'Budi Santoso', 
        class: '2B', 
        action: 'Update Data Santri', 
        time: '4 jam lalu',
        amount: '-',
        status: 'info'
    },
    { 
        id: 3, 
        student: 'Citra Dewi', 
        class: '1A', 
        action: 'Pendaftaran Santri Baru', 
        time: '1 hari lalu',
        amount: '-',
        status: 'warning'
    },
    { 
        id: 4, 
        student: 'Dian Pratama', 
        class: '3B', 
        action: 'Pembayaran SPP', 
        time: '1 hari lalu',
        amount: 'Rp 150.000',
        status: 'success'
    },
    { 
        id: 5, 
        student: 'Eka Putri', 
        class: '2A', 
        action: 'Cuti Akademik', 
        time: '2 hari lalu',
        amount: '-',
        status: 'danger'
    },
]);

// ============================================
// FUNGSI HELPERS
// ============================================
const getStatusColor = (status) => {
    const map = {
        success: 'bg-green-100 text-green-700',
        info: 'bg-blue-100 text-blue-700',
        warning: 'bg-yellow-100 text-yellow-700',
        danger: 'bg-red-100 text-red-700'
    };
    return map[status] || 'bg-gray-100 text-gray-700';
};

const getStatusIcon = (status) => {
    const map = {
        success: '✓',
        info: 'ℹ',
        warning: '⚠',
        danger: '✕'
    };
    return map[status] || '•';
};

// Icon helper
const getIconPath = (icon) => {
    const icons = {
        'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'user-check': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'user-x': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'file-text': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'arrow-up': 'M5 10l7-7m0 0l7 7m-7-7v18',
        'arrow-down': 'M19 14l-7 7m0 0l-7-7m7 7V3'
    };
    return icons[icon] || '';
};

// Format number
const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num);
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
        <!-- STATISTIK CARDS (4 KARTU)                  -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            <div 
                v-for="(stat, idx) in stats" 
                :key="idx" 
                class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all duration-200"
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ stat.label }}</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stat.value }}</p>
                        <div class="flex items-center mt-1">
                            <span 
                                class="text-xs font-medium"
                                :class="stat.changeType === 'increase' ? 'text-green-600' : 'text-red-600'"
                            >
                                {{ stat.change }}
                            </span>
                            <span class="text-xs text-gray-400 ml-1">dari bulan lalu</span>
                        </div>
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
        <!-- 2 KOLOM: STATUS AKADEMIK + CHART          -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Status Transisi Akademik -->
            <div class="lg:col-span-1 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-800">Status Transisi Akademik</h3>
                    <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">2025/2026</span>
                </div>
                
                <div class="space-y-4">
                    <div v-for="(item, idx) in academicStatus" :key="idx" 
                         class="border rounded-lg p-4"
                         :class="item.color === 'green' ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">{{ item.label }}</p>
                                <p class="text-2xl font-bold mt-1" :class="item.color === 'green' ? 'text-green-600' : 'text-red-600'">
                                    {{ item.value }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">Target: {{ formatNumber(item.target) }} santri</p>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold" :class="item.color === 'green' ? 'text-green-600' : 'text-red-600'">
                                    {{ item.percentage }}%
                                </div>
                                <p class="text-xs text-gray-400">realisasi</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="h-1.5 rounded-full transition-all duration-500"
                                 :class="item.color === 'green' ? 'bg-green-500' : 'bg-red-500'"
                                 :style="`width: ${item.percentage}%`">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <button class="w-full mt-4 bg-blue-600 text-white text-sm font-medium py-2 rounded-lg hover:bg-blue-700 transition">
                    Kelola Transisi Akademik →
                </button>
            </div>

            <!-- Grafik / Ringkasan Kelas -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-800">Distribusi Santri per Kelas</h3>
                    <button class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat Detail →</button>
                </div>

                <!-- Bar Chart -->
                <div class="space-y-2.5">
                    <div v-for="(cls, idx) in classSummary" :key="idx" class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-600 w-16">{{ cls.name }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden relative">
                            <div class="absolute inset-0 flex items-center px-3 text-xs text-white font-medium z-10">
                                {{ cls.active }} aktif
                            </div>
                            <div class="h-full bg-green-500 transition-all duration-500" 
                                 :style="`width: ${(cls.active / cls.total) * 100}%`">
                            </div>
                            <div class="h-full bg-red-400 transition-all duration-500" 
                                 :style="`width: ${(cls.inactive / cls.total) * 100}%; margin-left: ${(cls.active / cls.total) * 100}%`">
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 w-10 text-right">{{ cls.total }}</span>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded bg-green-500"></div>
                        <span class="text-xs text-gray-600">Aktif</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded bg-red-400"></div>
                        <span class="text-xs text-gray-600">Non-Aktif</span>
                    </div>
                    <div class="flex items-center gap-1.5 ml-auto">
                        <span class="text-xs text-gray-400">Total Santri</span>
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
                <button class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat Semua →</button>
            </div>

            <div class="divide-y divide-gray-100">
                <div v-for="activity in recentActivities" :key="activity.id" 
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
                            <p class="text-sm font-medium text-gray-800">{{ activity.student }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">{{ activity.action }}</span>
                                <span class="text-xs text-gray-300">•</span>
                                <span class="text-xs text-gray-400">{{ activity.class }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span v-if="activity.amount !== '-'" class="text-sm font-medium text-green-600">
                            {{ activity.amount }}
                        </span>
                        <span class="text-xs text-gray-400">{{ activity.time }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <span class="text-xs text-gray-400">Menampilkan 5 aktivitas terakhir</span>
                <span class="text-xs text-gray-400">Terakhir diperbarui: 5 menit lalu</span>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- INFO CARD TAMBAHAN                         -->
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
                        <p class="text-xs text-green-700 font-medium">Tingkat Kehadiran</p>
                        <p class="text-sm font-bold text-green-800">94.2%</p>
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
                        <p class="text-sm font-bold text-purple-800">24 Orang</p>
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
                        <p class="text-xs text-orange-700 font-medium">Jam Belajar</p>
                        <p class="text-sm font-bold text-orange-800">2.400 Jam</p>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>