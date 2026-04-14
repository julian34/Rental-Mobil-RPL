<template>
    <div class="flex h-screen bg-gray-100 overflow-hidden">
        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-[#1a2332] to-[#0f1419] text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-xl lg:shadow-none"
        >
            <!-- Logo -->
            <div
                class="flex items-center gap-2 px-6 py-5 border-b border-white/10 bg-gradient-to-r from-blue-600 to-blue-700"
            >
                <div
                    class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center backdrop-blur-sm"
                >
                    <svg
                        class="w-5 h-5 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2.5 1M13 16H3m10 0l2.5 1M13 6h6l2 4.5V16h-2.5"
                        />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold leading-tight text-white">
                        Admin Panel
                    </p>
                    <p class="text-xs text-blue-100">Rental Management</p>
                </div>
            </div>

            <!-- User info -->
            <div class="px-6 py-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center font-bold text-sm"
                    >
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white truncate">
                            {{ user.name }}
                        </p>
                        <p class="text-xs text-blue-300">Administrator</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <button
                    v-for="item in menuItems"
                    :key="item.key"
                    @click="
                        activePage = item.key;
                        sidebarOpen = false;
                    "
                    :class="
                        activePage === item.key
                            ? 'bg-blue-600 text-white shadow-md'
                            : 'text-gray-300 hover:bg-white/10 hover:text-white'
                    "
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group"
                    :title="item.label"
                >
                    <span class="text-lg leading-none flex-shrink-0">{{
                        item.icon
                    }}</span>
                    <div class="text-left flex-1 min-w-0">
                        <p class="truncate font-medium">{{ item.label }}</p>
                        <p
                            v-if="item.sub"
                            class="text-[10px] opacity-60 truncate"
                        >
                            {{ item.sub }}
                        </p>
                    </div>
                    <span
                        v-if="item.badge"
                        class="ml-auto text-xs bg-red-500 text-white rounded-full px-1.5 py-0.5 leading-none font-semibold flex-shrink-0"
                        >{{ item.badge }}</span
                    >
                </button>
            </nav>

            <!-- Logout -->
            <div class="px-3 py-4 border-t border-white/10">
                <button
                    @click="$emit('logout')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition"
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
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>
                    Sign Out
                </button>
            </div>
        </aside>

        <!-- Sidebar overlay (mobile) -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header
                class="bg-white shadow-sm z-10 flex items-center justify-between px-4 sm:px-6 h-14 shrink-0 border-b border-gray-200"
            >
                <div class="flex items-center gap-3 flex-1">
                    <!-- Hamburger (mobile) -->
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden text-gray-500 hover:text-gray-700 p-1.5 hover:bg-gray-100 rounded-lg transition"
                        aria-label="Toggle sidebar"
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
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <h1
                            class="text-base font-semibold text-gray-800 truncate"
                        >
                            {{ currentMenu.label }}
                        </h1>
                        <p
                            class="text-xs text-gray-500 hidden sm:block truncate"
                        >
                            {{ currentMenu.sub || "Manage your rental system" }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 ml-auto">
                    <span
                        class="text-xs text-gray-500 hidden sm:inline-block whitespace-nowrap"
                        >{{ today }}</span
                    >
                    <div
                        class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold cursor-pointer hover:bg-blue-700 transition"
                        title="User profile"
                    >
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    <component :is="currentComponent" :user="user" />
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import DashboardHome from "./admin/DashboardHome.vue";
import ManageCars from "./admin/ManageCars.vue";
import RentalTransactions from "./admin/RentalTransactions.vue";
import PaymentData from "./admin/PaymentData.vue";
import CarStatus from "./admin/CarStatus.vue";
import OperationalReports from "./admin/OperationalReports.vue";
import UserAccounts from "./admin/UserAccounts.vue";

export default {
    name: "AdminDashboard",
    components: {
        DashboardHome,
        ManageCars,
        RentalTransactions,
        PaymentData,
        CarStatus,
        OperationalReports,
        UserAccounts,
    },
    props: {
        user: { type: Object, required: true },
    },
    emits: ["logout"],
    data() {
        return {
            activePage: "home",
            sidebarOpen: false,
            menuItems: [
                {
                    key: "home",
                    label: "Dashboard",
                    sub: "Overview & stats",
                    icon: "📊",
                },
                {
                    key: "cars",
                    label: "Manage Cars",
                    sub: "Fleet management",
                    icon: "🚗",
                },
                {
                    key: "rentals",
                    label: "Rental Transactions",
                    sub: "Booking records",
                    icon: "📋",
                    badge: null,
                },
                {
                    key: "payments",
                    label: "Payment Data",
                    sub: "Finance management",
                    icon: "💳",
                },
                {
                    key: "car-status",
                    label: "Car Status",
                    sub: "Available / Rented / Maint",
                    icon: "🔄",
                },
                {
                    key: "reports",
                    label: "Operational Reports",
                    sub: "Analytics & exports",
                    icon: "📈",
                },
                {
                    key: "users",
                    label: "User Accounts",
                    sub: "Customer · Owner · Staff",
                    icon: "👥",
                },
            ],
        };
    },
    computed: {
        currentMenu() {
            return (
                this.menuItems.find((m) => m.key === this.activePage) ||
                this.menuItems[0]
            );
        },
        currentComponent() {
            const map = {
                home: "DashboardHome",
                cars: "ManageCars",
                rentals: "RentalTransactions",
                payments: "PaymentData",
                "car-status": "CarStatus",
                reports: "OperationalReports",
                users: "UserAccounts",
            };
            return map[this.activePage] || "DashboardHome";
        },
        today() {
            return new Date().toLocaleDateString("en-US", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            });
        },
    },
    methods: {
        closeSidebar() {
            this.sidebarOpen = false;
        },
        async fetchPendingBadge() {
            try {
                const res = await window.axios.get("/api/cashier/stats");
                const pending = res.data.pending ?? 0;
                const item = this.menuItems.find((m) => m.key === "rentals");
                if (item) item.badge = pending > 0 ? pending : null;
            } catch (_) {}
        },
    },
    mounted() {
        this._handleEscape = (e) => {
            if (e.key === "Escape") this.closeSidebar();
        };
        window.addEventListener("keydown", this._handleEscape);
        this.fetchPendingBadge();
    },
    beforeUnmount() {
        window.removeEventListener("keydown", this._handleEscape);
    },
};
</script>
