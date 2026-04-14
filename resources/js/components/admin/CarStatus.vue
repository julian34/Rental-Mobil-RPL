<template>
    <div class="space-y-5">

        <!-- Loading -->
        <div v-if="loading" class="text-center py-16 text-gray-400">
            <svg class="w-8 h-8 mx-auto mb-3 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Memuat data kendaraan...
        </div>

        <!-- Error -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-red-600 text-sm">
            {{ error }}
            <button @click="fetchAll" class="ml-3 underline font-medium">Coba lagi</button>
        </div>

        <template v-else>
            <!-- Status summary strip -->
            <div class="grid grid-cols-4 gap-4">
                <div v-for="s in statusSummary" :key="s.label"
                    class="rounded-xl p-4 flex items-center gap-4 shadow-sm border" :class="s.bg">
                    <div class="text-3xl">{{ s.icon }}</div>
                    <div>
                        <p class="text-2xl font-bold" :class="s.text">{{ s.count }}</p>
                        <p class="text-xs font-semibold" :class="s.text">{{ s.label }}</p>
                    </div>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
                <div class="flex gap-2">
                    <input v-model="search" type="text" placeholder="Cari mobil..."
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-48" />
                </div>
                <div class="flex items-center gap-3">
                    <p class="text-xs text-gray-400">Menampilkan mobil yang tidak sedang disewa</p>
                    <button @click="fetchAll"
                        class="flex items-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                        ↻ Refresh
                    </button>
                </div>
            </div>

            <!-- Cards grid — only Available & Maintenance cars -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div
                    v-for="car in filtered"
                    :key="car.car_id"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition"
                >
                    <!-- Car header -->
                    <div class="h-24 flex items-center justify-center text-4xl" :class="headerBg(car.car_status)">
                        <span>{{ brandIcon(car.brand) }}</span>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="font-bold text-gray-900 text-sm">{{ car.brand }} {{ car.model }}</h3>
                            <span class="text-xs font-mono text-gray-400 shrink-0">{{ car.car_code }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-1">{{ car.license_plate }}</p>
                        <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
                            <span v-if="car.year">{{ car.year }}</span>
                            <span v-if="car.color">· {{ car.color }}</span>
                            <span>· Rp {{ formatPrice(car.rental_price_per_day) }}/hari</span>
                        </div>

                        <!-- Status badge -->
                        <div class="mb-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold border"
                                :class="car.car_status === 'Available'
                                    ? 'bg-green-500 text-white border-green-500'
                                    : 'bg-orange-400 text-white border-orange-400'">
                                {{ car.car_status }}
                            </span>
                        </div>

                        <!-- Condition notes -->
                        <div class="mb-3">
                            <label class="text-xs text-gray-400 mb-1 block">Kondisi</label>
                            <p class="text-xs text-gray-600">{{ car.car_condition || 'Tidak ada catatan' }}</p>
                        </div>

                        <!-- Maintenance button -->
                        <button
                            v-if="car.car_status === 'Available'"
                            @click="openMaintenanceWizard(car)"
                            class="w-full px-3 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition flex items-center justify-center gap-1"
                        >
                            🔧 Kirim ke Maintenance
                        </button>
                        <p v-else class="text-xs text-orange-600 font-medium text-center">Sedang dalam perbaikan</p>

                        <p class="text-[10px] text-gray-300 mt-2">Diperbarui: {{ formatDate(car.updated_at) }}</p>
                    </div>
                </div>
            </div>

            <div v-if="filtered.length === 0" class="text-center py-12 text-gray-400">Tidak ada mobil yang cocok dengan filter.</div>

            <!-- Active Maintenance Records table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">🔧 Catatan Maintenance Aktif</h3>
                    <span class="text-xs bg-orange-100 text-orange-700 font-semibold rounded-full px-2 py-0.5">{{ activeRecords.length }} aktif</span>
                </div>
                <div v-if="activeRecords.length === 0" class="text-center py-8 text-gray-400 text-sm">
                    Tidak ada maintenance aktif saat ini.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr class="text-xs text-gray-500 uppercase tracking-wide">
                                <th class="text-left px-5 py-3">Kendaraan</th>
                                <th class="text-left px-5 py-3">Deskripsi</th>
                                <th class="text-left px-5 py-3">Bengkel</th>
                                <th class="text-right px-5 py-3">Biaya</th>
                                <th class="text-left px-5 py-3">Staff</th>
                                <th class="text-center px-5 py-3">Status</th>
                                <th class="text-left px-5 py-3">Catatan Staff</th>
                                <th class="text-center px-5 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="rec in activeRecords" :key="rec.id" class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-800 whitespace-nowrap">
                                    {{ rec.car?.brand }} {{ rec.car?.model }}
                                    <p class="text-[10px] text-gray-400">{{ rec.car?.license_plate }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-600 text-xs max-w-[180px]">{{ rec.description }}</td>
                                <td class="px-5 py-3 text-gray-600 text-xs whitespace-nowrap">{{ rec.workshop_name }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-800 whitespace-nowrap">Rp {{ formatPrice(rec.repair_cost) }}</td>
                                <td class="px-5 py-3 text-xs text-gray-600 whitespace-nowrap">{{ rec.assigned_staff?.name || '-' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                        :class="rec.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700'">
                                        {{ rec.status === 'pending' ? 'Menunggu' : 'Dalam Proses' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500 max-w-[180px]">{{ rec.staff_notes || '-' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <button
                                        @click="completeMaintenance(rec)"
                                        :disabled="completing === rec.id"
                                        class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-semibold rounded-lg transition disabled:opacity-50 whitespace-nowrap"
                                    >
                                        {{ completing === rec.id ? 'Memproses...' : '✓ Selesai' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Completed Maintenance History -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">📋 Riwayat Maintenance Selesai</h3>
                </div>
                <div v-if="completedRecords.length === 0" class="text-center py-8 text-gray-400 text-sm">
                    Belum ada riwayat maintenance.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr class="text-xs text-gray-500 uppercase tracking-wide">
                                <th class="text-left px-5 py-3">Kendaraan</th>
                                <th class="text-left px-5 py-3">Deskripsi</th>
                                <th class="text-left px-5 py-3">Bengkel</th>
                                <th class="text-right px-5 py-3">Biaya</th>
                                <th class="text-left px-5 py-3">Staff</th>
                                <th class="text-left px-5 py-3">Catatan Staff</th>
                                <th class="text-left px-5 py-3">Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="rec in completedRecords" :key="rec.id" class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-800 whitespace-nowrap">
                                    {{ rec.car?.brand }} {{ rec.car?.model }}
                                    <p class="text-[10px] text-gray-400">{{ rec.car?.license_plate }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-600 text-xs max-w-[180px]">{{ rec.description }}</td>
                                <td class="px-5 py-3 text-gray-600 text-xs">{{ rec.workshop_name }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-800">Rp {{ formatPrice(rec.repair_cost) }}</td>
                                <td class="px-5 py-3 text-xs text-gray-600">{{ rec.assigned_staff?.name || '-' }}</td>
                                <td class="px-5 py-3 text-xs text-gray-500 max-w-[180px]">{{ rec.staff_notes || '-' }}</td>
                                <td class="px-5 py-3 text-xs text-gray-500">{{ formatDate(rec.completed_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <!-- ==================== MAINTENANCE WIZARD MODAL ==================== -->
        <div v-if="showWizard" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showWizard = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-orange-50 rounded-t-2xl">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">🔧 Form Maintenance</h3>
                        <p class="text-xs text-gray-500">Step {{ wizardStep }} dari 3</p>
                    </div>
                    <button @click="showWizard = false" class="text-gray-400 hover:text-gray-700 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Step indicator -->
                <div class="px-6 pt-4 pb-2">
                    <div class="flex gap-2">
                        <div v-for="n in 3" :key="n" class="flex-1 h-1.5 rounded-full" :class="n <= wizardStep ? 'bg-orange-500' : 'bg-gray-200'"></div>
                    </div>
                </div>

                <!-- Step 1: Info Mobil (display) + Deskripsi Kerusakan -->
                <div v-if="wizardStep === 1" class="px-6 py-4 space-y-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">Kendaraan</p>
                        <p class="font-bold text-gray-900">{{ wizardCar?.brand }} {{ wizardCar?.model }}</p>
                        <p class="text-xs text-gray-500">{{ wizardCar?.license_plate }} · {{ wizardCar?.car_code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kerusakan / Yang Harus Diperbaiki <span class="text-red-500">*</span></label>
                        <textarea v-model="wizardForm.description" rows="4" placeholder="Contoh: Rem depan aus, perlu penggantian kampas rem dan balancing roda..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                        <p v-if="wizardErrors.description" class="text-red-500 text-xs mt-1">{{ wizardErrors.description }}</p>
                    </div>
                </div>

                <!-- Step 2: Bengkel & Biaya -->
                <div v-if="wizardStep === 2" class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bengkel <span class="text-red-500">*</span></label>
                        <input v-model="wizardForm.workshop_name" type="text" placeholder="Contoh: Bengkel Jaya Motor"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" />
                        <p v-if="wizardErrors.workshop_name" class="text-red-500 text-xs mt-1">{{ wizardErrors.workshop_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Biaya Perbaikan (Rp) <span class="text-red-500">*</span></label>
                        <input v-model.number="wizardForm.repair_cost" type="number" min="0" placeholder="500000"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" />
                        <p v-if="wizardErrors.repair_cost" class="text-red-500 text-xs mt-1">{{ wizardErrors.repair_cost }}</p>
                    </div>
                </div>

                <!-- Step 3: Assign Staff -->
                <div v-if="wizardStep === 3" class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tugaskan Staff</label>
                        <select v-model="wizardForm.assigned_staff_id"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                            <option :value="null">-- Pilih Staff (opsional) --</option>
                            <option v-for="s in staffList" :key="s.id" :value="s.id">{{ s.name }} ({{ s.email }})</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Staff yang ditugaskan akan melihat tugas ini di dashboard mereka dan melaporkan progress perbaikan.</p>
                    </div>

                    <!-- Summary -->
                    <div class="bg-orange-50 rounded-xl p-4 space-y-2">
                        <p class="text-sm font-bold text-gray-800">Ringkasan Maintenance</p>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <p class="text-gray-500">Kendaraan</p>
                            <p class="text-gray-800 font-medium">{{ wizardCar?.brand }} {{ wizardCar?.model }}</p>
                            <p class="text-gray-500">Kerusakan</p>
                            <p class="text-gray-800">{{ wizardForm.description }}</p>
                            <p class="text-gray-500">Bengkel</p>
                            <p class="text-gray-800">{{ wizardForm.workshop_name }}</p>
                            <p class="text-gray-500">Biaya</p>
                            <p class="text-gray-800 font-semibold">Rp {{ formatPrice(wizardForm.repair_cost) }}</p>
                            <p class="text-gray-500">Staff</p>
                            <p class="text-gray-800">{{ selectedStaffName }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                    <button v-if="wizardStep > 1" @click="wizardStep--"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                        ← Kembali
                    </button>
                    <div v-else></div>
                    <button v-if="wizardStep < 3" @click="nextStep"
                        class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition">
                        Lanjut →
                    </button>
                    <button v-else @click="submitMaintenance" :disabled="submitting"
                        class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold rounded-lg transition disabled:opacity-50 flex items-center gap-2">
                        <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        {{ submitting ? 'Menyimpan...' : '✓ Kirim ke Maintenance' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast notification -->
        <div v-if="toast"
            class="fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-lg text-sm font-medium transition-all duration-300"
            :class="toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
            {{ toast.msg }}
        </div>
    </div>
</template>

<script>
export default {
    name: 'CarStatus',
    props: {
        user: { type: Object, required: true },
    },
    data() {
        return {
            search: '',
            loading: false,
            error: null,
            toast: null,
            completing: null,

            cars: [],
            records: [],
            staffList: [],

            // Wizard
            showWizard: false,
            wizardStep: 1,
            wizardCar: null,
            submitting: false,
            wizardForm: {
                description: '',
                workshop_name: '',
                repair_cost: null,
                assigned_staff_id: null,
            },
            wizardErrors: {},
        };
    },
    computed: {
        nonRentedCars() {
            return this.cars.filter(c => c.car_status !== 'Rented');
        },
        filtered() {
            const s = this.search.toLowerCase();
            return this.nonRentedCars.filter(c =>
                !s || `${c.brand} ${c.model}`.toLowerCase().includes(s) || c.license_plate.toLowerCase().includes(s) || c.car_code.toLowerCase().includes(s)
            );
        },
        statusSummary() {
            const avail  = this.cars.filter(c => c.car_status === 'Available').length;
            const rented = this.cars.filter(c => c.car_status === 'Rented').length;
            const maint  = this.cars.filter(c => c.car_status === 'Maintenance').length;
            const active = this.records.filter(r => r.status !== 'completed').length;
            return [
                { label: 'Available',   count: avail,  icon: '✅', bg: 'bg-green-50 border-green-200',  text: 'text-green-700'  },
                { label: 'Rented',      count: rented, icon: '🚗', bg: 'bg-blue-50 border-blue-200',    text: 'text-blue-700'   },
                { label: 'Maintenance', count: maint,  icon: '🔧', bg: 'bg-orange-50 border-orange-200',text: 'text-orange-700' },
                { label: 'Record Aktif',count: active, icon: '📋', bg: 'bg-purple-50 border-purple-200',text: 'text-purple-700' },
            ];
        },
        activeRecords() {
            return this.records.filter(r => r.status !== 'completed');
        },
        completedRecords() {
            return this.records.filter(r => r.status === 'completed');
        },
        selectedStaffName() {
            if (!this.wizardForm.assigned_staff_id) return 'Belum ditugaskan';
            const s = this.staffList.find(x => x.id === this.wizardForm.assigned_staff_id);
            return s ? s.name : '-';
        },
    },
    mounted() {
        this.fetchAll();
    },
    methods: {
        async fetchAll() {
            this.loading = true;
            this.error = null;
            try {
                const [carsRes, recordsRes, staffRes] = await Promise.all([
                    window.axios.get('/api/cars'),
                    window.axios.get('/api/maintenance'),
                    window.axios.get('/api/maintenance/staff-list'),
                ]);
                this.cars = carsRes.data.cars || [];
                this.records = recordsRes.data.records || [];
                this.staffList = staffRes.data.staff || [];
            } catch (e) {
                this.error = 'Gagal memuat data.';
            } finally {
                this.loading = false;
            }
        },
        openMaintenanceWizard(car) {
            this.wizardCar = car;
            this.wizardStep = 1;
            this.wizardForm = { description: '', workshop_name: '', repair_cost: null, assigned_staff_id: null };
            this.wizardErrors = {};
            this.showWizard = true;
        },
        nextStep() {
            this.wizardErrors = {};
            if (this.wizardStep === 1) {
                if (!this.wizardForm.description.trim()) {
                    this.wizardErrors.description = 'Deskripsi kerusakan wajib diisi.';
                    return;
                }
            }
            if (this.wizardStep === 2) {
                if (!this.wizardForm.workshop_name.trim()) {
                    this.wizardErrors.workshop_name = 'Nama bengkel wajib diisi.';
                    return;
                }
                if (!this.wizardForm.repair_cost || this.wizardForm.repair_cost <= 0) {
                    this.wizardErrors.repair_cost = 'Biaya perbaikan harus lebih dari 0.';
                    return;
                }
            }
            this.wizardStep++;
        },
        async submitMaintenance() {
            this.submitting = true;
            try {
                await window.axios.post('/api/maintenance', {
                    car_id: this.wizardCar.car_id,
                    description: this.wizardForm.description,
                    repair_cost: this.wizardForm.repair_cost,
                    workshop_name: this.wizardForm.workshop_name,
                    assigned_staff_id: this.wizardForm.assigned_staff_id,
                });
                this.showWizard = false;
                this.showToast('success', `${this.wizardCar.brand} ${this.wizardCar.model} dikirim ke maintenance.`);
                await this.fetchAll();
            } catch (err) {
                this.showToast('error', err.response?.data?.message ?? 'Gagal membuat maintenance record.');
            } finally {
                this.submitting = false;
            }
        },
        async completeMaintenance(rec) {
            this.completing = rec.id;
            try {
                await window.axios.patch(`/api/maintenance/${rec.id}/complete`);
                this.showToast('success', `Maintenance ${rec.car?.brand} ${rec.car?.model} selesai. Mobil kembali Available.`);
                await this.fetchAll();
            } catch (err) {
                this.showToast('error', err.response?.data?.message ?? 'Gagal menyelesaikan maintenance.');
            } finally {
                this.completing = null;
            }
        },
        formatPrice(val) {
            if (!val) return '0';
            return Number(val).toLocaleString('id-ID');
        },
        formatDate(val) {
            if (!val) return '-';
            return new Date(val).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
            });
        },
        headerBg(status) {
            return {
                Available:   'bg-green-50',
                Rented:      'bg-blue-50',
                Maintenance: 'bg-orange-50',
            }[status] || 'bg-gray-50';
        },
        brandIcon(brand) {
            const map = {
                Toyota: '🚙', Honda: '🚗', Mitsubishi: '🛻', Daihatsu: '🚐',
                Suzuki: '🚘', Nissan: '🚖', Mazda: '🏎️', Isuzu: '🚚',
            };
            const key = Object.keys(map).find(k => brand?.toLowerCase().includes(k.toLowerCase()));
            return key ? map[key] : '🚗';
        },
        showToast(type, msg) {
            this.toast = { type, msg };
            setTimeout(() => { this.toast = null; }, 3000);
        },
    },
};
</script>
