<template>
    <div class="space-y-5">
        <!-- Controls -->
        <div
            class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center"
        >
            <div class="flex gap-2 flex-wrap">
                <select
                    v-model="period"
                    @change="fetchData"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >
                    <option value="weekly">Minggu Ini</option>
                    <option value="monthly">Bulan Ini</option>
                    <option value="quarterly">Kuartal Ini</option>
                    <option value="yearly">Tahun Ini</option>
                </select>
            </div>
            <button
                @click="exportCSV"
                class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition"
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
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                    />
                </svg>
                Export CSV
            </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex items-center justify-center py-24">
            <div
                class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"
            ></div>
        </div>

        <template v-else>
            <!-- KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Revenue (All Time) -->
                <div
                    class="bg-white rounded-xl p-5 shadow-sm border border-gray-100"
                >
                    <p
                        class="text-xs text-gray-400 uppercase tracking-wide font-medium"
                    >
                        Total Revenue
                    </p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        Rp {{ formatCompact(kpis.all_time_revenue) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        Semua transaksi selesai
                    </p>
                </div>
                <!-- Current Period Revenue -->
                <div
                    class="bg-white rounded-xl p-5 shadow-sm border border-gray-100"
                >
                    <p
                        class="text-xs text-gray-400 uppercase tracking-wide font-medium"
                    >
                        Revenue {{ periodLabel }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        Rp {{ formatCompact(kpis.current_revenue) }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        <span
                            :class="
                                kpis.revenue_change >= 0
                                    ? 'text-green-500'
                                    : 'text-red-400'
                            "
                            class="text-xs font-semibold"
                        >
                            {{ kpis.revenue_change >= 0 ? "▲" : "▼" }}
                            {{ Math.abs(kpis.revenue_change) }}%
                        </span>
                        <span class="text-xs text-gray-400"
                            >vs period lalu</span
                        >
                    </div>
                </div>
                <!-- Total Rentals -->
                <div
                    class="bg-white rounded-xl p-5 shadow-sm border border-gray-100"
                >
                    <p
                        class="text-xs text-gray-400 uppercase tracking-wide font-medium"
                    >
                        Total Penyewaan
                    </p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ kpis.all_time_rentals }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        <span
                            :class="
                                kpis.rental_change >= 0
                                    ? 'text-green-500'
                                    : 'text-red-400'
                            "
                            class="text-xs font-semibold"
                        >
                            {{ kpis.rental_change >= 0 ? "▲" : "▼" }}
                            {{ Math.abs(kpis.rental_change) }}%
                        </span>
                        <span class="text-xs text-gray-400"
                            >vs period lalu</span
                        >
                    </div>
                </div>
                <!-- Fleet Utilization -->
                <div
                    class="bg-white rounded-xl p-5 shadow-sm border border-gray-100"
                >
                    <p
                        class="text-xs text-gray-400 uppercase tracking-wide font-medium"
                    >
                        Fleet Utilization
                    </p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ kpis.fleet_utilization }}%
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ kpis.rented_cars }}/{{ kpis.total_cars }} mobil
                        disewa
                    </p>
                </div>
            </div>

            <!-- Secondary KPI row -->
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                >
                    <p
                        class="text-xs text-gray-400 uppercase tracking-wide font-medium"
                    >
                        Rata-rata / Transaksi
                    </p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        Rp {{ formatPrice(kpis.avg_revenue_rental) }}
                    </p>
                </div>
                <div
                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                >
                    <p
                        class="text-xs text-gray-400 uppercase tracking-wide font-medium"
                    >
                        Total Denda Keterlambatan
                    </p>
                    <p class="text-xl font-bold text-red-600 mt-1">
                        Rp {{ formatPrice(kpis.total_late_charge) }}
                    </p>
                </div>
                <div
                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                >
                    <p
                        class="text-xs text-gray-400 uppercase tracking-wide font-medium"
                    >
                        Rata-rata Durasi Sewa
                    </p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        {{ kpis.avg_rental_days }} hari
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Revenue bar chart -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                >
                    <h3 class="font-semibold text-gray-800 mb-1">
                        Revenue Bulanan
                    </h3>
                    <p class="text-xs text-gray-400 mb-5">6 bulan terakhir</p>
                    <div class="space-y-3">
                        <div
                            v-for="m in monthlyRevenue"
                            :key="m.monthKey"
                            class="flex items-center gap-3"
                        >
                            <span
                                class="w-12 text-xs text-gray-500 text-right shrink-0"
                                >{{ m.monthKey }}</span
                            >
                            <div
                                class="flex-1 bg-gray-100 rounded-full h-7 relative overflow-hidden"
                            >
                                <div
                                    class="h-full rounded-full flex items-center justify-end pr-2 transition-all duration-700"
                                    :class="
                                        m.current
                                            ? 'bg-blue-600'
                                            : 'bg-blue-300'
                                    "
                                    :style="{
                                        width:
                                            (maxRevenue > 0
                                                ? (m.revenue / maxRevenue) * 100
                                                : 0) + '%',
                                        minWidth: m.revenue > 0 ? '40px' : '0',
                                    }"
                                >
                                    <span
                                        class="text-white text-xs font-semibold whitespace-nowrap"
                                        >Rp {{ formatCompact(m.revenue) }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="monthlyRevenue.length === 0"
                        class="text-center py-8 text-gray-400 text-sm"
                    >
                        Belum ada data revenue.
                    </div>
                </div>

                <!-- Rentals by brand -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                >
                    <h3 class="font-semibold text-gray-800 mb-1">
                        Penyewaan per Brand
                    </h3>
                    <p class="text-xs text-gray-400 mb-4">
                        Berdasarkan transaksi selesai
                    </p>
                    <div class="space-y-3">
                        <div v-for="(b, i) in brandChart" :key="b.brand">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 font-medium">{{
                                    b.brand
                                }}</span>
                                <span class="text-gray-500"
                                    >{{ b.count }} rental ({{ b.pct }}%) · Rp
                                    {{ formatCompact(b.revenue) }}</span
                                >
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3">
                                <div
                                    class="h-3 rounded-full"
                                    :class="brandColors[i % brandColors.length]"
                                    :style="{ width: b.pct + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="brandChart.length === 0"
                        class="text-center py-8 text-gray-400 text-sm"
                    >
                        Belum ada data penyewaan.
                    </div>
                </div>
            </div>

            <!-- Detailed report table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div
                    class="flex items-center justify-between px-5 py-4 border-b border-gray-100"
                >
                    <h3 class="font-semibold text-gray-800">
                        Laporan Detail — 6 Bulan Terakhir
                    </h3>
                    <span class="text-xs text-gray-400"
                        >{{ detailRows.length }} entries</span
                    >
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr
                                class="text-xs text-gray-500 uppercase tracking-wide"
                            >
                                <th class="text-left px-5 py-3">Periode</th>
                                <th class="text-left px-5 py-3">Transaksi</th>
                                <th class="text-left px-5 py-3">Revenue</th>
                                <th class="text-left px-5 py-3">
                                    Pelanggan Baru
                                </th>
                                <th class="text-left px-5 py-3">
                                    Rata-rata Durasi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="row in detailRows"
                                :key="row.period"
                                class="border-b border-gray-50 hover:bg-gray-50"
                                :class="row.current ? 'bg-blue-50' : ''"
                            >
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    {{ row.period }}
                                    <span
                                        v-if="row.current"
                                        class="ml-2 text-xs bg-blue-600 text-white px-1.5 py-0.5 rounded"
                                        >Sekarang</span
                                    >
                                </td>
                                <td class="px-5 py-3 text-gray-700">
                                    {{ row.transactions }}
                                </td>
                                <td
                                    class="px-5 py-3 font-semibold text-gray-800"
                                >
                                    Rp {{ formatPrice(row.revenue) }}
                                </td>
                                <td class="px-5 py-3 text-gray-700">
                                    {{ row.newCustomers }}
                                </td>
                                <td class="px-5 py-3 text-gray-700">
                                    {{ row.avgDays }} hari
                                </td>
                            </tr>
                            <tr v-if="detailRows.length === 0">
                                <td
                                    colspan="5"
                                    class="px-5 py-12 text-center text-gray-400"
                                >
                                    Belum ada data.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment method breakdown -->
            <div
                v-if="byPayment.length > 0"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
            >
                <h3 class="font-semibold text-gray-800 mb-4">
                    Revenue per Metode Pembayaran
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        v-for="pm in byPayment"
                        :key="pm.payment_method"
                        class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl"
                    >
                        <div class="text-3xl">
                            {{ pm.payment_method === "cash" ? "💵" : "🏦" }}
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 capitalize">
                                {{
                                    pm.payment_method === "cash"
                                        ? "Tunai"
                                        : "Transfer"
                                }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ pm.count }} transaksi
                            </p>
                        </div>
                        <p class="font-bold text-gray-900">
                            Rp {{ formatPrice(pm.total) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Top performing cars -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">
                        Top Performing Cars
                    </h3>
                </div>
                <div class="p-5">
                    <div
                        v-if="topCars.length === 0"
                        class="text-center py-8 text-gray-400 text-sm"
                    >
                        Belum ada data.
                    </div>
                    <div
                        v-else
                        class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4"
                    >
                        <div
                            v-for="(car, i) in topCars"
                            :key="car.car_id"
                            class="flex items-center gap-3 p-3 rounded-xl bg-gray-50"
                        >
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg"
                                :class="
                                    [
                                        'bg-yellow-100 text-yellow-700',
                                        'bg-gray-100 text-gray-600',
                                        'bg-orange-100 text-orange-700',
                                        'bg-blue-100 text-blue-600',
                                        'bg-green-100 text-green-600',
                                    ][i] || 'bg-gray-100 text-gray-600'
                                "
                            >
                                {{ i + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-semibold text-gray-900 truncate"
                                >
                                    {{ car.brand }} {{ car.model }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ car.rental_count }} rental · Rp
                                    {{ formatCompact(car.total_revenue) }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ car.license_plate }}
                                </p>
                            </div>
                            <div v-if="i < 3" class="text-lg">
                                {{ ["🥇", "🥈", "🥉"][i] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    name: "OperationalReports",
    data() {
        return {
            loading: true,
            period: "monthly",
            kpis: {
                all_time_revenue: 0,
                all_time_rentals: 0,
                current_revenue: 0,
                current_rentals: 0,
                revenue_change: 0,
                rental_change: 0,
                avg_revenue_rental: 0,
                avg_rental_days: 0,
                total_late_charge: 0,
                fleet_utilization: 0,
                total_cars: 0,
                rented_cars: 0,
            },
            monthlyRevenue: [],
            brandChart: [],
            detailRows: [],
            topCars: [],
            byPayment: [],
            brandColors: [
                "bg-blue-500",
                "bg-purple-500",
                "bg-green-500",
                "bg-yellow-400",
                "bg-red-400",
                "bg-pink-500",
                "bg-indigo-500",
                "bg-teal-500",
            ],
        };
    },
    computed: {
        maxRevenue() {
            if (this.monthlyRevenue.length === 0) return 1;
            return Math.max(...this.monthlyRevenue.map((m) => m.revenue), 1);
        },
        periodLabel() {
            return {
                weekly: "Minggu Ini",
                monthly: "Bulan Ini",
                quarterly: "Kuartal Ini",
                yearly: "Tahun Ini",
            }[this.period];
        },
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        async fetchData() {
            this.loading = true;
            try {
                const res = await window.axios.get("/api/reports/revenue", {
                    params: { period: this.period },
                });
                const d = res.data;
                Object.assign(this.kpis, d.kpis ?? {});
                this.monthlyRevenue = d.monthly_revenue ?? [];
                this.brandChart = d.brand_chart ?? [];
                this.detailRows = d.detail_rows ?? [];
                this.topCars = d.top_cars ?? [];
                this.byPayment = d.by_payment ?? [];
            } catch (e) {
                console.error("Failed to load report data", e);
            } finally {
                this.loading = false;
            }
        },
        exportCSV() {
            const token =
                localStorage.getItem("token") ||
                sessionStorage.getItem("token");
            const url = `/api/reports/export-csv?period=${this.period}`;
            // Use fetch with auth header to download
            window.axios
                .get(url, { responseType: "blob" })
                .then((res) => {
                    const blob = new Blob([res.data], { type: "text/csv" });
                    const link = document.createElement("a");
                    link.href = URL.createObjectURL(blob);
                    link.download = `revenue-report-${this.period}-${new Date().toISOString().split("T")[0]}.csv`;
                    link.click();
                    URL.revokeObjectURL(link.href);
                })
                .catch(() => alert("Gagal mengexport CSV."));
        },
        formatPrice(val) {
            return Number(val ?? 0).toLocaleString("id-ID");
        },
        formatCompact(val) {
            const n = Number(val ?? 0);
            if (n >= 1_000_000_000)
                return (
                    (n / 1_000_000_000).toFixed(1).replace(/\.0$/, "") + " M"
                );
            if (n >= 1_000_000)
                return (n / 1_000_000).toFixed(1).replace(/\.0$/, "") + " Jt";
            if (n >= 1_000)
                return (n / 1_000).toFixed(1).replace(/\.0$/, "") + " Rb";
            return n.toLocaleString("id-ID");
        },
    },
};
</script>
