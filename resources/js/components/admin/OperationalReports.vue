<template>
    <div class="space-y-5">

        <!-- Controls -->
        <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
            <div class="flex gap-2 flex-wrap">
                <select v-model="period" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="weekly">This Week</option>
                    <option value="monthly">This Month</option>
                    <option value="quarterly">This Quarter</option>
                    <option value="yearly">This Year</option>
                </select>
                <select v-model="reportType" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="revenue">Revenue Report</option>
                    <option value="rentals">Rental Report</option>
                    <option value="fleet">Fleet Utilization</option>
                </select>
            </div>
            <button @click="exportReport"
                class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </button>
        </div>

        <!-- KPI cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div v-for="kpi in kpis" :key="kpi.label" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">{{ kpi.label }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ kpi.value }}</p>
                <div class="flex items-center gap-1 mt-1">
                    <span :class="kpi.change >= 0 ? 'text-green-500' : 'text-red-400'" class="text-xs font-semibold">
                        {{ kpi.change >= 0 ? '▲' : '▼' }} {{ Math.abs(kpi.change) }}%
                    </span>
                    <span class="text-xs text-gray-400">vs prev period</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <!-- Revenue bar chart (CSS) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-1">Monthly Revenue</h3>
                <p class="text-xs text-gray-400 mb-5">Last 6 months (in million Rp)</p>
                <div class="space-y-3">
                    <div v-for="m in revenueChart" :key="m.month" class="flex items-center gap-3">
                        <span class="w-8 text-xs text-gray-500 text-right shrink-0">{{ m.month }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-6 relative overflow-hidden">
                            <div
                                class="h-full rounded-full flex items-center justify-end pr-2 transition-all duration-700"
                                :class="m.current ? 'bg-blue-600' : 'bg-blue-300'"
                                :style="{ width: (m.value / maxRevenue * 100) + '%' }"
                            >
                                <span class="text-white text-xs font-semibold">{{ m.value }}M</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rental by car type (donut-like) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-1">Rentals by Car Type</h3>
                <p class="text-xs text-gray-400 mb-4">{{ period === 'monthly' ? 'This month' : 'Selected period' }}</p>
                <div class="space-y-3">
                    <div v-for="t in carTypeChart" :key="t.type">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 font-medium">{{ t.type }}</span>
                            <span class="text-gray-500">{{ t.count }} rentals ({{ t.pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3">
                            <div class="h-3 rounded-full" :class="t.color" :style="{ width: t.pct + '%' }"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Detailed report table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Detailed Report — {{ periodLabel }}</h3>
                <span class="text-xs text-gray-400">{{ reportRows.length }} entries</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="text-xs text-gray-500 uppercase tracking-wide">
                            <th class="text-left px-5 py-3">Period</th>
                            <th class="text-left px-5 py-3">Transactions</th>
                            <th class="text-left px-5 py-3">Revenue</th>
                            <th class="text-left px-5 py-3">New Customers</th>
                            <th class="text-left px-5 py-3">Avg Rental Days</th>
                            <th class="text-left px-5 py-3">Fleet Util.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in reportRows" :key="row.period" class="border-b border-gray-50 hover:bg-gray-50" :class="row.current ? 'bg-blue-50' : ''">
                            <td class="px-5 py-3 font-medium text-gray-800">
                                {{ row.period }}
                                <span v-if="row.current" class="ml-2 text-xs bg-blue-600 text-white px-1.5 py-0.5 rounded">Current</span>
                            </td>
                            <td class="px-5 py-3 text-gray-700">{{ row.transactions }}</td>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ row.revenue }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ row.newCustomers }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ row.avgDays }} days</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-100 rounded-full h-2">
                                        <div class="h-2 bg-blue-500 rounded-full" :style="{ width: row.utilization + '%' }"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ row.utilization }}%</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top performing cars -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Top Performing Cars</h3>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div v-for="(car, i) in topCars" :key="car.name" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg"
                        :class="['bg-yellow-100 text-yellow-700', 'bg-gray-100 text-gray-600', 'bg-orange-100 text-orange-700'][i]">
                        {{ i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ car.name }}</p>
                        <p class="text-xs text-gray-500">{{ car.rentals }} rentals · {{ car.revenue }}</p>
                    </div>
                    <div class="text-lg">{{ ['🥇','🥈','🥉'][i] }}</div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    name: 'OperationalReports',
    data() {
        return {
            period: 'monthly',
            reportType: 'revenue',
            kpis: [
                { label: 'Total Revenue',    value: 'Rp 48.1M', change: 8   },
                { label: 'Total Rentals',    value: '127',       change: 12  },
                { label: 'Avg Revenue/Car',  value: 'Rp 1.14M', change: 3   },
                { label: 'Customer Sat.',    value: '4.7 / 5',   change: 2   },
            ],
            revenueChart: [
                { month: 'Nov', value: 32, current: false },
                { month: 'Dec', value: 41, current: false },
                { month: 'Jan', value: 38, current: false },
                { month: 'Feb', value: 45, current: false },
                { month: 'Mar', value: 44, current: false },
                { month: 'Apr', value: 48, current: true  },
            ],
            carTypeChart: [
                { type: 'MPV',      count: 58, pct: 46, color: 'bg-blue-500'   },
                { type: 'SUV',      count: 31, pct: 24, color: 'bg-purple-500' },
                { type: 'Sedan',    count: 22, pct: 17, color: 'bg-green-500'  },
                { type: 'Hatchback',count: 16, pct: 13, color: 'bg-yellow-400' },
            ],
            reportRows: [
                { period: 'April 2026',    transactions: 42, revenue: 'Rp 48.1M', newCustomers: 18, avgDays: 2.8, utilization: 78, current: true  },
                { period: 'March 2026',    transactions: 39, revenue: 'Rp 44.3M', newCustomers: 15, avgDays: 3.1, utilization: 72, current: false },
                { period: 'February 2026', transactions: 35, revenue: 'Rp 45.2M', newCustomers: 12, avgDays: 3.4, utilization: 68, current: false },
                { period: 'January 2026',  transactions: 31, revenue: 'Rp 38.0M', newCustomers: 10, avgDays: 2.9, utilization: 61, current: false },
                { period: 'December 2025', transactions: 44, revenue: 'Rp 41.5M', newCustomers: 21, avgDays: 3.0, utilization: 82, current: false },
            ],
            topCars: [
                { name: 'Toyota Avanza',    rentals: 28, revenue: 'Rp 9.8M'  },
                { name: 'Toyota Fortuner',  rentals: 19, revenue: 'Rp 18.1M' },
                { name: 'Toyota Innova',    rentals: 17, revenue: 'Rp 9.4M'  },
            ],
        };
    },
    computed: {
        maxRevenue() {
            return Math.max(...this.revenueChart.map(m => m.value));
        },
        periodLabel() {
            return { weekly: 'This Week', monthly: 'Last 5 Months', quarterly: 'This Quarter', yearly: 'This Year' }[this.period];
        },
    },
    methods: {
        exportReport() {
            alert('Exporting report as CSV... (connect to real API)');
        },
    },
};
</script>
