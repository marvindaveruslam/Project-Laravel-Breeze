<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"; // Sesuaikan dengan layout Anda
import { ref } from "vue";

// Props dari Controller (misal: $siswas)
const props = defineProps({
    siswas: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    stats: {
        type: Object,
        default: () => ({ total: 0, laki_laki: 0, perempuan: 0 }),
    },
});

// State untuk pencarian (opsional)
const search = ref("");

// Fungsi untuk hapus data (opsional)
const deleteSiswa = (id) => {
    if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        // Gunakan router.delete dari Inertia
        // router.delete(route('siswa.destroy', id));
        console.log("Hapus ID:", id);
    }
};
</script>

<template>
    <Head title="Data Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Siswa
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Text -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Data Siswa</h1>
                    <p class="text-gray-500 mt-1">
                        Selamat datang kembali, Administrator
                    </p>
                </div>

                <!-- Statistik Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div
                        class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between"
                    >
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Total Siswa
                            </p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ stats.total }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Semua siswa
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-500"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                ></path>
                            </svg>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between"
                    >
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Laki-laki
                            </p>
                            <p class="text-2xl font-bold text-blue-600 mt-1">
                                {{ stats.laki_laki }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Siswa putra
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-500"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                ></path>
                            </svg>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between"
                    >
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Perempuan
                            </p>
                            <p class="text-2xl font-bold text-pink-600 mt-1">
                                {{ stats.perempuan }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Siswa putri
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-pink-50 rounded-lg flex items-center justify-center text-pink-500"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data Siswa -->
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden"
                >
                    <div
                        class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <div>
                            <h3
                                class="text-lg font-bold text-gray-900 flex items-center gap-2"
                            >
                                <svg
                                    class="w-5 h-5 text-gray-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    ></path>
                                </svg>
                                Daftar Siswa
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Menampilkan {{ siswas.data.length }} dari
                                {{ siswas.total || 0 }} siswa
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                >
                                    <svg
                                        class="w-4 h-4 text-gray-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                        ></path>
                                    </svg>
                                </div>
                                <input
                                    v-model="search"
                                    type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2"
                                    placeholder="Cari nama, NIS, atau kelas..."
                                />
                            </div>
                            <Link
                                :href="route('siswa.create')"
                                class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 flex items-center gap-2"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    ></path>
                                </svg>
                                Tambah Siswa
                            </Link>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50"
                            >
                                <tr>
                                    <th scope="col" class="px-6 py-3">#</th>
                                    <th scope="col" class="px-6 py-3">Nama</th>
                                    <th scope="col" class="px-6 py-3">NIS</th>
                                    <th scope="col" class="px-6 py-3">JK</th>
                                    <th scope="col" class="px-6 py-3">Kelas</th>
                                    <th scope="col" class="px-6 py-3">No HP</th>
                                    <th scope="col" class="px-6 py-3">
                                        Dibuat
                                    </th>
                                    <th scope="col" class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="siswas.data.length === 0">
                                    <td
                                        colspan="8"
                                        class="px-6 py-4 text-center text-gray-500"
                                    >
                                        Belum ada data siswa.
                                    </td>
                                </tr>
                                <tr
                                    v-for="(siswa, index) in siswas.data"
                                    :key="siswa.id"
                                    class="bg-white border-b hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4">{{ index + 1 }}</td>
                                    <td
                                        class="px-6 py-4 flex items-center gap-3"
                                    >
                                        <div
                                            class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold"
                                        >
                                            {{
                                                siswa.nama
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </div>
                                        <span
                                            class="font-medium text-gray-900"
                                            >{{ siswa.nama }}</span
                                        >
                                    </td>
                                    <td class="px-6 py-4">{{ siswa.nis }}</td>
                                    <td class="px-6 py-4">
                                        {{
                                            siswa.jenis_kelamin === "L"
                                                ? "Laki-laki"
                                                : "Perempuan"
                                        }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded"
                                            >{{
                                                siswa.kelas?.nama_kelas || "-"
                                            }}</span
                                        >
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ siswa.no_hp || "-" }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{
                                            new Date(
                                                siswa.created_at,
                                            ).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "long",
                                                year: "numeric",
                                            })
                                        }}
                                    </td>
                                    <td class="px-6 py-4 flex gap-2">
                                        <Link
                                            :href="
                                                route('siswa.edit', siswa.id)
                                            "
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                ></path>
                                            </svg>
                                        </Link>
                                        <button
                                            @click="deleteSiswa(siswa.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                ></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
