<script setup>
import { ref } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

// Data statistik dengan warna
const stats = ref([
    { 
        label: 'TOTAL SANTRI', 
        value: '442',
        borderColor: 'border-blue-200',
        bgColor: 'bg-blue-50',
        textColor: 'text-blue-600'
    },
    { 
        label: 'SANTRI AKTIF', 
        value: '365',
        borderColor: 'border-green-200',
        bgColor: 'bg-green-50',
        textColor: 'text-green-600'
    },
    { 
        label: 'TIDAK AKTIF', 
        value: '77',
        borderColor: 'border-red-200',
        bgColor: 'bg-red-50',
        textColor: 'text-red-600'
    },
    { 
        label: 'CATATAN', 
        value: '102',
        borderColor: 'border-yellow-200',
        bgColor: 'bg-yellow-50',
        textColor: 'text-yellow-600'
    },
]);
</script>

<template>
    <Head title="Dashboard" />

    <DashboardLayout title="Dashboard Utama">
        <template #default>
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div 
                    v-for="(stat, idx) in stats" 
                    :key="idx" 
                    class="border rounded-lg p-4 text-center"
                    :class="[stat.borderColor, stat.bgColor]"
                >
                    <div class="text-sm text-gray-600">{{ stat.label }}</div>
                    <div class="text-xl font-bold mt-1" :class="stat.textColor">{{ stat.value }}</div>
                    <div class="text-xs text-gray-400 mt-1">Santri</div>
                </div>
            </div>

            <!-- Status Transisi Akademik -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Transisi Akademik</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="border border-green-200 bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-sm text-gray-600">Naik Kelas</div>
                        <div class="text-xl font-bold text-green-600 mt-1">0</div>
                        <div class="text-xs text-gray-400 mt-1">Santri</div>
                    </div>
                    <div class="border border-red-200 bg-red-50 rounded-lg p-4 text-center">
                        <div class="text-sm text-gray-600">Tidak Naik Kelas</div>
                        <div class="text-xl font-bold text-red-600 mt-1">0</div>
                        <div class="text-xs text-gray-400 mt-1">Santri</div>
                    </div>
                </div>
            </div>
        </template>
    </DashboardLayout>
</template>