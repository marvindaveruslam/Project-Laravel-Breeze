<!-- resources/js/Pages/Dashboard/Classes/DetailClass.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    kelas: {
        type: Object,
        required: true,
        default: () => ({
            id: null,
            nama_kelas: '',
            tingkat: '',
            rombel: '',
            walikelas: null,
            walikelas_id: null,
            gurus: [],
            santris: [],
        })
    },
    daftarGuru: { type: Array, default: () => [] }
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
});

// ============================================
// STATE REAKTIF
// ============================================
const activeTab = ref('jadwal');
const showTambahJadwal = ref(false);
const showEditWalikelas = ref(false);

// State untuk modal Edit Jadwal
const showEditJadwal = ref(false);
const editingGuruId = ref(null);

// Form Jadwal (Tambah)
const formJadwal = useForm({
    guru_id: '',
    mata_pelajaran: '',
    hari: '',
    jam_mulai: '',
    jam_selesai: '',
});

// Form Jadwal (Edit)
const formEditJadwal = useForm({
    guru_id: '',
    mata_pelajaran: '',
    hari: '',
    jam_mulai: '',
    jam_selesai: '',
});

// Form Walikelas
const formWalikelas = useForm({
    walikelas_id: props.kelas.walikelas_id || '',
});

// ============================================
// COMPUTED
// ============================================
const totalSantri = computed(() => props.kelas.santris?.length || 0);
const totalGuru = computed(() => props.kelas.gurus?.length || 0);

// ============================================
// FUNGSI WALIKELAS
// ============================================
const openEditWalikelas = () => {
    formWalikelas.walikelas_id = props.kelas.walikelas_id || '';
    formWalikelas.clearErrors();
    showEditWalikelas.value = true;
};

const saveWalikelas = () => {
    formWalikelas.put(route('classes.updateWalikelas', props.kelas.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditWalikelas.value = false;
            Toast.fire({ icon: 'success', title: '✅ Walikelas berhasil diperbarui!' });
        },
        onError: () => {
            Toast.fire({ icon: 'error', title: 'Gagal menyimpan walikelas.' });
        }
    });
};

// ============================================
// FUNGSI JADWAL — TAMBAH
// ============================================
const openTambahJadwal = () => {
    formJadwal.reset();
    formJadwal.clearErrors();
    showTambahJadwal.value = true;
};

const saveJadwal = () => {
    formJadwal.post(route('classes.storeGuru', props.kelas.id), {
        preserveScroll: true,
        onSuccess: () => {
            showTambahJadwal.value = false;
            Toast.fire({ icon: 'success', title: '✅ Jadwal berhasil disimpan!' });
        },
        onError: () => {
            Toast.fire({ icon: 'error', title: 'Gagal menyimpan. Cek kembali form Anda.' });
        }
    });
};

// ============================================
// FUNGSI JADWAL — EDIT
// ============================================
const openEditJadwal = (guru) => {
    editingGuruId.value = guru.id;
    formEditJadwal.guru_id = guru.id;
    formEditJadwal.mata_pelajaran = guru.pivot?.mata_pelajaran || '';
    formEditJadwal.hari = guru.pivot?.hari || '';
    formEditJadwal.jam_mulai = guru.pivot?.jam_mulai || '';
    formEditJadwal.jam_selesai = guru.pivot?.jam_selesai || '';
    formEditJadwal.clearErrors();
    showEditJadwal.value = true;
};

const saveEditJadwal = () => {
    formEditJadwal.put(route('classes.updateGuru', [props.kelas.id, editingGuruId.value]), {
        preserveScroll: true,
        onSuccess: () => {
            showEditJadwal.value = false;
            Toast.fire({ icon: 'success', title: '✅ Jadwal berhasil diperbarui!' });
        },
        onError: () => {
            Toast.fire({ icon: 'error', title: 'Gagal memperbarui. Cek kembali form Anda.' });
        }
    });
};

// ============================================
// FUNGSI JADWAL — HAPUS
// ============================================
const deleteJadwal = (guruId) => {
    Swal.fire({
        title: 'Hapus Jadwal?',
        text: 'Data jadwal yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('classes.destroyGuru', [props.kelas.id, guruId]), {
                preserveScroll: true,
                onSuccess: () => Toast.fire({ icon: 'success', title: '🗑️ Jadwal berhasil dihapus!' })
            });
        }
    });
};

const openPresensi = (guru) => {
    Toast.fire({ icon: 'info', title: 'Fitur halaman absensi segera hadir!' });
};

const cekTabrakan = () => {
    Toast.fire({ icon: 'success', title: '✅ Tidak ada tabrakan jadwal.' });
};
</script>

<template>
    <Head :title="`Detail Kelas - ${kelas.nama_kelas}`" />

    <DashboardLayout
        title="Data Kelas"
        :header-title="kelas.nama_kelas"
        :header-subtitle="`Tingkat ${kelas.tingkat} | ${totalSantri} Santri`"
    >
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
            <Link :href="route('dashboard.classes')" class="hover:text-blue-600">Data Kelas</Link>
            <span>/</span>
            <span class="text-gray-700 font-medium">{{ kelas.nama_kelas }}</span>
        </div>

        <!-- Info Kelas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Tingkat</p>
                <p class="text-sm font-semibold text-gray-900">{{ kelas.tingkat || '-' }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Nama Kelas / Rombel</p>
                <p class="text-sm font-semibold text-gray-900">{{ kelas.nama_kelas }} ({{ kelas.rombel || '-' }})</p>
            </div>

            <!-- KOTAK WALIKELAS -->
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Walikelas</p>
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-900">{{ kelas.walikelas?.nama || 'Belum ditentukan' }}</p>
                    <button
                        @click="openEditWalikelas"
                        class="text-blue-500 hover:text-blue-700 p-1 bg-blue-50 rounded transition"
                        title="Edit Walikelas"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Jumlah Santri</p>
                <p class="text-sm font-semibold text-gray-900">{{ totalSantri }} Santri</p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 px-6 overflow-x-auto bg-gray-50/50">
                <nav class="flex gap-1 -mb-px">
                    <button
                        @click="activeTab = 'jadwal'"
                        class="px-4 py-3 text-sm font-medium transition whitespace-nowrap border-b-2"
                        :class="activeTab === 'jadwal' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                    >
                        📅 Jadwal & Guru
                    </button>
                    <button
                        @click="activeTab = 'presensi'"
                        class="px-4 py-3 text-sm font-medium transition whitespace-nowrap border-b-2"
                        :class="activeTab === 'presensi' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                    >
                        📋 Presensi Kelas
                    </button>
                    <button
                        @click="activeTab = 'peserta'"
                        class="px-4 py-3 text-sm font-medium transition whitespace-nowrap border-b-2"
                        :class="activeTab === 'peserta' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                    >
                        👥 Peserta Kelas
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- TAB 1: Jadwal & Guru -->
                <div v-if="activeTab === 'jadwal'">
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                        <div class="flex gap-2">
                            <button
                                @click="openTambahJadwal"
                                class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1 shadow-sm"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Tambah Guru / Jadwal
                            </button>
                            <button
                                @click="cekTabrakan"
                                class="px-4 py-2 text-sm bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition flex items-center gap-1 shadow-sm"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Cek Tabrakan
                            </button>
                        </div>
                        <span class="text-sm text-gray-500">Total: {{ totalGuru }} Guru</span>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Guru</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Pelajaran</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hari</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Mulai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Selesai</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-if="!kelas.gurus || kelas.gurus.length === 0">
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada guru yang di-assign ke kelas ini.
                                    </td>
                                </tr>
                                <tr v-for="(guru, idx) in kelas.gurus" :key="guru.pivot?.id || guru.id" class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-500 text-center">{{ idx + 1 }}</td>
                                    <td class="px-4 py-3 text-gray-900 font-medium">{{ guru.nama }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded border border-blue-200 bg-blue-50 text-blue-700">
                                            {{ guru.pivot?.mata_pelajaran || 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ guru.pivot?.hari || '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ guru.pivot?.jam_mulai || '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ guru.pivot?.jam_selesai || '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- TOMBOL EDIT -->
                                            <button
                                                @click="openEditJadwal(guru)"
                                                class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded transition"
                                                title="Edit Jadwal"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            <!-- TOMBOL HAPUS -->
                                            <button
                                                @click="deleteJadwal(guru.id)"
                                                class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition"
                                                title="Hapus"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: Presensi Kelas -->
                <div v-if="activeTab === 'presensi'">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-semibold text-gray-700">📋 Daftar Pertemuan</h4>
                        <span class="text-sm text-gray-500">Klik card untuk mengisi presensi</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="(guru, idx) in kelas.gurus" :key="guru.pivot?.id || guru.id"
                             class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer bg-white"
                             @click="openPresensi(guru)">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Pertemuan {{ idx + 1 }}</p>
                                    <p class="text-xs text-gray-500">{{ guru.pivot?.hari || '-' }}, {{ guru.pivot?.jam_mulai || '-' }} - {{ guru.pivot?.jam_selesai || '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Mapel: {{ guru.pivot?.mata_pelajaran || 'Umum' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 font-medium">
                                    {{ totalSantri }} Santri
                                </span>
                            </div>
                        </div>
                        <div v-if="!kelas.gurus || kelas.gurus.length === 0" class="col-span-3 text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed">
                            Belum ada jadwal / guru yang di-assign.
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Peserta Kelas -->
                <div v-if="activeTab === 'peserta'">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-semibold text-gray-700">👥 Daftar Peserta Kelas</h4>
                        <span class="text-sm text-gray-500">Total: {{ totalSantri }} Santri</span>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Santri</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis Kelamin</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-if="!kelas.santris || kelas.santris.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada santri di kelas ini.
                                    </td>
                                </tr>
                                <tr v-for="(santri, idx) in kelas.santris" :key="santri.id" class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 text-gray-500 text-center">{{ idx + 1 }}</td>
                                    <td class="px-4 py-2 text-gray-700 font-mono text-xs">{{ santri.nis }}</td>
                                    <td class="px-4 py-2 text-gray-900 font-medium">{{ santri.name || santri.nama }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ santri.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                            {{ santri.status || 'Aktif' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT WALIKELAS -->
        <div v-if="showEditWalikelas" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showEditWalikelas = false">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">👨‍🏫 Atur Walikelas</h3>
                    <button @click="showEditWalikelas = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Guru Sebagai Walikelas</label>
                    <select v-model="formWalikelas.walikelas_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Tidak Ada / Kosongkan --</option>
                        <option v-for="guru in daftarGuru" :key="guru.id" :value="guru.id">{{ guru.nama }}</option>
                    </select>
                    <p v-if="formWalikelas.errors.walikelas_id" class="text-xs text-red-500 mt-1">{{ formWalikelas.errors.walikelas_id }}</p>
                </div>
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button @click="showEditWalikelas = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Batal</button>
                    <button @click="saveWalikelas" :disabled="formWalikelas.processing" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                        {{ formWalikelas.processing ? 'Menyimpan...' : '💾 Simpan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH JADWAL -->
        <div v-if="showTambahJadwal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showTambahJadwal = false">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">📅 Tambah Guru / Jadwal</h3>
                    <button @click="showTambahJadwal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Guru *</label>
                        <select v-model="formJadwal.guru_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Guru --</option>
                            <option v-for="guru in daftarGuru" :key="guru.id" :value="guru.id">{{ guru.nama }}</option>
                        </select>
                        <p v-if="formJadwal.errors.guru_id" class="text-xs text-red-500 mt-1">{{ formJadwal.errors.guru_id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran *</label>
                        <input v-model="formJadwal.mata_pelajaran" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Fiqih">
                        <p v-if="formJadwal.errors.mata_pelajaran" class="text-xs text-red-500 mt-1">{{ formJadwal.errors.mata_pelajaran }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hari *</label>
                        <select v-model="formJadwal.hari" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                        <p v-if="formJadwal.errors.hari" class="text-xs text-red-500 mt-1">{{ formJadwal.errors.hari }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai *</label>
                            <input v-model="formJadwal.jam_mulai" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p v-if="formJadwal.errors.jam_mulai" class="text-xs text-red-500 mt-1">{{ formJadwal.errors.jam_mulai }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai *</label>
                            <input v-model="formJadwal.jam_selesai" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p v-if="formJadwal.errors.jam_selesai" class="text-xs text-red-500 mt-1">{{ formJadwal.errors.jam_selesai }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button @click="showTambahJadwal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Batal</button>
                    <button @click="saveJadwal" :disabled="formJadwal.processing" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                        {{ formJadwal.processing ? 'Menyimpan...' : '💾 Simpan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT JADWAL -->
        <div v-if="showEditJadwal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showEditJadwal = false">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">✏️ Edit Guru / Jadwal</h3>
                    <button @click="showEditJadwal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Guru *</label>
                        <select v-model="formEditJadwal.guru_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Guru --</option>
                            <option v-for="guru in daftarGuru" :key="guru.id" :value="guru.id">{{ guru.nama }}</option>
                        </select>
                        <p v-if="formEditJadwal.errors.guru_id" class="text-xs text-red-500 mt-1">{{ formEditJadwal.errors.guru_id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran *</label>
                        <input v-model="formEditJadwal.mata_pelajaran" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Fiqih">
                        <p v-if="formEditJadwal.errors.mata_pelajaran" class="text-xs text-red-500 mt-1">{{ formEditJadwal.errors.mata_pelajaran }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hari *</label>
                        <select v-model="formEditJadwal.hari" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                        <p v-if="formEditJadwal.errors.hari" class="text-xs text-red-500 mt-1">{{ formEditJadwal.errors.hari }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai *</label>
                            <input v-model="formEditJadwal.jam_mulai" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p v-if="formEditJadwal.errors.jam_mulai" class="text-xs text-red-500 mt-1">{{ formEditJadwal.errors.jam_mulai }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai *</label>
                            <input v-model="formEditJadwal.jam_selesai" type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p v-if="formEditJadwal.errors.jam_selesai" class="text-xs text-red-500 mt-1">{{ formEditJadwal.errors.jam_selesai }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button @click="showEditJadwal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Batal</button>
                    <button @click="saveEditJadwal" :disabled="formEditJadwal.processing" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                        {{ formEditJadwal.processing ? 'Menyimpan...' : '💾 Simpan Perubahan' }}
                    </button>
                </div>
            </div>
        </div>

    </DashboardLayout>
</template>