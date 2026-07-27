<script setup>
import { ref } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

</script>

<template>
    <Head title="Dashboard" />

    <DashboardLayout title="Dashboard Utama">
        <template #default>
            Keuangan SPP
        </template>
    </DashboardLayout>
</template>