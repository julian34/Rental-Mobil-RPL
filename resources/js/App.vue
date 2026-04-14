<template>
    <!-- Admin view -->
    <AdminDashboard v-if="isAdmin" :user="user" @logout="handleLogout" />

    <!-- Cashier view -->
    <CashierDashboard
        v-else-if="isCashier"
        :user="user"
        @logout="handleLogout"
    />

    <!-- Staff view -->
    <StaffDashboard v-else-if="isStaff" :user="user" @logout="handleLogout" />

    <!-- Customer view — shown immediately after login for Customer role -->
    <CustomerDashboard
        v-else-if="isCustomer"
        :user="user"
        @logout="handleLogout"
    />

    <!-- Public website view -->
    <div v-else class="min-h-screen flex flex-col bg-white font-sans">
        <!-- Navbar -->
        <Navbar
            :user="user"
            @open-login="activeModal = 'login'"
            @open-register="activeModal = 'register'"
            @logout="handleLogout"
        />

        <!-- Hero + Search -->
        <HeroSearch @search="handleSearch" />

        <!-- Partner logos strip -->
        <div class="bg-white border-b border-gray-100 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p
                    class="text-center text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4"
                >
                    Trusted partners worldwide
                </p>
                <div
                    class="flex flex-wrap justify-center items-center gap-6 sm:gap-10"
                >
                    <span
                        v-for="brand in suppliers"
                        :key="brand"
                        class="text-sm font-bold text-gray-400 hover:text-gray-700 transition cursor-default uppercase tracking-wide"
                        >{{ brand }}</span
                    >
                </div>
            </div>
        </div>

        <!-- Why Us -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Why Choose Us?
                    </h2>
                    <p class="text-gray-500 mt-2">
                        Everything you need for a perfect rental experience
                    </p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                    <div
                        v-for="feat in features"
                        :key="feat.title"
                        class="text-center group"
                    >
                        <div
                            class="w-16 h-16 rounded-2xl mx-auto mb-5 flex items-center justify-center text-2xl shadow-md transition-transform group-hover:-translate-y-1"
                            :class="feat.bgClass"
                        >
                            {{ feat.icon }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            {{ feat.title }}
                        </h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            {{ feat.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Destinations -->
        <PopularDestinations />

        <!-- How it works -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        How It Works
                    </h2>
                    <p class="text-gray-500 mt-2">
                        Rent a car in 5 simple steps
                    </p>
                </div>
                <div class="relative">
                    <!-- Connector line (desktop) -->
                    <div
                        class="hidden sm:block absolute top-8 left-[10%] right-[10%] h-0.5 bg-blue-100 z-0"
                    ></div>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-5 gap-6 relative z-10"
                    >
                        <div
                            v-for="(step, i) in steps"
                            :key="step.label"
                            class="flex flex-col items-center text-center"
                        >
                            <div
                                class="w-16 h-16 rounded-full bg-blue-700 flex items-center justify-center text-white text-xl font-bold shadow-lg mb-3"
                            >
                                {{ step.icon }}
                            </div>
                            <p
                                class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1"
                            >
                                Step {{ i + 1 }}
                            </p>
                            <p class="text-sm font-bold text-gray-900">
                                {{ step.label }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- We care for you -->
        <section class="py-16 bg-blue-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white">
                        We Care For You
                    </h2>
                    <p class="text-blue-200 mt-2">
                        Your satisfaction is our top priority
                    </p>
                </div>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                >
                    <div
                        v-for="care in carePillars"
                        :key="care.title"
                        class="bg-white/10 rounded-2xl p-6 text-center hover:bg-white/20 transition"
                    >
                        <div class="text-3xl mb-4">{{ care.icon }}</div>
                        <h3 class="text-white font-bold mb-2">
                            {{ care.title }}
                        </h3>
                        <p class="text-blue-200 text-sm">
                            {{ care.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Banner -->
        <section class="py-14 bg-gray-50 border-t border-gray-100">
            <div class="max-w-2xl mx-auto px-4 text-center">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">
                    Ready to hit the road?
                </h2>
                <p class="text-gray-500 mb-6">
                    Search from 600+ suppliers and find the best deal for your
                    journey.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button
                        @click="scrollToTop"
                        class="px-8 py-3 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-full transition shadow-lg"
                    >
                        Search Cars Now
                    </button>
                    <template v-if="!user">
                        <button
                            @click="activeModal = 'register'"
                            class="px-8 py-3 border-2 border-blue-700 text-blue-700 font-semibold rounded-full hover:bg-blue-50 transition"
                        >
                            Create Free Account
                        </button>
                    </template>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <SiteFooter />

        <!-- Search Results Modal (simple) -->
        <div
            v-if="searchResults"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="searchResults = null"
        >
            <div
                class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-xl font-bold text-gray-900">
                        Available Cars
                    </h3>
                    <button
                        @click="searchResults = null"
                        class="text-gray-400 hover:text-gray-700"
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-500 mb-5">
                    Showing results for
                    <strong>{{
                        searchResults.pickupLocation || "your location"
                    }}</strong>
                </p>
                <div class="space-y-4">
                    <div
                        v-for="car in availableCars"
                        :key="car.id"
                        class="flex items-center gap-4 border border-gray-200 rounded-xl p-4 hover:border-blue-300 transition group"
                    >
                        <div
                            class="w-16 h-12 rounded-lg flex items-center justify-center text-2xl"
                            :style="{ background: car.gradient }"
                        >
                            {{ car.icon }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 text-sm">
                                {{ car.name }}
                            </h4>
                            <p class="text-xs text-gray-500">
                                {{ car.type }} &bull; {{ car.seats }} seats
                                &bull; {{ car.transmission }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-700">
                                Rp{{ car.price }}/day
                            </p>
                            <button
                                @click="selectCar(car)"
                                class="text-xs px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-full transition"
                            >
                                Select
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals — outside v-else so they survive the isAdmin transition -->
    <LoginModal
        v-if="activeModal === 'login'"
        @close="activeModal = null"
        @login-success="onLoginSuccess"
        @switch-to-register="activeModal = 'register'"
    />

    <RegisterModal
        v-if="activeModal === 'register'"
        @close="activeModal = null"
        @register-success="onLoginSuccess"
        @switch-to-login="activeModal = 'login'"
    />
</template>

<script>
import AdminDashboard from "./components/AdminDashboard.vue";
import CashierDashboard from "./components/CashierDashboard.vue";
import StaffDashboard from "./components/StaffDashboard.vue";
import CustomerDashboard from "./components/CustomerDashboard.vue";
import Navbar from "./components/Navbar.vue";
import HeroSearch from "./components/HeroSearch.vue";
import PopularDestinations from "./components/PopularDestinations.vue";
import SiteFooter from "./components/SiteFooter.vue";
import LoginModal from "./components/LoginModal.vue";
import RegisterModal from "./components/RegisterModal.vue";

export default {
    name: "App",
    components: {
        AdminDashboard,
        CashierDashboard,
        StaffDashboard,
        CustomerDashboard,
        Navbar,
        HeroSearch,
        PopularDestinations,
        SiteFooter,
        LoginModal,
        RegisterModal,
    },
    data() {
        return {
            activeModal: null,
            user: null,
            token: null,
            searchResults: null,

            suppliers: [
                "Sixt",
                "Europcar",
                "Hertz",
                "Avis",
                "Budget",
                "Enterprise",
                "Alamo",
                "National",
                "Thrifty",
                "Dollar",
            ],

            features: [
                {
                    icon: "🚗",
                    title: "600+ Suppliers",
                    description:
                        "Access the widest network of car rental companies worldwide for the best selection.",
                    bgClass: "bg-blue-50",
                },
                {
                    icon: "💰",
                    title: "Guaranteed Best Price",
                    description:
                        "We compare prices across all suppliers so you always get the lowest rate available.",
                    bgClass: "bg-yellow-50",
                },
                {
                    icon: "⭐",
                    title: "15+ Years Experience",
                    description:
                        "Trusted by over 8.5 million clients with a track record of excellence since 2009.",
                    bgClass: "bg-green-50",
                },
            ],

            steps: [
                { icon: "📍", label: "Choose Location" },
                { icon: "📅", label: "Pick Dates" },
                { icon: "🚗", label: "Select Car" },
                { icon: "💳", label: "Pay Securely" },
                { icon: "🔑", label: "Pick Up Keys" },
            ],

            carePillars: [
                {
                    icon: "🌍",
                    title: "20,000+ Locations",
                    description:
                        "Pick up and drop off at thousands of locations worldwide.",
                },
                {
                    icon: "🗣️",
                    title: "Multi-language",
                    description: "Our team supports you in over 20 languages.",
                },
                {
                    icon: "📞",
                    title: "24/7 Support",
                    description:
                        "Round-the-clock assistance for any question or emergency.",
                },
                {
                    icon: "🔒",
                    title: "Secure Payments",
                    description:
                        "All transactions protected with bank-level encryption.",
                },
            ],

            availableCars: [
                {
                    id: 1,
                    name: "Toyota Avanza",
                    type: "MPV",
                    seats: 7,
                    transmission: "Automatic",
                    price: "350.000",
                    icon: "🚐",
                    gradient: "linear-gradient(135deg,#bfdbfe,#93c5fd)",
                },
                {
                    id: 2,
                    name: "Honda Jazz",
                    type: "Hatchback",
                    seats: 5,
                    transmission: "Manual",
                    price: "280.000",
                    icon: "🚗",
                    gradient: "linear-gradient(135deg,#bbf7d0,#86efac)",
                },
                {
                    id: 3,
                    name: "Mitsubishi Pajero",
                    type: "SUV",
                    seats: 7,
                    transmission: "Automatic",
                    price: "850.000",
                    icon: "🛻",
                    gradient: "linear-gradient(135deg,#fde68a,#fcd34d)",
                },
                {
                    id: 4,
                    name: "Toyota Innova",
                    type: "MPV",
                    seats: 7,
                    transmission: "Automatic",
                    price: "550.000",
                    icon: "🚙",
                    gradient: "linear-gradient(135deg,#e0e7ff,#c7d2fe)",
                },
            ],
        };
    },
    computed: {
        isAdmin() {
            return (
                this.user?.roles?.some((r) => r.name === "Administrator") ??
                false
            );
        },
        isCashier() {
            return this.user?.roles?.some((r) => r.name === "Cashier") ?? false;
        },
        isStaff() {
            return this.user?.roles?.some((r) => r.name === "Staff") ?? false;
        },
        isCustomer() {
            return (
                this.user?.roles?.some((r) => r.name === "Customer") ?? false
            );
        },
    },
    mounted() {
        // Restore session from localStorage so admin stays logged in after refresh
        const token = localStorage.getItem("auth_token");
        if (token) {
            window.axios.defaults.headers.common["Authorization"] =
                `Bearer ${token}`;
            window.axios
                .get("/api/auth/me")
                .then((res) => {
                    this.user = res.data.user;
                    this.token = token;
                })
                .catch(() => {
                    // Token expired or invalid — clear storage
                    localStorage.removeItem("auth_token");
                    delete window.axios.defaults.headers.common[
                        "Authorization"
                    ];
                });
        }
    },
    methods: {
        onLoginSuccess({ user, token }) {
            this.user = user;
            this.token = token;
            this.activeModal = null;
            localStorage.setItem("auth_token", token);
            window.axios.defaults.headers.common["Authorization"] =
                `Bearer ${token}`;
        },
        async handleLogout() {
            try {
                await window.axios.post("/api/auth/logout");
            } catch (_) {
                // ignore
            }
            this.user = null;
            this.token = null;
            localStorage.removeItem("auth_token");
            delete window.axios.defaults.headers.common["Authorization"];
        },
        handleSearch(form) {
            if (!form.pickupLocation.trim()) {
                alert("Please enter a pick-up location.");
                return;
            }
            this.searchResults = form;
        },
        selectCar(car) {
            if (!this.user) {
                this.searchResults = null;
                this.activeModal = "login";
                return;
            }
            alert(`Booking confirmed for: ${car.name} at Rp${car.price}/day`);
            this.searchResults = null;
        },
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: "smooth" });
        },
    },
};
</script>
