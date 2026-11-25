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
    { immediate: true, deep: true },
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

// Sinkronkan form setelah data dimuat ulang dari server (mis. setelah simpan)
watch(
    () => props.siswa.grades,
    (g) => {
        gradeForm.grades = toRows(g || []);
        gradeForm.defaults({ grades: toRows(g || []) });
    },
    { deep: true },
);

const num = (v) =>
    v === null || v === undefined || v === "" ? null : Number(v);
const average = (row) => {
    const vals = [num(row.daily_score), num(row.exam_score)].filter(
        (v) => v !== null && !Number.isNaN(v),
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
            (g) => g.subject.toLowerCase() === name.toLowerCase(),
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
            { preserveScroll: true },
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
            { preserveScroll: true },
        );
    }
};

const statusStyle = {
    hadir: "bg-green-100 text-green-700",
    izin: "bg-blue-100 text-blue-700",
    sakit: "bg-yellow-100 text-yellow-700",
    alpa: "bg-red-100 text-red-700",
};
const attCards = [
    { key: "hadir", label: "Hadir", cls: "text-green-600" },
    { key: "izin", label: "Izin", cls: "text-blue-600" },
    { key: "sakit", label: "Sakit", cls: "text-yellow-600" },
    { key: "alpa", label: "Alpa", cls: "text-red-600" },
];
</script>

<template>
    <Head :title="`Siswa - ${siswa.name}`" />

    <DashboardLayout title="Data Siswa">
        <div class="mb-4">
            <Link
                :href="route('dashboard.siswa')"
                class="text-sm text-blue-600 hover:text-blue-800"
                >← Kembali ke Daftar Siswa</Link
            >
        </div>

        <!-- Header siswa -->
        <div
            class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6 flex flex-col md:flex-row md:items-center gap-4"
        >
            <div
                class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold"
            >
                {{ (siswa.name || "?").charAt(0).toUpperCase() }}
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ siswa.name }}
                </h1>
                <p class="text-sm text-gray-500">
                    NIS {{ siswa.nis }} · Kelas {{ siswa.class || "-" }} ·
                    {{ siswa.status }}
                </p>
            </div>
            <div class="text-sm">
                <p class="text-gray-500">Walikelas</p>
                <p class="font-semibold text-gray-900">
                    {{ siswa.homeroom_teacher || "-" }}
                </p>
            </div>
        </div>

        <!-- BIODATA -->
        <div
            class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6"
        >
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Biodata</h2>
                <button
                    v-if="!editing"
                    @click="startEdit"
                    class="text-sm px-3 py-1.5 border rounded-lg hover:bg-gray-50"
                >
                    Edit Biodata
                </button>
            </div>

            <dl
                v-if="!editing"
                class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 text-sm"
            >
                <div>
                    <dt class="text-gray-500">NIS</dt>
                    <dd class="font-medium text-gray-900">{{ siswa.nis }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Nama Lengkap</dt>
                    <dd class="font-medium text-gray-900">{{ siswa.name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Jenis Kelamin</dt>
                    <dd class="font-medium text-gray-900">
                        {{ siswa.gender === "L" ? "Laki-laki" : "Perempuan" }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tempat, Tanggal Lahir</dt>
                    <dd class="font-medium text-gray-900">
                        {{ siswa.birth_place || "-" }},
                        {{ formatDate(siswa.birth_date) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Kelas</dt>
                    <dd class="font-medium text-gray-900">
                        {{ siswa.class || "-" }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Kamar Asrama</dt>
                    <dd class="font-medium text-gray-900">
                        {{ siswa.dormitory || "-" }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Nama Orang Tua/Wali</dt>
                    <dd class="font-medium text-gray-900">
                        {{ siswa.parent_name || "-" }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">No. HP Wali</dt>
                    <dd class="font-medium text-gray-900">
                        {{ siswa.parent_phone || "-" }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Nama Walikelas</dt>
                    <dd class="font-medium text-gray-900">
                        {{ siswa.homeroom_teacher || "-" }}
                    </dd>
                </div>
                <div class="md:col-span-3">
                    <dt class="text-gray-500">Alamat</dt>
                    <dd class="font-medium text-gray-900">
                        {{ siswa.address || "-" }}
                    </dd>
                </div>
            </dl>

            <div v-else>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <label class="text-gray-600">NIS</label>
                        <input
                            v-model="bio.nis"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        />
                        <p v-if="bio.errors.nis" class="text-xs text-red-600">
                            {{ bio.errors.nis }}
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-600">Nama Lengkap</label>
                        <input
                            v-model="bio.name"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        />
                        <p v-if="bio.errors.name" class="text-xs text-red-600">
                            {{ bio.errors.name }}
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-600">Jenis Kelamin</label>
                        <select
                            v-model="bio.gender"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        >
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-600">Tempat Lahir</label>
                        <input
                            v-model="bio.birth_place"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        />
                    </div>
                    <div>
                        <label class="text-gray-600">Tanggal Lahir</label>
                        <input
                            v-model="bio.birth_date"
                            type="date"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        />
                        <p
                            v-if="bio.errors.birth_date"
                            class="text-xs text-red-600"
                        >
                            {{ bio.errors.birth_date }}
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-600">Kelas</label>
                        <input
                            v-model="bio.class"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        />
                    </div>
                    <div>
                        <label class="text-gray-600">Kamar Asrama</label>
                        <input
                            v-model="bio.dormitory"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        />
                    </div>
                    <div>
                        <label class="text-gray-600">Nama Orang Tua/Wali</label>
                        <input
                            v-model="bio.parent_name"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        />
                    </div>
                    <div>
                        <label class="text-gray-600">No. HP Wali</label>
                        <input
                            v-model="bio.parent_phone"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        />
                    </div>
                    <div>
                        <label class="text-gray-600">Nama Walikelas</label>
                        <input
                            v-model="bio.homeroom_teacher"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        />
                    </div>
                    <div>
                        <label class="text-gray-600">Status</label>
                        <select
                            v-model="bio.status"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        >
                            <option>Aktif</option>
                            <option>Lulus</option>
                            <option>Keluar</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="text-gray-600">Alamat</label>
                        <textarea
                            v-model="bio.address"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg p-2"
                        ></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button
                        @click="editing = false"
                        class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="saveBio"
                        :disabled="bio.processing"
                        class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg disabled:opacity-50"
                    >
                        Simpan Biodata
                    </button>
                </div>
            </div>
        </div>

        <!-- NILAI -->
        <div
            class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6"
        >
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Nilai</h2>
                    <p class="text-sm text-gray-500">
                        Nilai harian, ulangan, dan rata-rata (dihitung
                        otomatis).
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Rata-rata keseluruhan</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ overallAverage ?? "-" }}
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Mata Pelajaran</th>
                            <th class="px-4 py-3 w-32">Nilai Harian</th>
                            <th class="px-4 py-3 w-32">Nilai Ulangan</th>
                            <th class="px-4 py-3 w-28">Rata-rata</th>
                            <th class="px-4 py-3 w-16"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="gradeForm.grades.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-4 text-center text-gray-500"
                            >
                                Belum ada nilai. Tambahkan mata pelajaran di
                                bawah.
                            </td>
                        </tr>
                        <tr
                            v-for="(row, i) in gradeForm.grades"
                            :key="row.id ?? `new-${i}`"
                            class="border-b"
                        >
                            <td class="px-4 py-2">
                                <input
                                    v-model="row.subject"
                                    class="w-full border border-gray-300 rounded-lg p-1.5"
                                />
                                <p
                                    v-if="
                                        gradeForm.errors[`grades.${i}.subject`]
                                    "
                                    class="text-xs text-red-600"
                                >
                                    {{
                                        gradeForm.errors[`grades.${i}.subject`]
                                    }}
                                </p>
                            </td>
                            <td class="px-4 py-2">
                                <input
                                    v-model="row.daily_score"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="w-full border border-gray-300 rounded-lg p-1.5"
                                />
                                <p
                                    v-if="
                                        gradeForm.errors[
                                            `grades.${i}.daily_score`
                                        ]
                                    "
                                    class="text-xs text-red-600"
                                >
                                    0–100
                                </p>
                            </td>
                            <td class="px-4 py-2">
                                <input
                                    v-model="row.exam_score"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="w-full border border-gray-300 rounded-lg p-1.5"
                                />
                                <p
                                    v-if="
                                        gradeForm.errors[
                                            `grades.${i}.exam_score`
                                        ]
                                    "
                                    class="text-xs text-red-600"
                                >
                                    0–100
                                </p>
                            </td>
                            <td class="px-4 py-2 font-semibold text-gray-900">
                                {{ average(row) ?? "-" }}
                            </td>
                            <td class="px-4 py-2">
                                <button
                                    @click="removeRow(row, i)"
                                    class="text-red-600 hover:text-red-800 text-xs"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-3 mt-4"
            >
                <div class="flex gap-2">
                    <input
                        v-model="newSubject"
                        @keyup.enter="addSubject"
                        placeholder="Mata pelajaran baru (mis. Fiqih)"
                        class="border border-gray-300 rounded-lg p-2 text-sm w-64"
                    />
                    <button
                        @click="addSubject"
                        class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50"
                    >
                        + Tambah
                    </button>
                </div>
                <button
                    @click="saveGrades"
                    :disabled="
                        gradeForm.processing || gradeForm.grades.length === 0
                    "
                    class="px-5 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg disabled:opacity-50"
                >
                    {{ gradeForm.processing ? "Menyimpan..." : "Simpan Nilai" }}
                </button>
            </div>
        </div>

        <!-- KEHADIRAN -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Kehadiran</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div
                    v-for="c in attCards"
                    :key="c.key"
                    class="border border-gray-100 rounded-lg p-4 text-center"
                >
                    <p class="text-sm text-gray-500">{{ c.label }}</p>
                    <p class="text-3xl font-bold" :class="c.cls">
                        {{ attendanceSummary[c.key] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-3 md:items-end mb-6">
                <div>
                    <label class="text-xs text-gray-600">Tanggal</label>
                    <input
                        v-model="attForm.date"
                        type="date"
                        class="block border border-gray-300 rounded-lg p-2 text-sm"
                    />
                </div>
                <div>
                    <label class="text-xs text-gray-600">Status</label>
                    <select
                        v-model="attForm.status"
                        class="block border border-gray-300 rounded-lg p-2 text-sm"
                    >
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpa">Alpa</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="text-xs text-gray-600"
                        >Keterangan (opsional)</label
                    >
                    <input
                        v-model="attForm.note"
                        class="block w-full border border-gray-300 rounded-lg p-2 text-sm"
                    />
                </div>
                <button
                    @click="saveAttendance"
                    :disabled="attForm.processing"
                    class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg disabled:opacity-50"
                >
                    Simpan Kehadiran
                </button>
            </div>
            <p
                v-if="attForm.errors.date || attForm.errors.status"
                class="text-xs text-red-600 -mt-4 mb-4"
            >
                {{ attForm.errors.date || attForm.errors.status }}
            </p>

            <h3 class="text-sm font-semibold text-gray-700 mb-2">
                Riwayat terbaru
            </h3>
            <p
                v-if="recentAttendances.length === 0"
                class="text-sm text-gray-500"
            >
                Belum ada catatan kehadiran.
            </p>
            <ul v-else class="divide-y text-sm">
                <li
                    v-for="a in recentAttendances"
                    :key="a.id"
                    class="py-2 flex items-center justify-between gap-3"
                >
                    <span class="w-40 text-gray-700">{{
                        formatDate(a.date)
                    }}</span>
                    <span
                        class="px-2 py-0.5 rounded text-xs font-medium capitalize"
                        :class="statusStyle[a.status]"
                        >{{ a.status }}</span
                    >
                    <span class="flex-1 text-gray-500">{{ a.note || "" }}</span>
                    <button
                        @click="removeAttendance(a)"
                        class="text-red-600 hover:text-red-800 text-xs"
                    >
                        Hapus
                    </button>
                </li>
            </ul>
        </div>
    </DashboardLayout>
</template>
