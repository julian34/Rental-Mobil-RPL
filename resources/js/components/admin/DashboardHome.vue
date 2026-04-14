<template>
    <div class="space-y-6">
        <!-- Stat cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
                v-for="stat in stats"
                :key="stat.label"
                class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md border border-gray-100 hover:border-gray-200 transition-all duration-200 cursor-default"
            >
                <div class="flex items-start justify-between mb-3">
                    <div
                        class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
                        :class="stat.bg"
                    >
                        {{ stat.icon }}
                    </div>
                    <span v-if="loading" class="text-xs text-gray-300 animate-pulse">Memuat...</span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">
                        {{ stat.label }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ stat.value }}
                    </p>
                    <p v-if="stat.sub" class="text-xs mt-1" :class="stat.subCls || 'text-gray-400'">{{ stat.sub }}</p>
                </div>
            </div>
        </div>

        <!-- Second row of stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
                v-for="stat in stats2"
                :key="stat.label"
                class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0"
                        :class="stat.bg"
                    >
                        {{ stat.icon }}
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">
                            {{ stat.label }}
                        </p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ stat.value }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent rentals -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-transparent">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <span>📋</span> Transaksi Sewa Terbaru
                    </h3>
                </div>

                <!-- Loading skeleton -->
                <div v-if="loading" class="p-5 space-y-3">
                    <div v-for="i in 5" :key="i" class="h-8 bg-gray-100 rounded-lg animate-pulse"></div>
                </div>

                <div v-else-if="recentRentals.length === 0" class="text-center py-10 text-gray-400 text-sm">
                    Belum ada transaksi.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-600 uppercase tracking-wide border-b border-gray-200 bg-gray-50">
                                <th class="text-left px-5 py-3 font-semibold">Customer</th>
                                <th class="text-left px-5 py-3 font-semibold">Kendaraan</th>
                                <th class="text-left px-5 py-3 font-semibold">Periode</th>
                                <th class="text-center px-5 py-3 font-semibold">Pembayaran</th>
                                <th class="text-left px-5 py-3 font-semibold">Status</th>
                                <th class="text-right px-5 py-3 font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="r in recentRentals"
                                :key="r.id"
                                class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors"
                            >
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-800">{{ r.customer }}</p>
                                    <p class="text-xs text-gray-400 font-normal">{{ r.invoice }}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="text-gray-700">{{ r.car }}</p>
                                    <p class="text-xs text-gray-400">{{ r.plate }}</p>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500">
                                    <p>{{ r.startDate }}</p>
                                    <p class="text-gray-400">s/d {{ r.endDate }}</p>
                                    <p class="font-semibold text-gray-700">{{ r.days }} hari</p>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span v-if="r.paidAt" class="text-xs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full capitalize">
                                        ✓ {{ r.paymentMethod }}
                                    </span>
                                    <span v-else class="text-xs text-orange-500 font-medium">Belum bayar</span>
                                </td>
                                <td class="px-5 py-3">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs font-semibold inline-block"
                                        :class="statusClass(r.status)"
                                    >
                                        {{ statusLabel(r.status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-800 whitespace-nowrap">
                                    {{ r.amount }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fleet status + Alerts -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-transparent">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <span>🚗</span> Status Armada
                    </h3>
                </div>
                <div class="p-5 space-y-5 flex-1">
                    <!-- Loading skeleton -->
                    <div v-if="loading" class="space-y-4">
                        <div v-for="i in 3" :key="i" class="space-y-1">
                            <div class="h-4 bg-gray-100 rounded animate-pulse w-2/3"></div>
                            <div class="h-2 bg-gray-100 rounded animate-pulse"></div>
                        </div>
                    </div>
                    <template v-else>
                        <div v-for="s in fleetStatus" :key="s.label">
                            <div class="flex justify-between items-center text-sm mb-2">
                                <span class="flex items-center gap-2 text-gray-700 font-medium">
                                    <span class="w-3 h-3 rounded-full" :class="s.dot"></span>
                                    {{ s.label }}
                                </span>
                                <span class="font-semibold text-gray-900 text-sm">{{ s.count }} kendaraan</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div
                                    class="h-2.5 rounded-full transition-all duration-500 ease-out"
                                    :class="s.bar"
                                    :style="{ width: totalCars > 0 ? (s.count / totalCars) * 100 + '%' : '0%' }"
                                ></div>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-xs text-gray-600 font-medium">
                                Total armada:
                                <span class="text-gray-900 font-bold">{{ totalCars }} kendaraan</span>
                            </p>
                        </div>
                    </template>
                </div>

                <!-- Alerts -->
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">
                        🔔 Peringatan
                    </h4>
                    <div class="space-y-2.5">
                        <div v-if="loading">
                            <div class="h-8 bg-gray-100 rounded-lg animate-pulse"></div>
                        </div>
                        <div
                            v-else
                            v-for="alert in alerts"
                            :key="alert.msg"
                            class="flex items-start gap-2.5 text-xs p-2.5 rounded-lg border transition-colors"
                            :class="alert.bg"
                        >
                            <span class="text-base flex-shrink-0">{{ alert.icon }}</span>
                            <span :class="alert.text" class="leading-relaxed">{{ alert.msg }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DashboardHome',
    props: {
        user: { type: Object, required: true },
    },
    data() {
        return {
            loading: false,
            // Stat cards — row 1
            stats: [
                { label: 'Total Kendaraan', value: '-', icon: '🚗', bg: 'bg-blue-50' },
                { label: 'Sewa Aktif', value: '-', icon: '📋', bg: 'bg-green-50' },
                { label: 'Pendapatan Hari Ini', value: '-', icon: '💰', bg: 'bg-yellow-50' },
                { label: 'Selesai Hari Ini', value: '-', icon: '🏁', bg: 'bg-purple-50' },
            ],
            // Stat cards — row 2
            stats2: [
                { label: 'Pending', value: '-', icon: '⏳', bg: 'bg-yellow-50' },
                { label: 'Belum Dibayar', value: '-', icon: '💳', bg: 'bg-red-50' },
                { label: 'Total Transaksi', value: '-', icon: '📊', bg: 'bg-indigo-50' },
                { label: 'Total Pendapatan', value: '-', icon: '💎', bg: 'bg-emerald-50' },
            ],
            recentRentals: [],
            fleetStatus: [
                { label: 'Tersedia', count: 0, dot: 'bg-green-500', bar: 'bg-green-500' },
                { label: 'Disewa', count: 0, dot: 'bg-blue-500', bar: 'bg-blue-500' },
                { label: 'Maintenance', count: 0, dot: 'bg-orange-400', bar: 'bg-orange-400' },
            ],
            // Raw stats from API for alerts
            pendingCount: 0,
            unpaidCount: 0,
        };
    },
    computed: {
        totalCars() {
            return this.fleetStatus.reduce((s, f) => s + f.count, 0);
        },
        alerts() {
            const list = [];
            if (this.pendingCount > 0) {
                list.push({
                    icon: '🔔',
                    msg: `${this.pendingCount} transaksi pending menunggu serah terima kendaraan`,
                    bg: 'bg-blue-50 border-blue-200',
                    text: 'text-blue-700',
                });
            }
            if (this.unpaidCount > 0) {
                list.push({
                    icon: '💳',
                    msg: `${this.unpaidCount} transaksi aktif belum melakukan pembayaran`,
                    bg: 'bg-red-50 border-red-200',
                    text: 'text-red-700',
                });
            }
            if (list.length === 0) {
                list.push({
                    icon: '✅',
                    msg: 'Semua transaksi berjalan normal. Tidak ada peringatan.',
                    bg: 'bg-green-50 border-green-200',
                    text: 'text-green-700',
                });
            }
            return list;
        },
    },
    mounted() {
        this.fetchDashboardData();
    },
    methods: {
        async fetchDashboardData() {
            this.loading = true;
            try {
                const [statsRes, txRes, carsRes] = await Promise.all([
                    window.axios.get('/api/cashier/stats'),
                    window.axios.get('/api/cashier/transactions'),
                    window.axios.get('/api/cars'),
                ]);

                const s = statsRes.data;
                this.pendingCount = s.pending ?? 0;
                this.unpaidCount  = s.unpaid_active ?? 0;

                const cars = carsRes.data.cars || [];
                const totalCarsCount = cars.length;
                const txs = txRes.data.transactions || [];

                // Calculate totals from transaction data
                const completedTxs = txs.filter(tx => tx.status === 'completed');
                const totalRevenue = completedTxs.reduce((sum, tx) => sum + parseFloat(tx.grand_total || tx.total_price || 0), 0);

                // Update stat cards — row 1
                this.stats = [
                    { label: 'Total Kendaraan', value: String(totalCarsCount), icon: '🚗', bg: 'bg-blue-50',
                      sub: `${cars.filter(c => c.car_status === 'Available').length} tersedia`, subCls: 'text-green-500' },
                    { label: 'Sewa Aktif', value: String(s.active ?? 0), icon: '📋', bg: 'bg-green-50',
                      sub: 'mobil sedang disewa', subCls: 'text-blue-500' },
                    { label: 'Pendapatan Hari Ini', value: 'Rp ' + this.formatRevenue(s.revenue_today ?? 0), icon: '💰', bg: 'bg-yellow-50',
                      sub: `${s.completed_today ?? 0} transaksi selesai`, subCls: 'text-yellow-600' },
                    { label: 'Selesai Hari Ini', value: String(s.completed_today ?? 0), icon: '🏁', bg: 'bg-purple-50' },
                ];

                // Update stat cards — row 2
                this.stats2 = [
                    { label: 'Pending', value: String(s.pending ?? 0), icon: '⏳', bg: 'bg-yellow-50' },
                    { label: 'Belum Dibayar', value: String(s.unpaid_active ?? 0), icon: '💳', bg: 'bg-red-50' },
                    { label: 'Total Transaksi', value: String(txs.length), icon: '📊', bg: 'bg-indigo-50' },
                    { label: 'Total Pendapatan', value: 'Rp ' + this.formatRevenue(totalRevenue), icon: '💎', bg: 'bg-emerald-50' },
                ];

                // Recent rentals — 7 terbaru with full info
                this.recentRentals = txs.slice(0, 7).map((tx) => ({
                    id: tx.transaction_id,
                    invoice: tx.invoice_number,
                    customer: tx.user?.name || '-',
                    car: tx.car ? `${tx.car.brand} ${tx.car.model}` : '-',
                    plate: tx.car?.license_plate || '-',
                    startDate: this.formatDate(tx.start_date),
                    endDate: this.formatDate(tx.end_date),
                    days: tx.duration_days,
                    status: tx.status,
                    paidAt: tx.paid_at,
                    paymentMethod: tx.payment_method,
                    amount: 'Rp ' + this.formatPrice(tx.grand_total ?? tx.total_price),
                }));

                // Fleet status dari cars
                const groups = { Available: 0, Rented: 0, Maintenance: 0 };
                cars.forEach((c) => {
                    if (groups[c.car_status] !== undefined) groups[c.car_status]++;
                });
                this.fleetStatus = [
                    { label: 'Tersedia', count: groups.Available, dot: 'bg-green-500', bar: 'bg-green-500' },
                    { label: 'Disewa', count: groups.Rented, dot: 'bg-blue-500', bar: 'bg-blue-500' },
                    { label: 'Maintenance', count: groups.Maintenance, dot: 'bg-orange-400', bar: 'bg-orange-400' },
                ];
            } catch (e) {
                console.error('Gagal memuat data dashboard:', e);
            } finally {
                this.loading = false;
            }
        },
        formatPrice(val) {
            if (!val) return '0';
            return Number(val).toLocaleString('id-ID');
        },
        formatRevenue(val) {
            const n = Number(val) || 0;
            if (n >= 1_000_000_000) return (n / 1_000_000_000).toFixed(1).replace('.0', '') + 'M';
            if (n >= 1_000_000)     return (n / 1_000_000).toFixed(1).replace('.0', '') + 'jt';
            if (n >= 1_000)         return (n / 1_000).toFixed(0) + 'rb';
            return String(n);
        },
        formatDate(val) {
            if (!val) return '-';
            return new Date(val).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
            });
        },
        statusClass(status) {
            return {
                active:    'bg-green-100 text-green-700',
                completed: 'bg-gray-100 text-gray-600',
                pending:   'bg-yellow-100 text-yellow-700',
                cancelled: 'bg-red-100 text-red-600',
            }[status] || 'bg-gray-100 text-gray-600';
        },
        statusLabel(status) {
            return {
                active:    'Aktif',
                completed: 'Selesai',
                pending:   'Pending',
                cancelled: 'Dibatalkan',
            }[status] || status;
        },
    },
};
</script>
