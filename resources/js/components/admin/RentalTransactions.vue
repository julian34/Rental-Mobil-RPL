<template>
    <div class="space-y-5">
        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
            <div class="flex gap-2 flex-wrap">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari invoice, nama customer..."
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
                />
                <select
                    v-model="filterStatus"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="active">Aktif</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            <button
                @click="fetchTransactions"
                :disabled="loading"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition shrink-0 disabled:opacity-50"
            >
                <svg class="w-4 h-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>

        <!-- Summary pills -->
        <div class="flex flex-wrap gap-3">
            <div
                v-for="s in summaryStats"
                :key="s.label"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium"
                :class="s.cls"
            >
                <span>{{ s.icon }}</span> {{ s.label }}:
                <strong>{{ s.val }}</strong>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-16 text-gray-400">
            <svg class="w-8 h-8 mx-auto mb-3 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Memuat data transaksi...
        </div>

        <!-- Error -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-red-600 text-sm">
            {{ error }}
            <button @click="fetchTransactions" class="ml-3 underline font-medium">Coba lagi</button>
        </div>

        <!-- Table -->
        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="text-xs text-gray-500 uppercase tracking-wide">
                            <th class="text-left px-5 py-3">Invoice</th>
                            <th class="text-left px-5 py-3">Customer</th>
                            <th class="text-left px-5 py-3">Kendaraan</th>
                            <th class="text-left px-5 py-3">Periode</th>
                            <th class="text-left px-5 py-3">Metode</th>
                            <th class="text-right px-5 py-3">Total</th>
                            <th class="text-center px-5 py-3">Status</th>
                            <th class="text-center px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="tx in filtered"
                            :key="tx.transaction_id"
                            class="border-b border-gray-50 hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-5 py-3">
                                <p class="font-mono text-xs text-blue-700 font-semibold">{{ tx.invoice_number }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(tx.created_at) }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ tx.user?.name || '-' }}</p>
                                <p class="text-xs text-gray-400 font-normal">{{ tx.user?.no_telepon ?? tx.user?.username }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                <p class="font-medium text-gray-800">{{ carLabel(tx) }}</p>
                                <p class="text-xs text-gray-400">{{ tx.car?.license_plate }}</p>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">
                                <p>{{ formatDate(tx.start_date) }}</p>
                                <p class="text-gray-400">s/d {{ formatDate(tx.end_date) }}</p>
                                <p class="font-semibold text-gray-700 mt-0.5">{{ tx.duration_days }} hari</p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs">
                                    {{ tx.delivery_method === 'delivery' ? '🚚 Antar' : '🏢 Ambil' }}
                                </span>
                                <p v-if="tx.payment_method" class="text-xs text-gray-400 mt-0.5 capitalize">
                                    💳 {{ tx.payment_method }}
                                </p>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <p class="font-bold text-gray-900 whitespace-nowrap">Rp {{ formatPrice(tx.grand_total ?? tx.total_price) }}</p>
                                <p v-if="tx.late_days > 0" class="text-xs text-red-500 mt-0.5">
                                    +{{ tx.late_days }} hari telat
                                </p>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span
                                    class="text-xs rounded-full px-2.5 py-0.5 font-semibold whitespace-nowrap"
                                    :class="statusClass(tx.status)"
                                >
                                    {{ statusLabel(tx.status) }}
                                </span>
                                <p v-if="tx.status === 'active' && !tx.paid_at"
                                    class="text-xs text-red-500 font-semibold mt-1 whitespace-nowrap">
                                    Belum Dibayar
                                </p>
                                <p v-if="(tx.status === 'active' || tx.status === 'completed') && tx.paid_at"
                                    class="text-xs text-green-600 mt-1 whitespace-nowrap">
                                    ✓ Lunas
                                </p>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-center gap-1 flex-wrap">
                                    <!-- PENDING: Serahkan + optional Bayar -->
                                    <template v-if="tx.status === 'pending'">
                                        <button
                                            @click="openHandover(tx)"
                                            class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition"
                                            :title="tx.delivery_method === 'delivery' ? 'Konfirmasi Pengiriman' : 'Konfirmasi Pengambilan'"
                                        >{{ tx.delivery_method === 'delivery' ? 'Kirim' : 'Serahkan' }}</button>
                                        <button
                                            v-if="!tx.paid_at"
                                            @click="openPayment(tx)"
                                            class="px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-800 text-xs font-semibold rounded-lg transition"
                                            title="Bayar Dulu (opsional)"
                                        >Bayar</button>
                                    </template>
                                    <!-- ACTIVE: Kembali + optional Bayar -->
                                    <template v-else-if="tx.status === 'active'">
                                        <button
                                            @click="openReturn(tx)"
                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition"
                                            title="Konfirmasi Pengembalian"
                                        >Kembali</button>
                                        <button
                                            v-if="!tx.paid_at"
                                            @click="openPayment(tx)"
                                            class="px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-800 text-xs font-semibold rounded-lg transition"
                                            title="Catat Pembayaran"
                                        >Bayar</button>
                                    </template>
                                    <!-- Detail (always) -->
                                    <button
                                        @click="detail = tx"
                                        class="p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg transition"
                                        title="Lihat Detail"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <!-- Cancel (pending or active) -->
                                    <button
                                        v-if="tx.status === 'pending' || tx.status === 'active'"
                                        @click="openCancel(tx)"
                                        class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition"
                                        title="Batalkan"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filtered.length === 0">
                            <td colspan="8" class="text-center py-12 text-gray-400">
                                <div class="text-4xl mb-2">📄</div>
                                <p>Tidak ada transaksi yang ditemukan.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400">
                {{ filtered.length }} dari {{ transactions.length }} transaksi ditampilkan
            </div>
        </div>

        <!-- Detail Modal -->
        <div
            v-if="detail"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="detail = null"
        >
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                    <h3 class="font-bold text-gray-900">Detail Transaksi</h3>
                    <button @click="detail = null" class="text-gray-400 hover:text-gray-700 text-xl leading-none">✕</button>
                </div>
                <div class="p-6">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between items-start gap-4">
                            <dt class="text-gray-500 shrink-0">Invoice</dt>
                            <dd class="font-mono text-xs font-medium text-right">{{ detail.invoice_number }}</dd>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <dt class="text-gray-500 shrink-0">Status</dt>
                            <dd>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="statusClass(detail.status)">
                                    {{ statusLabel(detail.status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Informasi Customer</p>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Nama</dt>
                                    <dd class="font-medium">{{ detail.user?.name || '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Username</dt>
                                    <dd>{{ detail.user?.username || '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">No. Telepon</dt>
                                    <dd>{{ detail.user?.no_telepon || '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">No. KTP</dt>
                                    <dd>{{ detail.user?.no_ktp || '-' }}</dd>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Kendaraan</p>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Mobil</dt>
                                    <dd class="font-medium">{{ carLabel(detail) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Plat Nomor</dt>
                                    <dd>{{ detail.car?.license_plate || '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Tahun</dt>
                                    <dd>{{ detail.car?.year || '-' }}</dd>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Periode Sewa</p>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Tanggal Mulai</dt>
                                    <dd>{{ formatDate(detail.start_date) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Tanggal Akhir</dt>
                                    <dd>{{ formatDate(detail.end_date) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Durasi</dt>
                                    <dd>{{ detail.duration_days }} hari</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Metode Pengambilan</dt>
                                    <dd>{{ detail.delivery_method === 'delivery' ? '🚚 Antar ke Alamat' : '🏢 Ambil Sendiri' }}</dd>
                                </div>
                                <div v-if="detail.delivery_address" class="flex justify-between gap-4">
                                    <dt class="text-gray-500 shrink-0">Alamat Pengiriman</dt>
                                    <dd class="text-right text-xs">{{ detail.delivery_address }}</dd>
                                </div>
                                <div v-if="detail.handed_over_at" class="flex justify-between">
                                    <dt class="text-gray-500">Diserahkan pada</dt>
                                    <dd class="text-xs">{{ formatDatetime(detail.handed_over_at) }}</dd>
                                </div>
                                <div v-if="detail.actual_return_date" class="flex justify-between">
                                    <dt class="text-gray-500">Dikembalikan</dt>
                                    <dd>{{ formatDate(detail.actual_return_date) }}</dd>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Rincian Biaya</p>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Harga/Hari</dt>
                                    <dd>Rp {{ formatPrice(detail.rental_price_per_day) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Subtotal</dt>
                                    <dd>Rp {{ formatPrice(detail.total_price) }}</dd>
                                </div>
                                <div v-if="detail.late_days > 0" class="flex justify-between text-red-600">
                                    <dt>Denda Terlambat ({{ detail.late_days }} hari)</dt>
                                    <dd>+ Rp {{ formatPrice(detail.late_charge) }}</dd>
                                </div>
                                <div class="flex justify-between border-t pt-2 mt-1">
                                    <dt class="font-semibold text-gray-700">Grand Total</dt>
                                    <dd class="font-bold text-blue-700 text-base">
                                        Rp {{ formatPrice(detail.grand_total ?? detail.total_price) }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Pembayaran</p>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Metode Bayar</dt>
                                    <dd>{{ detail.payment_method === 'cash' ? 'Tunai' : detail.payment_method === 'transfer' ? 'Transfer' : '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Dibayar pada</dt>
                                    <dd>{{ detail.paid_at ? formatDatetime(detail.paid_at) : 'Belum dibayar' }}</dd>
                                </div>
                                <div v-if="detail.cashier" class="flex justify-between">
                                    <dt class="text-gray-500">Diproses oleh</dt>
                                    <dd>{{ detail.cashier?.name }}</dd>
                                </div>
                            </div>
                        </div>
                    </dl>
                    <button
                        @click="detail = null"
                        class="mt-5 w-full py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="payModal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="payModal.open = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Proses Pembayaran</h3>
                    <button @click="payModal.open = false" class="text-gray-400 hover:text-gray-700">✕</button>
                </div>
                <div class="bg-gray-50 rounded-xl overflow-hidden mb-5">
                    <div class="px-4 py-2.5 bg-green-600">
                        <p class="text-white text-xs font-semibold tracking-wide">{{ payModal.tx?.invoice_number }}</p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="flex justify-between px-4 py-2.5 text-sm">
                            <span class="text-gray-500">Pelanggan</span>
                            <span class="font-medium">{{ payModal.tx?.user?.name }}</span>
                        </div>
                        <div class="flex justify-between px-4 py-2.5 text-sm">
                            <span class="text-gray-500">Mobil</span>
                            <span class="font-medium">{{ payModal.tx?.car?.brand }} {{ payModal.tx?.car?.model }}</span>
                        </div>
                        <div class="flex justify-between px-4 py-2.5 text-sm">
                            <span class="text-gray-500">Durasi</span>
                            <span class="font-medium">{{ payModal.tx?.duration_days }} hari</span>
                        </div>
                        <div class="flex justify-between px-4 py-3 bg-green-50">
                            <span class="font-bold text-gray-900">Total Tagihan</span>
                            <span class="font-bold text-green-700 text-base">Rp {{ formatPrice(payModal.tx?.total_price) }}</span>
                        </div>
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-600 mb-2">Metode Pembayaran</p>
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <button
                        @click="payModal.method = 'cash'"
                        :class="['border-2 rounded-xl p-4 text-left transition', payModal.method === 'cash' ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:border-gray-300']"
                    >
                        <div class="text-2xl mb-1 select-none">💵</div>
                        <p class="font-semibold text-sm text-gray-900">Tunai</p>
                        <p class="text-xs text-gray-500 mt-0.5">Bayar langsung di kasir</p>
                    </button>
                    <button
                        @click="payModal.method = 'transfer'"
                        :class="['border-2 rounded-xl p-4 text-left transition', payModal.method === 'transfer' ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:border-gray-300']"
                    >
                        <div class="text-2xl mb-1 select-none">🏦</div>
                        <p class="font-semibold text-sm text-gray-900">Transfer</p>
                        <p class="text-xs text-gray-500 mt-0.5">Transfer bank / e-wallet</p>
                    </button>
                </div>
                <p v-if="payModal.error" class="text-red-500 text-sm mb-3">{{ payModal.error }}</p>
                <div class="flex gap-3">
                    <button @click="payModal.open = false"
                        class="flex-1 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button @click="submitPayment" :disabled="payModal.loading"
                        :class="['flex-1 py-2.5 rounded-lg text-sm font-semibold transition shadow',
                            payModal.loading ? 'bg-green-400 text-white cursor-wait' : 'bg-green-600 hover:bg-green-700 text-white']">
                        {{ payModal.loading ? 'Memproses...' : 'Konfirmasi Pembayaran' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Handover Modal -->
        <div v-if="handoverModal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="handoverModal.open = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ handoverModal.tx?.delivery_method === 'delivery' ? 'Konfirmasi Pengiriman Mobil' : 'Konfirmasi Pengambilan Mobil' }}
                    </h3>
                    <button @click="handoverModal.open = false" class="text-gray-400 hover:text-gray-700">✕</button>
                </div>
                <div :class="handoverModal.tx?.delivery_method === 'delivery' ? 'bg-orange-50 border-orange-200' : 'bg-blue-50 border-blue-200'"
                    class="border rounded-xl p-4 mb-5 flex items-start gap-3">
                    <span class="text-3xl select-none mt-0.5">
                        {{ handoverModal.tx?.delivery_method === 'delivery' ? '🚚' : '🏢' }}
                    </span>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">
                            {{ handoverModal.tx?.delivery_method === 'delivery' ? 'Antar ke Lokasi Pelanggan' : 'Pengambilan di Tempat' }}
                        </p>
                        <p v-if="handoverModal.tx?.delivery_method === 'delivery'" class="text-xs text-gray-600 mt-1">
                            Alamat: <strong>{{ handoverModal.tx?.delivery_address }}</strong>
                        </p>
                        <p v-else class="text-xs text-gray-600 mt-1">
                            Pelanggan mengambil langsung di kantor.
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl divide-y divide-gray-100 mb-5">
                    <div class="flex justify-between px-4 py-2.5 text-sm">
                        <span class="text-gray-500">Pelanggan</span>
                        <span class="font-medium">{{ handoverModal.tx?.user?.name }}</span>
                    </div>
                    <div class="flex justify-between px-4 py-2.5 text-sm">
                        <span class="text-gray-500">Mobil</span>
                        <span class="font-medium">{{ handoverModal.tx?.car?.brand }} {{ handoverModal.tx?.car?.model }}</span>
                    </div>
                    <div class="flex justify-between px-4 py-2.5 text-sm">
                        <span class="text-gray-500">Plat Nomor</span>
                        <span class="font-mono font-medium">{{ handoverModal.tx?.car?.license_plate }}</span>
                    </div>
                    <div class="flex justify-between px-4 py-2.5 text-sm">
                        <span class="text-gray-500">Batas Kembali</span>
                        <span class="font-medium text-red-600">{{ formatDate(handoverModal.tx?.end_date) }}</span>
                    </div>
                    <div class="flex justify-between px-4 py-2.5 text-sm">
                        <span class="text-gray-500">Pembayaran</span>
                        <span v-if="handoverModal.tx?.paid_at" class="capitalize font-medium text-green-700">
                            ✅ Sudah — {{ handoverModal.tx?.payment_method }}
                        </span>
                        <span v-else class="text-orange-600 font-medium">⏳ Bayar saat kembali</span>
                    </div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-5 text-xs text-yellow-700">
                    ⚠️ Pastikan kondisi kendaraan sudah dicek sebelum diserahkan.
                    Setelah dikonfirmasi, status mobil akan berubah menjadi <strong>Disewa</strong>.
                </div>
                <p v-if="handoverModal.error" class="text-red-500 text-sm mb-3">{{ handoverModal.error }}</p>
                <div class="flex gap-3">
                    <button @click="handoverModal.open = false"
                        class="flex-1 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button @click="submitHandover" :disabled="handoverModal.loading"
                        :class="['flex-1 py-2.5 rounded-lg text-sm font-semibold transition shadow',
                            handoverModal.loading ? 'bg-orange-400 text-white cursor-wait' : 'bg-orange-500 hover:bg-orange-600 text-white']">
                        {{ handoverModal.loading ? 'Memproses...' : (handoverModal.tx?.delivery_method === 'delivery' ? 'Konfirmasi Dikirim' : 'Konfirmasi Diserahkan') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Return Modal -->
        <div v-if="returnModal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="returnModal.open = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Konfirmasi Pengembalian Mobil</h3>
                    <button @click="returnModal.open = false" class="text-gray-400 hover:text-gray-700">✕</button>
                </div>
                <div class="flex items-center gap-3 bg-blue-50 rounded-xl p-4 mb-5">
                    <span class="text-3xl select-none">🚗</span>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900">{{ returnModal.tx?.car?.brand }} {{ returnModal.tx?.car?.model }}</p>
                        <p class="text-xs text-gray-500">{{ returnModal.tx?.invoice_number }} &bull; {{ returnModal.tx?.user?.name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Batas kembali: <strong class="text-blue-700">{{ formatDate(returnModal.tx?.end_date) }}</strong></p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Pengembalian Aktual</label>
                    <input
                        v-model="returnModal.actual_date"
                        type="date"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>
                <div v-if="returnModal.actual_date" class="mb-4">
                    <div v-if="returnLateDays === 0" class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl select-none">✅</span>
                            <div>
                                <p class="font-semibold text-green-800 text-sm">Tepat Waktu / Lebih Awal</p>
                                <p class="text-xs text-green-600 mt-0.5">Tidak ada denda keterlambatan.</p>
                            </div>
                        </div>
                        <div class="mt-3 flex justify-between text-sm border-t border-green-200 pt-3">
                            <span class="font-bold text-gray-900">Total Tagihan</span>
                            <span class="font-bold text-green-700">Rp {{ formatPrice(returnModal.tx?.total_price) }}</span>
                        </div>
                    </div>
                    <div v-else class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-start gap-2 mb-3">
                            <span class="text-xl select-none mt-0.5">⚠️</span>
                            <div>
                                <p class="font-bold text-red-800 text-sm">Keterlambatan {{ returnLateDays }} Hari</p>
                                <p class="text-xs text-red-600 mt-0.5">Denda: Rp {{ formatPrice(returnModal.tx?.late_penalty_per_day) }}/hari × {{ returnLateDays }} hari</p>
                            </div>
                        </div>
                        <div class="divide-y divide-red-100 text-sm">
                            <div class="flex justify-between py-1.5">
                                <span class="text-gray-500">Biaya Sewa</span>
                                <span>Rp {{ formatPrice(returnModal.tx?.total_price) }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 text-red-700">
                                <span>Denda Keterlambatan</span>
                                <span class="font-semibold">+ Rp {{ formatPrice(returnLateCharge) }}</span>
                            </div>
                            <div class="flex justify-between py-2 font-bold">
                                <span class="text-gray-900">Total Tagihan</span>
                                <span class="text-red-700 text-base">Rp {{ formatPrice(returnGrandTotal) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!returnModal.tx?.paid_at" class="mb-4">
                    <p class="text-xs font-semibold text-gray-600 mb-2">
                        Metode Pembayaran
                        <span class="text-red-500 ml-1">* Wajib diisi (belum dibayar)</span>
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            @click="returnModal.payment_method = 'cash'"
                            :class="['border-2 rounded-xl p-3 text-left transition',
                                returnModal.payment_method === 'cash'
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200 hover:border-gray-300']"
                        >
                            <div class="text-xl mb-0.5 select-none">💵</div>
                            <p class="font-semibold text-sm text-gray-900">Tunai</p>
                        </button>
                        <button
                            @click="returnModal.payment_method = 'transfer'"
                            :class="['border-2 rounded-xl p-3 text-left transition',
                                returnModal.payment_method === 'transfer'
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200 hover:border-gray-300']"
                        >
                            <div class="text-xl mb-0.5 select-none">🏦</div>
                            <p class="font-semibold text-sm text-gray-900">Transfer</p>
                        </button>
                    </div>
                </div>
                <div v-else class="mb-4 bg-green-50 border border-green-200 rounded-xl px-4 py-3 flex items-center gap-2">
                    <span class="text-lg select-none">✅</span>
                    <p class="text-sm text-green-700 font-medium">
                        Sudah dibayar via <span class="capitalize font-bold">{{ returnModal.tx?.payment_method }}</span>
                    </p>
                </div>
                <p v-if="returnModal.error" class="text-red-500 text-sm mb-3">{{ returnModal.error }}</p>
                <div class="flex gap-3">
                    <button @click="returnModal.open = false"
                        class="flex-1 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button @click="submitReturn" :disabled="returnModal.loading"
                        :class="['flex-1 py-2.5 rounded-lg text-sm font-semibold transition shadow',
                            returnModal.loading ? 'bg-blue-400 text-white cursor-wait' : 'bg-blue-600 hover:bg-blue-700 text-white']">
                        {{ returnModal.loading ? 'Memproses...' : (returnModal.tx?.paid_at ? 'Konfirmasi Kembali' : 'Bayar & Konfirmasi Kembali') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Cancel Confirm Modal -->
        <div v-if="cancelModal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="cancelModal.open = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                <div class="text-5xl mb-4 select-none">🗑️</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Batalkan Transaksi?</h3>
                <p class="text-sm text-gray-500 mb-1">Invoice: <strong>{{ cancelModal.tx?.invoice_number }}</strong></p>
                <p class="text-sm text-gray-500 mb-5">Pelanggan: {{ cancelModal.tx?.user?.name }}</p>
                <p class="text-xs text-red-500 mb-5">Tindakan ini akan membebaskan mobil dan tidak dapat dibatalkan.</p>
                <p v-if="cancelModal.error" class="text-red-500 text-sm mb-3">{{ cancelModal.error }}</p>
                <div class="flex gap-3">
                    <button @click="cancelModal.open = false"
                        class="flex-1 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                        Kembali
                    </button>
                    <button @click="submitCancel" :disabled="cancelModal.loading"
                        :class="['flex-1 py-2.5 rounded-lg text-sm font-semibold transition',
                            cancelModal.loading ? 'bg-red-300 text-white cursor-wait' : 'bg-red-500 hover:bg-red-600 text-white']">
                        {{ cancelModal.loading ? 'Memproses...' : 'Ya, Batalkan' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RentalTransactions',
    props: {
        user: { type: Object, required: true },
    },
    data() {
        return {
            search: '',
            filterStatus: '',
            detail: null,
            transactions: [],
            loading: false,
            error: null,
            payModal: {
                open: false, tx: null, method: 'cash', error: null, loading: false,
            },
            handoverModal: {
                open: false, tx: null, error: null, loading: false,
            },
            returnModal: {
                open: false, tx: null, actual_date: '', payment_method: 'cash', error: null, loading: false,
            },
            cancelModal: {
                open: false, tx: null, error: null, loading: false,
            },
        };
    },
    computed: {
        filtered() {
            const s = this.search.toLowerCase();
            return this.transactions.filter((tx) => {
                const matchSearch =
                    !s ||
                    (tx.invoice_number || '').toLowerCase().includes(s) ||
                    (tx.user?.name || '').toLowerCase().includes(s) ||
                    (tx.user?.username || '').toLowerCase().includes(s) ||
                    this.carLabel(tx).toLowerCase().includes(s);
                const matchStatus = !this.filterStatus || tx.status === this.filterStatus;
                return matchSearch && matchStatus;
            });
        },
        summaryStats() {
            const counts = { pending: 0, active: 0, completed: 0, cancelled: 0 };
            this.transactions.forEach((tx) => {
                if (counts[tx.status] !== undefined) counts[tx.status]++;
            });
            return [
                { label: 'Pending', val: counts.pending, icon: '⏳', cls: 'bg-yellow-50 text-yellow-700' },
                { label: 'Aktif', val: counts.active, icon: '✅', cls: 'bg-green-50 text-green-700' },
                { label: 'Selesai', val: counts.completed, icon: '🏁', cls: 'bg-gray-100 text-gray-600' },
                { label: 'Dibatalkan', val: counts.cancelled, icon: '❌', cls: 'bg-red-50 text-red-600' },
            ];
        },
        returnLateDays() {
            if (!this.returnModal.actual_date || !this.returnModal.tx) return 0;
            const actual = new Date(this.returnModal.actual_date);
            const expected = new Date(this.returnModal.tx.end_date);
            const diff = Math.ceil((actual - expected) / (1000 * 60 * 60 * 24));
            return diff > 0 ? diff : 0;
        },
        returnLateCharge() {
            if (!this.returnModal.tx) return 0;
            return this.returnLateDays * parseFloat(this.returnModal.tx.late_penalty_per_day);
        },
        returnGrandTotal() {
            if (!this.returnModal.tx) return 0;
            return parseFloat(this.returnModal.tx.total_price) + this.returnLateCharge;
        },
    },
    mounted() {
        this.fetchTransactions();
    },
    methods: {
        async fetchTransactions() {
            this.loading = true;
            this.error = null;
            try {
                const res = await window.axios.get('/api/cashier/transactions');
                this.transactions = res.data.transactions || [];
            } catch (e) {
                this.error = 'Gagal memuat data transaksi. Pastikan Anda memiliki hak akses yang diperlukan.';
            } finally {
                this.loading = false;
            }
        },
        replaceTransaction(updated) {
            const idx = this.transactions.findIndex(
                t => t.transaction_id === updated.transaction_id
            );
            if (idx !== -1) this.transactions.splice(idx, 1, updated);
        },
        carLabel(tx) {
            if (!tx.car) return '-';
            return `${tx.car.brand} ${tx.car.model}`;
        },
        formatPrice(val) {
            if (val == null) return '0';
            return Number(val).toLocaleString('id-ID');
        },
        formatDate(val) {
            if (!val) return '-';
            return new Date(val).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
            });
        },
        formatDatetime(val) {
            if (!val) return '-';
            return new Date(val).toLocaleString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit',
            });
        },
        statusLabel(s) {
            return { pending: 'Pending', active: 'Aktif', completed: 'Selesai', cancelled: 'Dibatalkan' }[s] || s;
        },
        statusClass(s) {
            return {
                pending: 'bg-yellow-100 text-yellow-700',
                active: 'bg-green-100 text-green-700',
                completed: 'bg-gray-100 text-gray-600',
                cancelled: 'bg-red-100 text-red-600',
            }[s] || '';
        },

        // Payment
        openPayment(tx) {
            this.payModal = { open: true, tx, method: 'cash', error: null, loading: false };
        },
        async submitPayment() {
            this.payModal.error = null;
            this.payModal.loading = true;
            try {
                const res = await window.axios.patch(
                    `/api/cashier/transactions/${this.payModal.tx.transaction_id}/payment`,
                    { payment_method: this.payModal.method }
                );
                this.replaceTransaction(res.data.transaction);
                this.payModal.open = false;
            } catch (err) {
                this.payModal.error = err.response?.data?.message ?? 'Gagal memproses pembayaran.';
            } finally {
                this.payModal.loading = false;
            }
        },

        // Handover
        openHandover(tx) {
            this.handoverModal = { open: true, tx, error: null, loading: false };
        },
        async submitHandover() {
            this.handoverModal.error = null;
            this.handoverModal.loading = true;
            try {
                const res = await window.axios.patch(
                    `/api/cashier/transactions/${this.handoverModal.tx.transaction_id}/handover`
                );
                this.replaceTransaction(res.data.transaction);
                this.handoverModal.open = false;
            } catch (err) {
                this.handoverModal.error = err.response?.data?.message ?? 'Gagal mengkonfirmasi serah terima.';
            } finally {
                this.handoverModal.loading = false;
            }
        },

        // Return
        openReturn(tx) {
            this.returnModal = {
                open: true, tx,
                actual_date: new Date().toISOString().split('T')[0],
                payment_method: 'cash',
                error: null, loading: false,
            };
        },
        async submitReturn() {
            this.returnModal.error = null;
            this.returnModal.loading = true;
            try {
                const payload = { actual_return_date: this.returnModal.actual_date };
                if (!this.returnModal.tx.paid_at) {
                    payload.payment_method = this.returnModal.payment_method;
                }
                const res = await window.axios.patch(
                    `/api/cashier/transactions/${this.returnModal.tx.transaction_id}/return`,
                    payload
                );
                this.replaceTransaction(res.data.transaction);
                this.returnModal.open = false;
            } catch (err) {
                this.returnModal.error = err.response?.data?.message ?? 'Gagal mengkonfirmasi pengembalian.';
            } finally {
                this.returnModal.loading = false;
            }
        },

        // Cancel
        openCancel(tx) {
            this.cancelModal = { open: true, tx, error: null, loading: false };
        },
        async submitCancel() {
            this.cancelModal.error = null;
            this.cancelModal.loading = true;
            try {
                const res = await window.axios.patch(
                    `/api/cashier/transactions/${this.cancelModal.tx.transaction_id}/cancel`
                );
                this.replaceTransaction(res.data.transaction);
                this.cancelModal.open = false;
            } catch (err) {
                this.cancelModal.error = err.response?.data?.message ?? 'Gagal membatalkan transaksi.';
            } finally {
                this.cancelModal.loading = false;
            }
        },
    },
};
</script>
