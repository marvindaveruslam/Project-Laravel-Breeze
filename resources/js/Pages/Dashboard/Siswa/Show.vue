<script setup>
import { ref, computed, watch } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Swal from "sweetalert2";

const props = defineProps({
    siswa: { type: Object, required: true },
    attendanceSummary: {
        type: Object,
        default: () => ({ hadir: 0, izin: 0, sakit: 0, alpa: 0 }),
    },
    recentAttendances: { type: Array, default: () => [] },
    flash: Object,
});

// ================= NOTIFIKASI =================
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

const formatDate = (d) => {
    if (!d) return "-";
    return new Date(d).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
};

// ================= BIODATA =================
const editing = ref(false);
const fillBio = () => ({
    nis: props.siswa.nis ?? "",
    name: props.siswa.name ?? "",
    gender: props.siswa.gender ?? "L",
    class: props.siswa.class ?? "",
    birth_place: props.siswa.birth_place ?? "",
    birth_date: props.siswa.birth_date ?? "",
    parent_name: props.siswa.parent_name ?? "",
    parent_phone: props.siswa.parent_phone ?? "",
    dormitory: props.siswa.dormitory ?? "",
    address: props.siswa.address ?? "",
    homeroom_teacher: props.siswa.homeroom_teacher ?? "",
    status: props.siswa.status ?? "Aktif",
});
const bio = useForm(fillBio());

const startEdit = () => {
    bio.defaults(fillBio());
    bio.reset();
    bio.clearErrors();
    editing.value = true;
};

const saveBio = () => {
    bio.put(route("dashboard.siswa.update", props.siswa.id), {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = false;
        },
    });
};

// ================= NILAI =================
const toRows = (grades) =>
    grades.map((g) => ({
        id: g.id,
        subject: g.subject,
        daily_score: g.daily_score,
        exam_score: g.exam_score,
    }));

const gradeForm = useForm({ grades: toRows(props.siswa.grades || []) });

watch(
    () => props.siswa.grades,
    (g) => {
        gradeForm.grades = toRows(g || []);
        gradeForm.defaults({ grades: toRows(g || []) });
    },
    { deep: true }
);

const num = (v) =>
    v === null || v === undefined || v === "" ? null : Number(v);

const average = (row) => {
    const vals = [num(row.daily_score), num(row.exam_score)].filter(
        (v) => v !== null && !Number.isNaN(v)
    );
    if (!vals.length) return null;
    return Math.round(vals.reduce((a, b) => a + b, 0) / vals.length);
};

const overallAverage = computed(() => {
    const avgs = gradeForm.grades.map(average).filter((v) => v !== null);
    if (!avgs.length) return null;
    return (avgs.reduce((a, b) => a + b, 0) / avgs.length).toFixed(1);
});

const newSubject = ref("");
const addSubject = () => {
    const name = newSubject.value.trim();
    if (!name) return;
    if (
        gradeForm.grades.some(
            (g) => g.subject.toLowerCase() === name.toLowerCase()
        )
    ) {
        Toast.fire({ icon: "warning", title: "Mata pelajaran sudah ada" });
        return;
    }
    gradeForm.grades.push({
        id: null,
        subject: name,
        daily_score: null,
        exam_score: null,
    });
    newSubject.value = "";
};

const removeRow = async (row, index) => {
    if (!row.id) {
        gradeForm.grades.splice(index, 1);
        return;
    }
    const r = await Swal.fire({
        title: `Hapus ${row.subject}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
    });
    if (r.isConfirmed) {
        router.delete(
            route("dashboard.siswa.grades.destroy", [props.siswa.id, row.id]),
            { preserveScroll: true }
        );
    }
};

const saveGrades = () => {
    gradeForm.put(route("dashboard.siswa.grades.update", props.siswa.id), {
        preserveScroll: true,
    });
};

// ================= KEHADIRAN =================
const today = new Date().toISOString().slice(0, 10);
const attForm = useForm({ date: today, status: "hadir", note: "" });

const saveAttendance = () => {
    attForm.post(route("dashboard.siswa.attendance.store", props.siswa.id), {
        preserveScroll: true,
        onSuccess: () => attForm.reset("note"),
    });
};

const removeAttendance = async (a) => {
    const r = await Swal.fire({
        title: "Hapus catatan ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
    });
    if (r.isConfirmed) {
        router.delete(
            route("dashboard.siswa.attendance.destroy", [props.siswa.id, a.id]),
            { preserveScroll: true }
        );
    }
};

const statusStyle = {
    hadir: "bg-green-50 text-green-700 border-green-200",
    izin: "bg-blue-50 text-blue-700 border-blue-200",
    sakit: "bg-yellow-50 text-yellow-700 border-yellow-200",
    alpa: "bg-red-50 text-red-700 border-red-200",
};

const attCards = [
    { key: "hadir", label: "Hadir", cls: "text-green-700", bg: "bg-green-50 border-green-100" },
    { key: "izin", label: "Izin", cls: "text-blue-700", bg: "bg-blue-50 border-blue-100" },
    { key: "sakit", label: "Sakit", cls: "text-yellow-700", bg: "bg-yellow-50 border-yellow-100" },
    { key: "alpa", label: "Alpa", cls: "text-red-700", bg: "bg-red-50 border-red-100" },
];
</script>

<template>
    <Head :title="`Detail Siswa - ${siswa.name}`" />

    <DashboardLayout title="Data Siswa">
        <!-- Breadcrumb -->
        <div class="mb-3">
            <Link
                :href="route('dashboard.siswa')"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Siswa
            </Link>
        </div>

        <!-- Kartu Profil Utama -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
            <div class="p-4 flex flex-col sm:flex-row items-center sm:items-start gap-5">
                <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold shrink-0">
                    {{ (siswa.name || "?").charAt(0).toUpperCase() }}
                </div>
                
                <div class="text-center sm:text-left flex-1">
                    <h1 class="text-xl font-bold text-gray-900">{{ siswa.name }}</h1>
                    <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                            NIS: {{ siswa.nis }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                            Kelas: {{ siswa.class || "-" }}
                        </span>
                        <span 
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border"
                            :class="{
                                'bg-green-50 text-green-700 border-green-200': siswa.status === 'Aktif',
                                'bg-yellow-50 text-yellow-700 border-yellow-200': siswa.status === 'Lulus',
                                'bg-red-50 text-red-700 border-red-200': siswa.status === 'Keluar',
                            }"
                        >
                            {{ siswa.status || 'Aktif' }}
                        </span>
                    </div>
                </div>

                <div class="text-center sm:text-right bg-gray-50 px-4 py-2 rounded border border-gray-200">
                    <p class="text-[11px] text-gray-500 uppercase tracking-wider font-semibold">Walikelas</p>
                    <p class="text-sm font-bold text-gray-800">{{ siswa.homeroom_teacher || "-" }}</p>
                </div>
            </div>
        </div>

        <!-- BIODATA -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
            <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    Biodata Siswa
                </h3>
                <button
                    v-if="!editing"
                    @click="startEdit"
                    class="text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 px-3 py-1.5 rounded-md transition"
                >
                    Edit Biodata
                </button>
            </div>

            <!-- Mode Tampilan Biodata -->
            <div v-if="!editing" class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">NIS</p>
                        <p class="font-medium text-gray-900">{{ siswa.nis }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Nama Lengkap</p>
                        <p class="font-medium text-gray-900">{{ siswa.name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Jenis Kelamin</p>
                        <p class="font-medium text-gray-900">{{ siswa.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Tempat, Tanggal Lahir</p>
                        <p class="font-medium text-gray-900">{{ siswa.birth_place || '-' }}, {{ formatDate(siswa.birth_date) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Kelas</p>
                        <p class="font-medium text-gray-900">{{ siswa.class || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Kamar Asrama</p>
                        <p class="font-medium text-gray-900">{{ siswa.dormitory || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Nama Orang Tua / Wali</p>
                        <p class="font-medium text-gray-900">{{ siswa.parent_name || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">No. HP Wali</p>
                        <p class="font-medium text-gray-900">{{ siswa.parent_phone || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Nama Walikelas</p>
                        <p class="font-medium text-gray-900">{{ siswa.homeroom_teacher || '-' }}</p>
                    </div>
                    <div class="sm:col-span-2 md:col-span-3">
                        <p class="text-xs text-gray-500 mb-0.5">Alamat</p>
                        <p class="font-medium text-gray-900">{{ siswa.address || '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Mode Edit Biodata -->
            <div v-else class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">NIS</label>
                        <input v-model="bio.nis" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                        <p v-if="bio.errors.nis" class="text-xs text-red-600 mt-1">{{ bio.errors.nis }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">Nama Lengkap</label>
                        <input v-model="bio.name" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                        <p v-if="bio.errors.name" class="text-xs text-red-600 mt-1">{{ bio.errors.name }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">Jenis Kelamin</label>
                        <select v-model="bio.gender" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">Tempat Lahir</label>
                        <input v-model="bio.birth_place" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">Tanggal Lahir</label>
                        <input v-model="bio.birth_date" type="date" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                        <p v-if="bio.errors.birth_date" class="text-xs text-red-600 mt-1">{{ bio.errors.birth_date }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">Kelas</label>
                        <input v-model="bio.class" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">Kamar Asrama</label>
                        <input v-model="bio.dormitory" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">Nama Orang Tua/Wali</label>
                        <input v-model="bio.parent_name" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">No. HP Wali</label>
                        <input v-model="bio.parent_phone" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">Nama Walikelas</label>
                        <input v-model="bio.homeroom_teacher" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">Status</label>
                        <select v-model="bio.status" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option>Aktif</option>
                            <option>Lulus</option>
                            <option>Keluar</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 md:col-span-3">
                        <label class="text-xs text-gray-600 mb-1 block">Alamat</label>
                        <textarea v-model="bio.address" rows="2" class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-200">
                    <button @click="editing = false" class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-50 transition">Batal</button>
                    <button @click="saveBio" :disabled="bio.processing" class="px-3 py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded disabled:opacity-50 transition">
                        <span v-if="bio.processing">Menyimpan...</span>
                        <span v-else>Simpan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- NILAI -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
            <div class="px-4 py-3 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-gray-50 rounded-t-lg">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        Nilai
                    </h3>
                </div>
                <div class="text-right">
                    <p class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Rata-rata Keseluruhan</p>
                    <p class="text-lg font-bold text-blue-600 leading-none">{{ overallAverage ?? "-" }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-600 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 font-semibold">Mata Pelajaran</th>
                            <th class="px-3 py-2 w-32 text-center font-semibold">Nilai Harian</th>
                            <th class="px-3 py-2 w-32 text-center font-semibold">Nilai Ulangan</th>
                            <th class="px-3 py-2 w-24 text-center font-semibold">Rata-rata</th>
                            <th class="px-3 py-2 w-12 text-center font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="gradeForm.grades.length === 0">
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 text-sm">
                                Belum ada nilai.
                            </td>
                        </tr>
                        <tr v-for="(row, i) in gradeForm.grades" :key="row.id ?? `new-${i}`" class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-2">
                                <input v-model="row.subject" class="w-full border border-gray-300 rounded p-1 text-sm focus:ring-blue-500 focus:border-blue-500" />
                                <p v-if="gradeForm.errors[`grades.${i}.subject`]" class="text-[10px] text-red-600 mt-1">{{ gradeForm.errors[`grades.${i}.subject`] }}</p>
                            </td>
                            <td class="px-3 py-2">
                                <input v-model="row.daily_score" type="number" min="0" max="100" class="w-full border border-gray-300 rounded p-1 text-sm text-center focus:ring-blue-500 focus:border-blue-500" />
                            </td>
                            <td class="px-3 py-2">
                                <input v-model="row.exam_score" type="number" min="0" max="100" class="w-full border border-gray-300 rounded p-1 text-sm text-center focus:ring-blue-500 focus:border-blue-500" />
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="font-bold text-gray-700">{{ average(row) ?? "-" }}</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button @click="removeRow(row, i)" class="text-red-500 hover:text-red-700 p-1 rounded transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex gap-2 w-full sm:w-auto">
                    <input
                        v-model="newSubject"
                        @keyup.enter="addSubject"
                        placeholder="Mapel baru..."
                        class="border border-gray-300 rounded p-1.5 text-sm flex-1 sm:w-56 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <button @click="addSubject" class="px-3 py-1.5 text-sm font-medium border border-gray-300 bg-white rounded hover:bg-gray-50 transition whitespace-nowrap">
                        + Tambah
                    </button>
                </div>
                <button
                    @click="saveGrades"
                    :disabled="gradeForm.processing || gradeForm.grades.length === 0"
                    class="px-4 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded disabled:opacity-50 transition shadow-sm"
                >
                    {{ gradeForm.processing ? "Menyimpan..." : "Simpan Nilai" }}
                </button>
            </div>
        </div>

        <!-- KEHADIRAN -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Kehadiran
                </h3>
            </div>

            <!-- Statistik Kehadiran -->
            <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-3 border-b border-gray-100">
                <div v-for="c in attCards" :key="c.key" class="border rounded p-3 text-center" :class="c.bg">
                    <p class="text-xs font-medium opacity-80" :class="c.cls">{{ c.label }}</p>
                    <p class="text-xl font-bold mt-1" :class="c.cls">{{ attendanceSummary[c.key] ?? 0 }}</p>
                </div>
            </div>

            <!-- Form Input Kehadiran -->
            <div class="p-4">
                <div class="flex flex-col md:flex-row gap-3 md:items-end">
                    <div class="w-full md:w-auto">
                        <label class="text-[11px] font-semibold text-gray-600 mb-1 block uppercase tracking-wider">Tanggal</label>
                        <input v-model="attForm.date" type="date" class="w-full md:w-36 border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="w-full md:w-auto">
                        <label class="text-[11px] font-semibold text-gray-600 mb-1 block uppercase tracking-wider">Status</label>
                        <select v-model="attForm.status" class="w-full md:w-28 border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="text-[11px] font-semibold text-gray-600 mb-1 block uppercase tracking-wider">Keterangan</label>
                        <input v-model="attForm.note" placeholder="Opsional..." class="w-full border border-gray-300 rounded p-1.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <button @click="saveAttendance" :disabled="attForm.processing" class="px-4 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded disabled:opacity-50 transition shadow-sm w-full md:w-auto">
                        Simpan
                    </button>
                </div>
                <p v-if="attForm.errors.date || attForm.errors.status" class="text-xs text-red-600 mt-2">
                    {{ attForm.errors.date || attForm.errors.status }}
                </p>
            </div>

            <!-- Riwayat Kehadiran -->
            <div class="px-4 pb-4">
                <div v-if="recentAttendances.length === 0" class="text-sm text-gray-500 bg-gray-50 p-3 rounded text-center border border-dashed border-gray-300">
                    Belum ada catatan kehadiran.
                </div>
                <ul v-else class="divide-y divide-gray-100 border border-gray-200 rounded overflow-hidden">
                    <li v-for="a in recentAttendances" :key="a.id" class="px-3 py-2 flex items-center justify-between gap-3 hover:bg-gray-50 transition bg-white">
                        <span class="w-32 text-sm text-gray-700">
                            {{ formatDate(a.date) }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-xs font-medium capitalize w-16 text-center border" :class="statusStyle[a.status]">
                            {{ a.status }}
                        </span>
                        <span class="flex-1 text-sm text-gray-500 italic">{{ a.note || "" }}</span>
                        <button @click="removeAttendance(a)" class="text-gray-400 hover:text-red-600 transition p-1" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

    </DashboardLayout>
</template>