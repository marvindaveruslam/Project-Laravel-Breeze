<script setup>
import { watch } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Swal from "sweetalert2";

/**
 * Halaman input nilai versi TERBATAS untuk guru mapel.
 * Sengaja hanya menampilkan nama, NIS, kelas, dan nilai mapel milik guru ini —
 * tanpa biodata, kehadiran, ataupun data keuangan/SPP.
 */
const props = defineProps({
    siswa: { type: Object, required: true },
    grades: { type: Array, default: () => [] },
    limited: { type: Boolean, default: true },
    flash: Object,
});

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

watch(
    () => props.flash,
    (f) => {
        if (f?.success) Toast.fire({ icon: "success", title: f.success });
        if (f?.error) Toast.fire({ icon: "error", title: f.error });
    },
    { immediate: true, deep: true }
);

const toRows = (grades) =>
    grades.map((g) => ({
        id: g.id,
        subject: g.subject,
        daily_score: g.daily_score,
        exam_score: g.exam_score,
    }));

const form = useForm({ grades: toRows(props.grades) });

// Sinkronkan setelah data dimuat ulang dari server (mis. setelah simpan)
watch(
    () => props.grades,
    (g) => {
        form.grades = toRows(g || []);
    },
    { deep: true }
);

const num = (v) => (v === null || v === undefined || v === "" ? null : Number(v));
const average = (row) => {
    const vals = [num(row.daily_score), num(row.exam_score)].filter(
        (v) => v !== null && !Number.isNaN(v)
    );
    if (!vals.length) return null;
    return Math.round(vals.reduce((a, b) => a + b, 0) / vals.length);
};

const save = () => {
    form.put(route("dashboard.siswa.grades.update", props.siswa.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Input Nilai - ${siswa.name}`" />

    <DashboardLayout title="Data Siswa">
        <div class="mb-3">
            <Link
                :href="route('dashboard.siswa')"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition"
            >
                ← Kembali ke Daftar Siswa
            </Link>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-bold shrink-0">
                {{ (siswa.name || "?").charAt(0).toUpperCase() }}
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900">{{ siswa.name }}</h1>
                <p class="text-sm text-gray-500">
                    NIS {{ siswa.nis }} · Kelas {{ siswa.class || "-" }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h3 class="text-sm font-bold text-gray-900">Input Nilai</h3>
                <p v-if="limited" class="text-xs text-gray-500 mt-0.5">
                    Anda hanya dapat mengisi nilai untuk mata pelajaran yang Anda ajar di kelas ini.
                </p>
            </div>

            <div class="p-4">
                <p v-if="form.grades.length === 0" class="text-sm text-gray-500 text-center py-6">
                    Belum ada mata pelajaran yang dapat Anda nilai untuk siswa ini.
                </p>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-3 py-2">Mata Pelajaran</th>
                                <th class="px-3 py-2 w-32">Nilai Harian</th>
                                <th class="px-3 py-2 w-32">Nilai Ulangan</th>
                                <th class="px-3 py-2 w-28">Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, i) in form.grades" :key="row.id ?? `new-${i}`" class="border-b">
                                <td class="px-3 py-2 font-medium text-gray-900">{{ row.subject }}</td>
                                <td class="px-3 py-2">
                                    <input
                                        v-model="row.daily_score"
                                        type="number" min="0" max="100"
                                        class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500"
                                    />
                                    <p v-if="form.errors[`grades.${i}.daily_score`]" class="text-xs text-red-600">0–100</p>
                                </td>
                                <td class="px-3 py-2">
                                    <input
                                        v-model="row.exam_score"
                                        type="number" min="0" max="100"
                                        class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500"
                                    />
                                    <p v-if="form.errors[`grades.${i}.exam_score`]" class="text-xs text-red-600">0–100</p>
                                </td>
                                <td class="px-3 py-2 font-semibold text-gray-900">{{ average(row) ?? "-" }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-4" v-if="form.grades.length">
                    <button
                        @click="save"
                        :disabled="form.processing"
                        class="px-5 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg disabled:opacity-50 transition"
                    >
                        {{ form.processing ? "Menyimpan..." : "Simpan Nilai" }}
                    </button>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
