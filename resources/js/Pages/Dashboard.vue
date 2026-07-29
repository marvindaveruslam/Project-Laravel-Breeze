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
        value: '10',
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
        value: '8',
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
        value: '2',
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
        value: '5',
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
// DATA SANTRI
// ============================================
const students = ref([
    { id: 1, name: 'Ahmad Fauzi', class: '3A', status: 'active', gender: 'Laki-laki', age: 15, parent: 'H. Fauzi Rahman' },
    { id: 2, name: 'Budi Santoso', class: '2B', status: 'active', gender: 'Laki-laki', age: 14, parent: 'Hj. Siti Aminah' },
    { id: 3, name: 'Citra Dewi', class: '1A', status: 'active', gender: 'Perempuan', age: 12, parent: 'H. Agus Salim' },
    { id: 4, name: 'Dian Pratama', class: '3B', status: 'active', gender: 'Laki-laki', age: 15, parent: 'Hj. Fatimah' },
    { id: 5, name: 'Eka Putri', class: '2A', status: 'inactive', gender: 'Perempuan', age: 13, parent: 'H. M. Rizky' },
    { id: 6, name: 'Fajar Nugroho', class: '1B', status: 'active', gender: 'Laki-laki', age: 12, parent: 'Hj. Dewi' },
    { id: 7, name: 'Gita Permata', class: '3A', status: 'active', gender: 'Perempuan', age: 16, parent: 'H. Budi Santoso' },
    { id: 8, name: 'Hendra Wijaya', class: '2A', status: 'inactive', gender: 'Laki-laki', age: 14, parent: 'Hj. Rina' },
    { id: 9, name: 'Indah Lestari', class: '3B', status: 'active', gender: 'Perempuan', age: 15, parent: 'H. Joko' },
    { id: 10, name: 'Joko Susilo', class: '1A', status: 'active', gender: 'Laki-laki', age: 11, parent: 'Hj. Suryani' },
]);

// ============================================
// DATA GURU
// ============================================
const teachers = ref([
    { id: 1, name: 'Ustadz Ahmad Rifa\'i, S.Ag.', subject: 'Tahfidz & Tafsir', class: '3A', phone: '0812-3456-7890' },
    { id: 2, name: 'Ustadzah Siti Khadijah, S.Pd.I.', subject: 'Fiqh & Akhlaq', class: '3B', phone: '0813-4567-8901' },
    { id: 3, name: 'Ustadz Muhammad Ali, S.Pd.', subject: 'Nahwu & Sharaf', class: '2A', phone: '0814-5678-9012' },
    { id: 4, name: 'Ustadzah Fatimah Azzahra, M.Pd.', subject: 'Tajwid & Qira\'ah', class: '2B', phone: '0815-6789-0123' },
    { id: 5, name: 'Ustadz Abdul Rahman, Lc.', subject: 'Hadits & Tafsir', class: '1A', phone: '0816-7890-1234' },
    { id: 6, name: 'Ustadzah Maimunah, S.Ag.', subject: 'Tarikh & Kebudayaan', class: '1B', phone: '0817-8901-2345' },
]);

// ============================================
// SEARCH STATE
// ============================================
const searchQuery = ref('');
const searchResults = ref([]);

// ============================================
// MODAL STATE
// ============================================
const showAddStudentModal = ref(false);
const showAddTeacherModal = ref(false);
const showClassDetail = ref(false);
const showAddTeacherToClassModal = ref(false);
const selectedClass = ref(null);
const isSubmitting = ref(false);

// ============================================
// FORM TAMBAH SANTRI
// ============================================
const newStudent = ref({
    name: '',
    class: '',
    status: 'active',
    gender: 'Laki-laki',
    age: '',
    parent: '',
});

const classOptions = ['1A', '1B', '2A', '2B', '3A', '3B'];

// ============================================
// FORM TAMBAH GURU
// ============================================
const newTeacher = ref({
    name: '',
    subject: '',
    class: '',
    phone: '',
});

// ============================================
// FORM TAMBAH GURU KE KELAS
// ============================================
const teacherToClass = ref({
    teacherId: '',
    class: '',
});

// ============================================
// COMPUTED: CLASS SUMMARY
// ============================================
const classSummary = computed(() => {
    const classes = ['1A', '1B', '2A', '2B', '3A', '3B'];
    return classes.map(cls => {
        const studentsInClass = students.value.filter(s => s.class === cls);
        const total = studentsInClass.length;
        const active = studentsInClass.filter(s => s.status === 'active').length;
        const inactive = studentsInClass.filter(s => s.status === 'inactive').length;
        const teachersInClass = teachers.value.filter(t => t.class === cls);
        return { 
            name: cls, 
            total, 
            active, 
            inactive,
            teachers: teachersInClass,
            students: studentsInClass
        };
    });
});

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
// FUNGSI SEARCH
// ============================================
const searchStudents = () => {
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        return;
    }

    const query = searchQuery.value.toLowerCase().trim();
    
    const studentResults = students.value.filter(student => 
        student.name.toLowerCase().includes(query) ||
        student.class.toLowerCase().includes(query) ||
        student.parent.toLowerCase().includes(query)
    );

    const teacherResults = teachers.value.filter(teacher =>
        teacher.name.toLowerCase().includes(query) ||
        teacher.subject.toLowerCase().includes(query) ||
        teacher.class.toLowerCase().includes(query)
    );

    searchResults.value = [
        ...studentResults.map(s => ({ ...s, type: 'student' })),
        ...teacherResults.map(t => ({ ...t, type: 'teacher' }))
    ];
};

const clearSearch = () => {
    searchQuery.value = '';
    searchResults.value = [];
};

// ============================================
// FUNGSI TAMBAH SANTRI
// ============================================
const resetStudentForm = () => {
    newStudent.value = {
        name: '',
        class: '',
        status: 'active',
        gender: 'Laki-laki',
        age: '',
        parent: '',
    };
};

const submitStudent = () => {
    if (!newStudent.value.name || !newStudent.value.class || !newStudent.value.age) {
        alert('Mohon lengkapi data yang diperlukan (Nama, Kelas, dan Usia)');
        return;
    }

    isSubmitting.value = true;

    setTimeout(() => {
        const newId = students.value.length + 1;
        students.value.push({
            id: newId,
            name: newStudent.value.name,
            class: newStudent.value.class,
            status: newStudent.value.status,
            gender: newStudent.value.gender,
            age: parseInt(newStudent.value.age),
            parent: newStudent.value.parent || '-',
        });

        updateStats();
        resetStudentForm();
        showAddStudentModal.value = false;
        isSubmitting.value = false;
        alert('✅ Santri berhasil ditambahkan!');
    }, 1000);
};

// ============================================
// FUNGSI TAMBAH GURU
// ============================================
const resetTeacherForm = () => {
    newTeacher.value = {
        name: '',
        subject: '',
        class: '',
        phone: '',
    };
};

const submitTeacher = () => {
    if (!newTeacher.value.name || !newTeacher.value.subject || !newTeacher.value.class) {
        alert('Mohon lengkapi data yang diperlukan (Nama, Mata Pelajaran, dan Kelas)');
        return;
    }

    isSubmitting.value = true;

    setTimeout(() => {
        const newId = teachers.value.length + 1;
        teachers.value.push({
            id: newId,
            name: newTeacher.value.name,
            subject: newTeacher.value.subject,
            class: newTeacher.value.class,
            phone: newTeacher.value.phone || '-',
        });

        resetTeacherForm();
        showAddTeacherModal.value = false;
        isSubmitting.value = false;
        alert('✅ Guru berhasil ditambahkan!');
    }, 1000);
};

// ============================================
// FUNGSI TAMBAH GURU KE KELAS
// ============================================
const openAddTeacherToClass = (cls) => {
    selectedClass.value = cls;
    teacherToClass.value.class = cls;
    showAddTeacherToClassModal.value = true;
};

const submitTeacherToClass = () => {
    if (!teacherToClass.value.teacherId) {
        alert('Silakan pilih guru terlebih dahulu');
        return;
    }

    isSubmitting.value = true;

    setTimeout(() => {
        const teacher = teachers.value.find(t => t.id === parseInt(teacherToClass.value.teacherId));
        if (teacher) {
            teacher.class = teacherToClass.value.class;
            alert(`✅ Guru ${teacher.name} berhasil ditambahkan ke kelas ${teacherToClass.value.class}`);
        }
        showAddTeacherToClassModal.value = false;
        isSubmitting.value = false;
        teacherToClass.value = { teacherId: '', class: '' };
    }, 1000);
};

// ============================================
// FUNGSI OPEN CLASS DETAIL
// ============================================
const openClassDetail = (cls) => {
    selectedClass.value = cls;
    showClassDetail.value = true;
};

// ============================================
// UPDATE STATS
// ============================================
const updateStats = () => {
    const total = students.value.length;
    const active = students.value.filter(s => s.status === 'active').length;
    const inactive = students.value.filter(s => s.status === 'inactive').length;
    
    stats.value[0].value = total.toString();
    stats.value[1].value = active.toString();
    stats.value[2].value = inactive.toString();
};

// ============================================
// FUNGSI HELPERS
// ============================================
const getStatusBadge = (status) => {
    return status === 'active' 
        ? 'bg-green-100 text-green-700' 
        : 'bg-red-100 text-red-700';
};

const getStatusText = (status) => {
    return status === 'active' ? 'Aktif' : 'Non-Aktif';
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
</script>

<template>
    <Head title="Dashboard" />

    <DashboardLayout 
        title="Dashboard Utama"
        header-title="📊 Dashboard Utama"
        header-subtitle="Selamat datang kembali, Adi | Pimpinan"
    >
        <!-- ========================================== -->
        <!-- SEARCH BAR + BUTTONS                      -->
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
                        @input="searchStudents"
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
                        @click="showAddStudentModal = true"
                        class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition shadow-sm flex items-center gap-2 whitespace-nowrap text-sm"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('plus')" />
                        </svg>
                        Tambah Santri
                    </button>
                    <button 
                        @click="showAddTeacherModal = true"
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
                         class="px-6 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                     :class="result.type === 'student' ? 'bg-blue-500' : 'bg-purple-500'">
                                    {{ result.type === 'student' ? 'S' : 'G' }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-gray-800">{{ result.name }}</p>
                                        <span class="text-xs px-2 py-0.5 rounded-full" 
                                              :class="result.type === 'student' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'">
                                            {{ result.type === 'student' ? 'Santri' : 'Guru' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                                        <span v-if="result.type === 'student'">
                                            Kelas: <span class="font-medium text-gray-700">{{ result.class }}</span>
                                        </span>
                                        <span v-else>
                                            Mengajar: <span class="font-medium text-gray-700">{{ result.class }}</span>
                                        </span>
                                        <span v-if="result.type === 'student'" class="text-gray-300">|</span>
                                        <span v-if="result.type === 'student'">
                                            Status: <span class="font-medium" :class="result.status === 'active' ? 'text-green-600' : 'text-red-600'">
                                                {{ getStatusText(result.status) }}
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-300">|</span>
                                        <span v-if="result.type === 'teacher'">
                                            Mata Pelajaran: <span class="font-medium text-gray-700">{{ result.subject }}</span>
                                        </span>
                                        <span v-if="result.type === 'student' && result.parent" class="text-gray-300">|</span>
                                        <span v-if="result.type === 'student' && result.parent">
                                            Orang Tua: <span class="font-medium text-gray-700">{{ result.parent }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span v-if="result.type === 'student'" 
                                      class="text-xs px-2 py-1 rounded-full"
                                      :class="getStatusBadge(result.status)">
                                    {{ getStatusText(result.status) }}
                                </span>
                                <span v-else class="text-xs text-gray-400">
                                    {{ result.phone || 'No HP: -' }}
                                </span>
                            </div>
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
        <!-- DAFTAR KELAS + DETAIL                     -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">📚 Manajemen Kelas</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Kelola santri dan guru per kelas</p>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    <div v-for="cls in classSummary" :key="cls.name" 
                         class="px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                    {{ cls.name }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-semibold text-gray-800">{{ cls.name }}</span>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                            {{ cls.total }} Santri
                                        </span>
                                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-0.5 rounded-full">
                                            {{ cls.teachers.length }} Guru
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-green-600">● {{ cls.active }} Aktif</span>
                                        <span class="text-xs text-red-600">● {{ cls.inactive }} Non-Aktif</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    @click="openAddTeacherToClass(cls.name)"
                                    class="px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 text-xs font-medium rounded-lg transition"
                                >
                                    + Guru
                                </button>
                                <button 
                                    @click="openClassDetail(cls)"
                                    class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-lg transition"
                                >
                                    Detail →
                                </button>
                            </div>
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
                        <p class="text-sm font-bold text-purple-800">{{ teachers.length }} Orang</p>
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

        <!-- ========================================== -->
        <!-- MODAL TAMBAH SANTRI                       -->
        <!-- ========================================== -->
        <div v-if="showAddStudentModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showAddStudentModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-auto p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">📝 Tambah Santri Baru</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Isi data santri dengan lengkap</p>
                        </div>
                        <button @click="showAddStudentModal = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitStudent" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input v-model="newStudent.name" type="text" placeholder="Masukkan nama lengkap santri" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                                <select v-model="newStudent.class" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <option value="">Pilih Kelas</option>
                                    <option v-for="cls in classOptions" :key="cls" :value="cls">{{ cls }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Usia <span class="text-red-500">*</span></label>
                                <input v-model="newStudent.age" type="number" min="5" max="25" placeholder="Usia" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <select v-model="newStudent.gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select v-model="newStudent.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Non-Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua <span class="text-gray-400 text-xs">(opsional)</span></label>
                            <input v-model="newStudent.parent" type="text" placeholder="Masukkan nama orang tua" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showAddStudentModal = false" 
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
        <div v-if="showAddTeacherModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showAddTeacherModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-auto p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">👨‍🏫 Tambah Guru Baru</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Isi data guru dengan lengkap</p>
                        </div>
                        <button @click="showAddTeacherModal = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitTeacher" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input v-model="newTeacher.name" type="text" placeholder="Masukkan nama lengkap guru" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                            <input v-model="newTeacher.subject" type="text" placeholder="Masukkan mata pelajaran yang diajar" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas yang Diajar <span class="text-red-500">*</span></label>
                            <select v-model="newTeacher.class" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                <option value="">Pilih Kelas</option>
                                <option v-for="cls in classOptions" :key="cls" :value="cls">{{ cls }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon <span class="text-gray-400 text-xs">(opsional)</span></label>
                            <input v-model="newTeacher.phone" type="text" placeholder="Masukkan nomor telepon" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showAddTeacherModal = false" 
                                    class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmitting" 
                                    class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Guru' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL DETAIL KELAS                        -->
        <!-- ========================================== -->
        <div v-if="showClassDetail && selectedClass" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showClassDetail = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-auto p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6 sticky top-0 bg-white z-10 pb-4 border-b">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">📚 Detail Kelas {{ selectedClass.name }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Total {{ selectedClass.total }} Santri · {{ selectedClass.teachers.length }} Guru</p>
                        </div>
                        <button @click="showClassDetail = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Daftar Guru -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-700">👨‍🏫 Guru Pengajar</h4>
                            <button @click="openAddTeacherToClass(selectedClass.name)" 
                                    class="text-xs bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded-lg transition font-medium">
                                + Tambah Guru
                            </button>
                        </div>
                        <div v-if="selectedClass.teachers.length > 0" class="space-y-2">
                            <div v-for="teacher in selectedClass.teachers" :key="teacher.id" 
                                 class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ teacher.name }}</p>
                                    <p class="text-xs text-gray-500">{{ teacher.subject }}</p>
                                </div>
                                <span class="text-xs text-gray-400">{{ teacher.phone }}</span>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-400 text-center py-4">Belum ada guru yang mengajar di kelas ini</p>
                    </div>

                    <!-- Daftar Santri -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-700">👨‍🎓 Daftar Santri</h4>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-green-600">● {{ selectedClass.active }} Aktif</span>
                                <span class="text-xs text-red-600">● {{ selectedClass.inactive }} Non-Aktif</span>
                            </div>
                        </div>
                        <div v-if="selectedClass.students.length > 0" class="space-y-2 max-h-60 overflow-y-auto">
                            <div v-for="student in selectedClass.students" :key="student.id" 
                                 class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ student.name }}</p>
                                    <p class="text-xs text-gray-500">{{ student.gender }} · {{ student.age }} tahun</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400">{{ student.parent }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full" :class="getStatusBadge(student.status)">
                                        {{ getStatusText(student.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-400 text-center py-4">Belum ada santri di kelas ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL TAMBAH GURU KE KELAS                -->
        <!-- ========================================== -->
        <div v-if="showAddTeacherToClassModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showAddTeacherToClassModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">➕ Tambah Guru ke Kelas</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Kelas: <span class="font-semibold">{{ teacherToClass.class }}</span></p>
                        </div>
                        <button @click="showAddTeacherToClassModal = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitTeacherToClass" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Guru <span class="text-red-500">*</span></label>
                            <select v-model="teacherToClass.teacherId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                                <option value="">Pilih Guru</option>
                                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                    {{ teacher.name }} - {{ teacher.subject }}
                                </option>
                            </select>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showAddTeacherToClassModal = false" 
                                    class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmitting" 
                                    class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ isSubmitting ? 'Memproses...' : 'Tambahkan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>