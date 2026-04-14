<template>
    <div class="min-h-screen bg-gray-50 font-sans">

        <!-- ── Top Navbar ──────────────────────────────────────────── -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-700 flex items-center justify-center">
                        <span class="text-white font-bold text-sm">RC</span>
                    </div>
                    <span class="font-bold text-gray-900 text-lg">RentCar</span>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Tab: Cars / My Transactions -->
                    <div class="hidden sm:flex gap-1 bg-gray-100 rounded-full p-1">
                        <button
                            @click="view = 'cars'"
                            :class="['px-4 py-1 rounded-full text-sm font-medium transition', view === 'cars' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700']"
                        >Cari Mobil</button>
                        <button
                            @click="view = 'history'"
                            :class="['px-4 py-1 rounded-full text-sm font-medium transition', view === 'history' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700']"
                        >Transaksi Saya</button>
                    </div>
                    <span class="text-sm text-gray-500 hidden lg:block">
                        Halo, <span class="font-semibold text-gray-800">{{ user.name }}</span>
                    </span>
                    <button
                        @click="$emit('logout')"
                        class="text-sm px-4 py-2 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 transition"
                    >Logout</button>
                </div>
            </div>
        </nav>

        <!-- ── Main Content ────────────────────────────────────────── -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Welcome Banner -->
            <div class="bg-blue-700 rounded-2xl p-7 mb-8 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold mb-1">Halo, {{ user.name }}! 👋</h1>
                    <p class="text-blue-200 text-sm">Temukan mobil terbaik untuk perjalanan Anda hari ini.</p>
                </div>
                <div class="text-5xl select-none">🚗</div>
            </div>

            <!-- ── CAR LIST VIEW ─────────────────────────────────── -->
            <template v-if="view === 'cars'">
                <!-- Filter bar -->
                <div class="flex flex-col sm:flex-row gap-3 mb-6 items-start sm:items-center">
                    <h2 class="text-xl font-bold text-gray-900 mr-auto">Daftar Mobil</h2>
                    <div class="flex gap-2 flex-wrap">
                        <button
                            v-for="f in filters"
                            :key="f.value"
                            @click="activeFilter = f.value"
                            :class="[
                                'px-4 py-1.5 rounded-full text-sm font-medium transition',
                                activeFilter === f.value
                                    ? 'bg-blue-700 text-white shadow'
                                    : 'bg-white border border-gray-200 text-gray-600 hover:border-blue-400'
                            ]"
                        >{{ f.label }}</button>
                    </div>
                </div>

                <!-- States -->
                <div v-if="carsLoading" class="flex items-center justify-center py-24">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-700 rounded-full animate-spin"></div>
                </div>
                <div v-else-if="carsError" class="bg-red-50 border border-red-200 rounded-xl p-6 text-center text-red-600">
                    <p class="font-semibold mb-1">Gagal memuat data mobil</p>
                    <p class="text-sm">{{ carsError }}</p>
                    <button @click="fetchCars" class="mt-3 text-sm underline">Coba lagi</button>
                </div>
                <div v-else-if="filteredCars.length === 0" class="text-center py-24 text-gray-400">
                    <div class="text-5xl mb-4">🔍</div>
                    <p class="font-semibold text-gray-600">Tidak ada mobil ditemukan</p>
                    <p class="text-sm mt-1">Coba ubah filter pencarian Anda.</p>
                </div>

                <!-- Car Grid -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="car in filteredCars"
                        :key="car.car_id"
                        class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden"
                    >
                        <div class="h-36 flex items-center justify-center text-6xl select-none" :class="carBgClass(car.car_status)">
                            {{ carIcon(car.brand) }}
                        </div>
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ car.brand }} {{ car.model }}</h3>
                                    <p class="text-xs text-gray-400">{{ car.year }} &bull; {{ car.color ?? 'N/A' }} &bull; {{ car.license_plate }}</p>
                                </div>
                                <span :class="statusBadge(car.car_status)" class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap">
                                    {{ car.car_status }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ car.car_condition ?? 'Kondisi baik' }}</p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-400">Harga per hari</p>
                                    <p class="text-lg font-bold text-blue-700">Rp {{ formatPrice(car.rental_price_per_day) }}</p>
                                </div>
                                <button
                                    :disabled="car.car_status !== 'Available'"
                                    @click="openWizard(car)"
                                    :class="[
                                        'px-4 py-2 rounded-full text-sm font-semibold transition',
                                        car.car_status === 'Available'
                                            ? 'bg-yellow-400 hover:bg-yellow-500 text-gray-900 shadow'
                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ]"
                                >{{ car.car_status === 'Available' ? 'Sewa Sekarang' : 'Tidak Tersedia' }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ── TRANSACTION HISTORY VIEW ──────────────────────── -->
            <template v-else>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Transaksi Saya</h2>
                <div v-if="histLoading" class="flex items-center justify-center py-24">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-700 rounded-full animate-spin"></div>
                </div>
                <div v-else-if="transactions.length === 0" class="text-center py-24 text-gray-400">
                    <div class="text-5xl mb-4">📄</div>
                    <p class="font-semibold text-gray-600">Belum ada transaksi</p>
                    <p class="text-sm mt-1">Sewa mobil pertama Anda sekarang!</p>
                    <button @click="view = 'cars'" class="mt-4 px-6 py-2 bg-blue-700 text-white rounded-full text-sm font-semibold">Cari Mobil</button>
                </div>
                <div v-else class="space-y-4">
                    <div
                        v-for="tx in transactions"
                        :key="tx.transaction_id"
                        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-4">
                                <div class="text-3xl select-none">{{ carIcon(tx.car?.brand) }}</div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ tx.car?.brand }} {{ tx.car?.model }}</p>
                                    <p class="text-xs text-gray-400">{{ tx.invoice_number }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ formatDate(tx.start_date) }} s/d {{ formatDate(tx.end_date) }}
                                        ({{ tx.duration_days }} hari)
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ tx.delivery_method === 'delivery' ? '🚚 Antar ke lokasi' : '🏢 Ambil sendiri' }}
                                        <span v-if="tx.delivery_address"> — {{ tx.delivery_address }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-700">Rp {{ formatPrice(tx.total_price) }}</p>
                                <span :class="txStatusBadge(tx.status)" class="text-xs font-semibold px-2.5 py-1 rounded-full inline-block mt-1">
                                    {{ txStatusLabel(tx.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- BOOKING WIZARD MODAL                                       -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div
            v-if="wizard.open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="closeWizard"
        >
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">

                <!-- Step indicator -->
                <div v-if="wizard.step < 3" class="px-6 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <!-- Step 1 -->
                        <div class="flex items-center gap-2">
                            <div :class="['w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition',
                                wizard.step >= 1 ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-400']">1</div>
                            <span :class="['text-xs font-medium', wizard.step >= 1 ? 'text-blue-700' : 'text-gray-400']">Lokasi & Tanggal</span>
                        </div>
                        <div class="flex-1 h-0.5" :class="wizard.step >= 2 ? 'bg-blue-700' : 'bg-gray-200'"></div>
                        <!-- Step 2 -->
                        <div class="flex items-center gap-2">
                            <div :class="['w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition',
                                wizard.step >= 2 ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-400']">2</div>
                            <span :class="['text-xs font-medium', wizard.step >= 2 ? 'text-blue-700' : 'text-gray-400']">Invoice & Kebijakan</span>
                        </div>
                    </div>
                </div>

                <!-- ── STEP 1: Location & Dates ───────────────────── -->
                <div v-if="wizard.step === 1" class="p-6">
                    <!-- Car summary chip -->
                    <div class="flex items-center gap-3 bg-blue-50 rounded-xl p-3 mb-5">
                        <span class="text-3xl select-none">{{ carIcon(wizard.car.brand) }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 truncate">{{ wizard.car.brand }} {{ wizard.car.model }}</p>
                            <p class="text-xs text-gray-500">{{ wizard.car.year }} &bull; {{ wizard.car.color }}</p>
                        </div>
                        <p class="text-blue-700 font-bold whitespace-nowrap">Rp {{ formatPrice(wizard.car.rental_price_per_day) }}/hari</p>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Mulai</label>
                            <input v-model="wizard.form.start_date" type="date" :min="today"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Selesai</label>
                            <input v-model="wizard.form.end_date" type="date" :min="wizard.form.start_date || today"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        </div>
                    </div>

                    <!-- Duration preview -->
                    <div v-if="bookingDays > 0" class="bg-gray-50 rounded-xl px-4 py-3 flex items-center justify-between mb-5 text-sm">
                        <span class="text-gray-500">Durasi sewa</span>
                        <span class="font-bold text-gray-800">{{ bookingDays }} hari</span>
                    </div>

                    <!-- Delivery method -->
                    <p class="text-xs font-semibold text-gray-600 mb-2">Metode Pengambilan Mobil</p>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <button
                            @click="wizard.form.delivery_method = 'pickup'"
                            :class="[
                                'border-2 rounded-xl p-4 text-left transition',
                                wizard.form.delivery_method === 'pickup'
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="text-2xl mb-1 select-none">🏢</div>
                            <p class="font-semibold text-sm text-gray-900">Ambil Sendiri</p>
                            <p class="text-xs text-gray-500 mt-0.5">Datang ke kantor kami</p>
                        </button>
                        <button
                            @click="wizard.form.delivery_method = 'delivery'"
                            :class="[
                                'border-2 rounded-xl p-4 text-left transition',
                                wizard.form.delivery_method === 'delivery'
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="text-2xl mb-1 select-none">🚚</div>
                            <p class="font-semibold text-sm text-gray-900">Antar ke Lokasi</p>
                            <p class="text-xs text-gray-500 mt-0.5">Kami antar ke alamat Anda</p>
                        </button>
                    </div>

                    <!-- Delivery address (conditional) -->
                    <div v-if="wizard.form.delivery_method === 'delivery'" class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Pengiriman</label>
                        <textarea
                            v-model="wizard.form.delivery_address"
                            rows="2"
                            placeholder="Masukkan alamat lengkap..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"
                        ></textarea>
                    </div>

                    <p v-if="wizard.error" class="text-red-500 text-sm mb-3">{{ wizard.error }}</p>

                    <div class="flex gap-3">
                        <button @click="closeWizard"
                            class="flex-1 py-2.5 rounded-full border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button @click="goToStep2"
                            class="flex-1 py-2.5 rounded-full bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold transition shadow">
                            Lanjut →
                        </button>
                    </div>
                </div>

                <!-- ── STEP 2: Invoice + Policy ───────────────────── -->
                <div v-else-if="wizard.step === 2" class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Invoice & Kebijakan</h3>
                    <p class="text-sm text-gray-500 mb-5">Tinjau rincian sewa dan baca kebijakan sebelum konfirmasi.</p>

                    <!-- Invoice table -->
                    <div class="bg-gray-50 rounded-xl overflow-hidden mb-5">
                        <div class="px-4 py-3 bg-blue-700">
                            <p class="text-white text-xs font-semibold uppercase tracking-wide">Rincian Sewa</p>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div class="flex justify-between px-4 py-2.5 text-sm">
                                <span class="text-gray-500">Mobil</span>
                                <span class="font-medium text-gray-800">{{ wizard.car.brand }} {{ wizard.car.model }}</span>
                            </div>
                            <div class="flex justify-between px-4 py-2.5 text-sm">
                                <span class="text-gray-500">Plat Nomor</span>
                                <span class="font-medium text-gray-800">{{ wizard.car.license_plate }}</span>
                            </div>
                            <div class="flex justify-between px-4 py-2.5 text-sm">
                                <span class="text-gray-500">Tanggal Sewa</span>
                                <span class="font-medium text-gray-800">{{ formatDate(wizard.form.start_date) }}</span>
                            </div>
                            <div class="flex justify-between px-4 py-2.5 text-sm">
                                <span class="text-gray-500">Tanggal Kembali</span>
                                <span class="font-medium text-gray-800">{{ formatDate(wizard.form.end_date) }}</span>
                            </div>
                            <div class="flex justify-between px-4 py-2.5 text-sm">
                                <span class="text-gray-500">Durasi</span>
                                <span class="font-medium text-gray-800">{{ bookingDays }} hari</span>
                            </div>
                            <div class="flex justify-between px-4 py-2.5 text-sm">
                                <span class="text-gray-500">Harga / hari</span>
                                <span class="font-medium text-gray-800">Rp {{ formatPrice(wizard.car.rental_price_per_day) }}</span>
                            </div>
                            <div class="flex justify-between px-4 py-2.5 text-sm">
                                <span class="text-gray-500">Metode</span>
                                <span class="font-medium text-gray-800">
                                    {{ wizard.form.delivery_method === 'delivery' ? '🚚 Antar ke Lokasi' : '🏢 Ambil Sendiri' }}
                                </span>
                            </div>
                            <div v-if="wizard.form.delivery_method === 'delivery'" class="flex justify-between px-4 py-2.5 text-sm">
                                <span class="text-gray-500">Alamat Kirim</span>
                                <span class="font-medium text-gray-800 text-right max-w-[60%]">{{ wizard.form.delivery_address }}</span>
                            </div>
                            <div class="flex justify-between px-4 py-3 bg-blue-50">
                                <span class="font-bold text-gray-900">Total Estimasi</span>
                                <span class="font-bold text-blue-700 text-base">Rp {{ formatPrice(bookingTotal) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Late Policy box -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-5">
                        <div class="flex items-start gap-3">
                            <span class="text-xl select-none mt-0.5">⚠️</span>
                            <div>
                                <p class="font-bold text-yellow-800 text-sm mb-1">Kebijakan Keterlambatan Pengembalian</p>
                                <ul class="text-xs text-yellow-700 space-y-1 list-disc list-inside">
                                    <li>Pengembalian terlambat dikenakan <strong>denda Rp {{ formatPrice(latePenaltyPerDay) }} per hari</strong> (30% dari harga sewa harian).</li>
                                    <li>Denda dihitung per hari penuh, bukan per jam.</li>
                                    <li>Keterlambatan lebih dari <strong>3 hari</strong> tanpa konfirmasi dianggap pelanggaran kontrak dan dapat dikenakan sanksi tambahan.</li>
                                    <li>Mobil wajib dikembalikan dalam kondisi yang sama seperti saat diterima.</li>
                                    <li>Kerusakan yang terjadi selama masa sewa menjadi tanggung jawab penyewa.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Agree checkbox -->
                    <label class="flex items-start gap-3 mb-5 cursor-pointer group">
                        <input v-model="wizard.agreed" type="checkbox"
                            class="mt-0.5 w-4 h-4 accent-blue-700 rounded cursor-pointer" />
                        <span class="text-sm text-gray-600 group-hover:text-gray-800">
                            Saya telah membaca dan menyetujui <strong>syarat &amp; ketentuan</strong> serta <strong>kebijakan keterlambatan</strong> di atas.
                        </span>
                    </label>

                    <p v-if="wizard.error" class="text-red-500 text-sm mb-3">{{ wizard.error }}</p>

                    <div class="flex gap-3">
                        <button @click="wizard.step = 1"
                            class="flex-1 py-2.5 rounded-full border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                            ← Kembali
                        </button>
                        <button @click="submitBooking" :disabled="wizard.submitting"
                            :class="['flex-1 py-2.5 rounded-full text-sm font-semibold transition shadow',
                                wizard.submitting ? 'bg-blue-400 text-white cursor-wait' : 'bg-blue-700 hover:bg-blue-800 text-white']">
                            {{ wizard.submitting ? 'Memproses...' : 'Konfirmasi & Pesan' }}
                        </button>
                    </div>
                </div>

                <!-- ── STEP 3: Success / Invoice Confirmed ────────── -->
                <div v-else class="p-6 text-center">
                    <div class="text-6xl mb-4 select-none">🎉</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">Pemesanan Berhasil!</h3>
                    <p class="text-gray-500 text-sm mb-5">Invoice Anda telah dibuat. Simpan nomor invoice berikut.</p>

                    <!-- Invoice card -->
                    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-5 text-left">
                        <p class="text-xs font-semibold text-blue-500 uppercase tracking-wide mb-3">Invoice</p>
                        <p class="text-lg font-bold text-blue-800 mb-4 tracking-widest">{{ wizard.result?.invoice_number }}</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Mobil</span>
                                <span class="font-medium text-gray-800">{{ wizard.car.brand }} {{ wizard.car.model }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Periode</span>
                                <span class="font-medium text-gray-800">{{ formatDate(wizard.form.start_date) }} – {{ formatDate(wizard.form.end_date) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Metode</span>
                                <span class="font-medium text-gray-800">{{ wizard.form.delivery_method === 'delivery' ? '🚚 Antar ke Lokasi' : '🏢 Ambil Sendiri' }}</span>
                            </div>
                            <div class="flex justify-between border-t border-blue-200 pt-2 mt-2">
                                <span class="font-bold text-gray-900">Total</span>
                                <span class="font-bold text-blue-700">Rp {{ formatPrice(bookingTotal) }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-yellow-600 mt-1">
                                <span>Denda keterlambatan</span>
                                <span class="font-semibold">Rp {{ formatPrice(latePenaltyPerDay) }}/hari</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mb-6">Tim kami akan menghubungi Anda untuk konfirmasi pembayaran dan pengambilan kendaraan.</p>

                    <div class="flex gap-3">
                        <button @click="goToHistory"
                            class="flex-1 py-2.5 rounded-full border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                            Lihat Transaksi
                        </button>
                        <button @click="closeWizard"
                            class="flex-1 py-2.5 rounded-full bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold transition">
                            Tutup
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>

<script>
export default {
    name: 'CustomerDashboard',
    emits: ['logout'],
    props: {
        user: { type: Object, required: true },
    },

    data() {
        return {
            view: 'cars', // 'cars' | 'history'

            // Car list
            cars: [],
            carsLoading: true,
            carsError: null,
            activeFilter: 'all',
            filters: [
                { label: 'Semua',     value: 'all' },
                { label: 'Tersedia',  value: 'Available' },
                { label: 'Disewa',    value: 'Rented' },
                { label: 'Perawatan', value: 'Maintenance' },
            ],

            // Transaction history
            transactions: [],
            histLoading: false,

            // Booking wizard state
            wizard: {
                open: false,
                step: 1,       // 1 | 2 | 3
                car: null,
                form: {
                    start_date:       '',
                    end_date:         '',
                    delivery_method:  'pickup',
                    delivery_address: '',
                },
                agreed:     false,
                error:      null,
                submitting: false,
                result:     null,   // transaction from API
            },
        };
    },

    computed: {
        filteredCars() {
            if (this.activeFilter === 'all') return this.cars;
            return this.cars.filter(c => c.car_status === this.activeFilter);
        },
        today() {
            return new Date().toISOString().split('T')[0];
        },
        bookingDays() {
            const { start_date, end_date } = this.wizard.form;
            if (!start_date || !end_date) return 0;
            const diff = Math.ceil(
                (new Date(end_date) - new Date(start_date)) / (1000 * 60 * 60 * 24)
            );
            return diff > 0 ? diff : 0;
        },
        bookingTotal() {
            if (!this.wizard.car) return 0;
            return parseFloat(this.wizard.car.rental_price_per_day) * this.bookingDays;
        },
        latePenaltyPerDay() {
            if (!this.wizard.car) return 0;
            return Math.round(parseFloat(this.wizard.car.rental_price_per_day) * 0.30);
        },
    },

    mounted() {
        this.fetchCars();
    },

    watch: {
        view(val) {
            if (val === 'history') this.fetchTransactions();
        },
    },

    methods: {
        // ── Data fetching ──────────────────────────────────────────
        async fetchCars() {
            this.carsLoading = true;
            this.carsError   = null;
            try {
                const res   = await window.axios.get('/api/cars');
                this.cars   = res.data.cars ?? [];
            } catch (err) {
                this.carsError = err.response?.data?.message ?? 'Terjadi kesalahan jaringan.';
            } finally {
                this.carsLoading = false;
            }
        },

        async fetchTransactions() {
            this.histLoading  = true;
            try {
                const res         = await window.axios.get('/api/transactions');
                this.transactions = res.data.transactions ?? [];
            } catch (_) {
                this.transactions = [];
            } finally {
                this.histLoading = false;
            }
        },

        // ── Wizard ────────────────────────────────────────────────
        openWizard(car) {
            this.wizard = {
                open: true,
                step: 1,
                car,
                form: {
                    start_date:       '',
                    end_date:         '',
                    delivery_method:  'pickup',
                    delivery_address: this.user.alamat ?? '',
                },
                agreed:     false,
                error:      null,
                submitting: false,
                result:     null,
            };
        },

        closeWizard() {
            this.wizard.open = false;
        },

        goToHistory() {
            this.closeWizard();
            this.view = 'history';
            this.fetchTransactions();
        },

        goToStep2() {
            this.wizard.error = null;
            if (!this.wizard.form.start_date || !this.wizard.form.end_date) {
                this.wizard.error = 'Harap pilih tanggal mulai dan selesai.';
                return;
            }
            if (this.bookingDays <= 0) {
                this.wizard.error = 'Tanggal selesai harus setelah tanggal mulai.';
                return;
            }
            if (this.wizard.form.delivery_method === 'delivery' && !this.wizard.form.delivery_address.trim()) {
                this.wizard.error = 'Harap masukkan alamat pengiriman.';
                return;
            }
            this.wizard.step = 2;
        },

        async submitBooking() {
            this.wizard.error = null;
            if (!this.wizard.agreed) {
                this.wizard.error = 'Harap centang persetujuan syarat & ketentuan terlebih dahulu.';
                return;
            }
            this.wizard.submitting = true;
            try {
                const payload = {
                    car_id:           this.wizard.car.car_id,
                    start_date:       this.wizard.form.start_date,
                    end_date:         this.wizard.form.end_date,
                    delivery_method:  this.wizard.form.delivery_method,
                    delivery_address: this.wizard.form.delivery_method === 'delivery'
                        ? this.wizard.form.delivery_address
                        : null,
                };
                const res            = await window.axios.post('/api/transactions', payload);
                this.wizard.result   = res.data.transaction;
                this.wizard.step     = 3;
                // Update car status in local list so card flips to Rented
                const idx = this.cars.findIndex(c => c.car_id === this.wizard.car.car_id);
                if (idx !== -1) this.cars[idx].car_status = 'Rented';
            } catch (err) {
                this.wizard.error = err.response?.data?.message ?? 'Gagal membuat transaksi. Coba lagi.';
            } finally {
                this.wizard.submitting = false;
            }
        },

        // ── Formatting helpers ─────────────────────────────────────
        formatPrice(value) {
            return Number(value).toLocaleString('id-ID');
        },
        formatDate(val) {
            if (!val) return '-';
            return new Date(val).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        },
        carIcon(brand) {
            const map = {
                Toyota: '🚙', Honda: '🚗', Mitsubishi: '🛻', Daihatsu: '🚐',
                Suzuki: '🚘', Nissan: '🚖', Mazda: '🏎️', Isuzu: '🚚',
            };
            const key = Object.keys(map).find(k => brand?.toLowerCase().includes(k.toLowerCase()));
            return key ? map[key] : '🚗';
        },
        carBgClass(status) {
            if (status === 'Available')   return 'bg-blue-50';
            if (status === 'Rented')      return 'bg-yellow-50';
            if (status === 'Maintenance') return 'bg-red-50';
            return 'bg-gray-50';
        },
        statusBadge(status) {
            if (status === 'Available')   return 'bg-green-100 text-green-700';
            if (status === 'Rented')      return 'bg-yellow-100 text-yellow-700';
            if (status === 'Maintenance') return 'bg-red-100 text-red-700';
            return 'bg-gray-100 text-gray-600';
        },
        txStatusBadge(status) {
            const map = {
                pending:   'bg-yellow-100 text-yellow-700',
                active:    'bg-blue-100 text-blue-700',
                completed: 'bg-green-100 text-green-700',
                cancelled: 'bg-red-100 text-red-700',
            };
            return map[status] ?? 'bg-gray-100 text-gray-600';
        },
        txStatusLabel(status) {
            const map = {
                pending:   'Menunggu',
                active:    'Aktif',
                completed: 'Selesai',
                cancelled: 'Dibatalkan',
            };
            return map[status] ?? status;
        },
    },
};
</script>
