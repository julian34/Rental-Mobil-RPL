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
                </div>
                <div>
                    <p
                        class="text-xs text-gray-500 font-medium uppercase tracking-wide"
                    >
                        {{ stat.label }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ stat.value }}
                    </p>
                    <p
                        class="text-xs mt-2 font-medium"
                        :class="
                            stat.trend >= 0 ? 'text-green-600' : 'text-red-500'
                        "
                    >
                        {{ stat.trend >= 0 ? "📈" : "📉" }}
                        <span>{{ Math.abs(stat.trend) }}% vs last month</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent rentals -->
            <div
                class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
            >
                <div
                    class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-transparent"
                >
                    <h3
                        class="font-semibold text-gray-800 flex items-center gap-2"
                    >
                        <span>📋</span> Recent Rental Transactions
                    </h3>
                    <a
                        href="#"
                        class="text-xs text-blue-600 font-medium cursor-pointer hover:text-blue-700 hover:underline transition"
                        >View all →</a
                    >
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="text-xs text-gray-600 uppercase tracking-wide border-b border-gray-200 bg-gray-50"
                            >
                                <th class="text-left px-5 py-3 font-semibold">
                                    Customer
                                </th>
                                <th class="text-left px-5 py-3 font-semibold">
                                    Car
                                </th>
                                <th class="text-left px-5 py-3 font-semibold">
                                    Date
                                </th>
                                <th class="text-left px-5 py-3 font-semibold">
                                    Status
                                </th>
                                <th class="text-right px-5 py-3 font-semibold">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="r in recentRentals"
                                :key="r.id"
                                class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors"
                            >
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    {{ r.customer }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{ r.car }}
                                </td>
                                <td class="px-5 py-3 text-gray-500">
                                    {{ r.date }}
                                </td>
                                <td class="px-5 py-3">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs font-semibold inline-block"
                                        :class="statusClass(r.status)"
                                    >
                                        {{ r.status }}
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-3 text-right font-semibold text-gray-800"
                                >
                                    {{ r.amount }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Car status summary -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col"
            >
                <div
                    class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-transparent"
                >
                    <h3
                        class="font-semibold text-gray-800 flex items-center gap-2"
                    >
                        <span>🚗</span> Fleet Status
                    </h3>
                </div>
                <div class="p-5 space-y-5 flex-1">
                    <div v-for="s in fleetStatus" :key="s.label">
                        <div
                            class="flex justify-between items-center text-sm mb-2"
                        >
                            <span
                                class="flex items-center gap-2 text-gray-700 font-medium"
                            >
                                <span
                                    class="w-3 h-3 rounded-full"
                                    :class="s.dot"
                                ></span>
                                {{ s.label }}
                            </span>
                            <span class="font-semibold text-gray-900 text-sm"
                                >{{ s.count }} cars</span
                            >
                        </div>
                        <div
                            class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden"
                        >
                            <div
                                class="h-2.5 rounded-full transition-all duration-500 ease-out"
                                :class="s.bar"
                                :style="{
                                    width: (s.count / totalCars) * 100 + '%',
                                }"
                            ></div>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-600 font-medium">
                            Total fleet:
                            <span class="text-gray-900 font-bold"
                                >{{ totalCars }} vehicles</span
                            >
                        </p>
                    </div>
                </div>

                <!-- Pending alerts -->
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    <h4
                        class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3"
                    >
                        🔔 Alerts
                    </h4>
                    <div class="space-y-2.5">
                        <div
                            v-for="alert in alerts"
                            :key="alert.msg"
                            class="flex items-start gap-2.5 text-xs p-2.5 rounded-lg border transition-colors"
                            :class="alert.bg"
                        >
                            <span class="text-base flex-shrink-0">{{
                                alert.icon
                            }}</span>
                            <span :class="alert.text" class="leading-relaxed">{{
                                alert.msg
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "DashboardHome",
    data() {
        return {
            stats: [
                {
                    label: "Total Cars",
                    value: "42",
                    icon: "🚗",
                    bg: "bg-blue-50",
                    trend: 5,
                },
                {
                    label: "Active Rentals",
                    value: "18",
                    icon: "📋",
                    bg: "bg-green-50",
                    trend: 12,
                },
                {
                    label: "Revenue (Month)",
                    value: "Rp 48M",
                    icon: "💰",
                    bg: "bg-yellow-50",
                    trend: 8,
                },
                {
                    label: "Total Customers",
                    value: "214",
                    icon: "👤",
                    bg: "bg-purple-50",
                    trend: -2,
                },
            ],
            recentRentals: [
                {
                    id: 1,
                    customer: "Budi Santoso",
                    car: "Toyota Avanza",
                    date: "2026-04-13",
                    status: "Active",
                    amount: "Rp 1.050.000",
                },
                {
                    id: 2,
                    customer: "Siti Rahayu",
                    car: "Honda Jazz",
                    date: "2026-04-12",
                    status: "Completed",
                    amount: "Rp 560.000",
                },
                {
                    id: 3,
                    customer: "Ahmad Fauzi",
                    car: "Mitsubishi Pajero",
                    date: "2026-04-11",
                    status: "Pending",
                    amount: "Rp 2.550.000",
                },
                {
                    id: 4,
                    customer: "Dewi Kurniawati",
                    car: "Toyota Innova",
                    date: "2026-04-10",
                    status: "Active",
                    amount: "Rp 1.650.000",
                },
                {
                    id: 5,
                    customer: "Rudi Hermawan",
                    car: "Daihatsu Xenia",
                    date: "2026-04-09",
                    status: "Cancelled",
                    amount: "Rp 0",
                },
            ],
            fleetStatus: [
                {
                    label: "Available",
                    count: 18,
                    dot: "bg-green-500",
                    bar: "bg-green-500",
                },
                {
                    label: "Rented",
                    count: 18,
                    dot: "bg-blue-500",
                    bar: "bg-blue-500",
                },
                {
                    label: "Maintenance",
                    count: 6,
                    dot: "bg-orange-400",
                    bar: "bg-orange-400",
                },
            ],
            alerts: [
                {
                    icon: "⚠️",
                    msg: "2 cars due for service this week",
                    bg: "bg-orange-50",
                    text: "text-orange-700",
                },
                {
                    icon: "🔔",
                    msg: "3 pending rental approvals",
                    bg: "bg-blue-50",
                    text: "text-blue-700",
                },
                {
                    icon: "💳",
                    msg: "1 payment overdue by 2 days",
                    bg: "bg-red-50",
                    text: "text-red-700",
                },
            ],
        };
    },
    computed: {
        totalCars() {
            return this.fleetStatus.reduce((s, f) => s + f.count, 0);
        },
    },
    methods: {
        statusClass(status) {
            return (
                {
                    Active: "bg-green-100 text-green-700",
                    Completed: "bg-gray-100 text-gray-600",
                    Pending: "bg-yellow-100 text-yellow-700",
                    Cancelled: "bg-red-100 text-red-600",
                }[status] || "bg-gray-100 text-gray-600"
            );
        },
    },
};
</script>
