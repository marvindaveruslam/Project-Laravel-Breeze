<script setup>
import { ref, computed, watch } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Swal from "sweetalert2";

const props = defineProps({
    siswas: {
        type: Object,
        default: () => ({ 
            data: [], 
            current_page: 1, 
            last_page: 1, 
            total: 0, 
            from: 0, 
            to: 0 
        }),
    },
    stats: {
        type: Object,
        default: () => ({ total: 0, laki_laki: 0, perempuan: 0 }),
    },
    filters: { type: Object, default: () => ({ search: "" }) },
    kelasOptions: { type: Array, default: () => [] },
    access: {
        type: Object,
        default: () => ({ can_create: false, mode: "guru" }),
    },
    flash: Object,
});

// ---------------- Pencarian (server-side, debounce) ----------------
const search = ref(props.filters?.search || "");
let timer = null;
watch(search, (val) => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            route("dashboard.siswa"),
            { search: val || undefined },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    }, 400);
});

// ---------------- Pagination: jendela 10 nomor halaman ----------------
const WINDOW = 10;
const pageNumbers = computed(() => {
    const current = props.siswas.current_page || 1;
    const last = props.siswas.last_page || 1;
    let start = Math.max(1, current - Math.floor(WINDOW / 2));
    let end = start + WINDOW - 1;
    
    if (end > last) {
        end = last;
        start = Math.max(1, end - WINDOW + 1);
    }
    
    const pages = [];
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    return pages;
});

const goToPage = (page) => {
    if (
        page < 1 ||
        page > props.siswas.last_page ||
        page === props.siswas.current_page
    ) {
        return;
    }
    router.get(
        route("dashboard.siswa"),
        { search: search.value || undefined, page },
        {
            preserveState: true,
            preserveScroll: false,
        }
    );
};

// ---------------- Tambah siswa ----------------
const showModal = ref(false);

// Walikelas/admin membuka detail lengkap; guru mapel langsung ke halaman input nilai
const rowHref = (s) =>
    s.can_detail
        ? route("dashboard.siswa.show", s.id)
        : route("dashboard.siswa.nilai", s.id);
const form = useForm({
    nis: "",
    name: "",
    gender: "L",
    kelas_id: "",
});

const submit = () => {
    form.post(route("dashboard.siswa.store"), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            form.clearErrors();
        },
    });
};

// ---------------- Notifikasi ----------------
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
</script>

<template>
    <Head title="Data Siswa" />

    <DashboardLayout title="Data Siswa">
        <!-- Statistik -->
        <div v-if="stats" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500">Total Siswa</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500">Laki-laki</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ stats.laki_laki }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500">Perempuan</p>
                <p class="text-2xl font-bold text-pink-600 mt-1">{{ stats.perempuan }}</p>
            </div>
        </div>

        <!-- Tabel Data Siswa -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Siswa</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Menampilkan {{ siswas.from || 0 }}–{{ siswas.to || 0 }} dari {{ siswas.total || 0 }} siswa
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <input
                        v-model="search"
                        type="text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-64 p-2"
                        placeholder="Cari nama, NIS, atau kelas..."
                    />
                    <button
                        v-if="access.can_create"
                        @click="showModal = true"
                        class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2 whitespace-nowrap transition"
                    >
                        + Tambah Siswa
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Nama</th>
                            <th class="px-6 py-3">NIS</th>
                            <th v-if="access.mode !== 'guru'" class="px-6 py-3">JK</th>
                            <th class="px-6 py-3">Kelas</th>
                            <th v-if="access.mode !== 'guru'" class="px-6 py-3">Walikelas</th>
                            <th v-if="access.mode !== 'guru'" class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="siswas.data.length === 0">
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Belum ada data siswa.
                            </td>
                        </tr>
                        <tr
                            v-for="(s, index) in siswas.data"
                            :key="s.id"
                            class="bg-white border-b hover:bg-gray-50 transition"
                        >
                            <td class="px-6 py-3">
                                <!-- Penomoran urut antar halaman -->
                                {{ (siswas.from || 1) + index }}
                            </td>
                            <td class="px-6 py-3">
                                <Link
                                    :href="rowHref(s)"
                                    class="flex items-center gap-3 group"
                                >
                                    <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                        {{ (s.name || "?").charAt(0).toUpperCase() }}
                                    </span>
                                    <span class="font-medium text-gray-900 group-hover:text-blue-600 transition">
                                        {{ s.name }}
                                    </span>
                                </Link>
                            </td>
                            <td class="px-6 py-3">{{ s.nis }}</td>
                            <td v-if="access.mode !== 'guru'" class="px-6 py-3">
                                {{ s.gender ? (s.gender === "L" ? "Laki-laki" : "Perempuan") : "-" }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    {{ s.class || "-" }}
                                </span>
                            </td>
                            <td v-if="access.mode !== 'guru'" class="px-6 py-3">{{ s.homeroom_teacher || "-" }}</td>
                            <td v-if="access.mode !== 'guru'" class="px-6 py-3">
                                <span
                                    class="px-2 py-1 rounded text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-700': s.status === 'Aktif',
                                        'bg-yellow-100 text-yellow-700': s.status === 'Lulus',
                                        'bg-red-100 text-red-700': s.status === 'Keluar',
                                    }"
                                >
                                    {{ s.status || '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <Link
                                    :href="rowHref(s)"
                                    class="text-blue-600 hover:text-blue-900 font-medium"
                                >
                                    {{ s.can_detail ? "Buka" : "Input Nilai" }}
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="siswas.last_page > 1"
                class="p-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-3"
            >
                <p class="text-sm text-gray-500">
                    Halaman {{ siswas.current_page }} dari {{ siswas.last_page }}
                </p>
                <div class="flex items-center gap-1 flex-wrap justify-center">
                    <!-- Tombol Previous -->
                    <button
                        @click="goToPage(siswas.current_page - 1)"
                        :disabled="siswas.current_page === 1"
                        class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50 transition"
                    >
                        Previous
                    </button>

                    <!-- Titik-titik awal -->
                    <button
                        v-if="pageNumbers[0] > 1"
                        @click="goToPage(1)"
                        class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50 transition"
                    >
                        1 …
                    </button>

                    <!-- Nomor Halaman -->
                    <button
                        v-for="p in pageNumbers"
                        :key="p"
                        @click="goToPage(p)"
                        class="px-3 py-1.5 text-sm border rounded-lg transition"
                        :class="
                            p === siswas.current_page
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'hover:bg-gray-50'
                        "
                    >
                        {{ p }}
                    </button>

                    <!-- Titik-titik akhir -->
                    <button
                        v-if="pageNumbers[pageNumbers.length - 1] < siswas.last_page"
                        @click="goToPage(siswas.last_page)"
                        class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50 transition"
                    >
                        … {{ siswas.last_page }}
                    </button>

                    <!-- Tombol Next -->
                    <button
                        @click="goToPage(siswas.current_page + 1)"
                        :disabled="siswas.current_page === siswas.last_page"
                        class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50 transition"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Siswa -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
        >
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Siswa</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-600">NIS</label>
                        <input
                            v-model="form.nis"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                        <p v-if="form.errors.nis" class="text-xs text-red-600 mt-1">
                            {{ form.errors.nis }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Nama Lengkap</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-gray-600">Jenis Kelamin</label>
                            <select
                                v-model="form.gender"
                                class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Kelas</label>
                            <select
                                v-model="form.kelas_id"
                                class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">- Pilih kelas -</option>
                                <option v-for="k in kelasOptions" :key="k.id" :value="k.id">
                                    {{ k.nama_kelas }}
                                </option>
                            </select>
                            <p v-if="form.errors.kelas_id" class="text-xs text-red-600 mt-1">
                                {{ form.errors.kelas_id }}
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Nama walikelas terisi otomatis sesuai kelas yang dipilih.</p>
                </div>
                <div class="flex justify-end gap-2 mt-5">
                    <button
                        @click="
                            showModal = false;
                            form.reset();
                            form.clearErrors();
                        "
                        class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button
                        @click="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg disabled:opacity-50 transition"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan</span>
                    </button>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>