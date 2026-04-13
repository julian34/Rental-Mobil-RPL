<template>
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-indigo-100">

        <!-- Navbar -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-indigo-600">Rental Mobil</h1>
                    </div>
                    <div class="flex items-center space-x-3">

                        <!-- Logged in -->
                        <template v-if="user">
                            <div class="flex items-center gap-3">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-semibold text-gray-800">{{ user.name }}</p>
                                    <p class="text-xs text-indigo-500">{{ primaryRole }}</p>
                                </div>
                                <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <button
                                    @click="handleLogout"
                                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 text-sm hover:bg-gray-50 transition"
                                >
                                    Keluar
                                </button>
                            </div>
                        </template>

                        <!-- Guest -->
                        <template v-else>
                            <button
                                @click="activeModal = 'register'"
                                class="px-4 py-2 rounded-lg border border-green-600 text-green-600 text-sm font-medium hover:bg-green-50 transition"
                            >
                                Daftar
                            </button>
                            <button
                                @click="activeModal = 'login'"
                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition"
                            >
                                Masuk
                            </button>
                        </template>

                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Welcome to Rental Mobil</h2>
                <p class="text-xl text-gray-600">Temukan dan sewa kendaraan terbaik untuk perjalanan Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div
                    v-for="car in cars"
                    :key="car.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition"
                >
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-40 flex items-center justify-center">
                        <span class="text-5xl">🚗</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ car.name }}</h3>
                        <p class="text-gray-600 mb-4">{{ car.description }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-indigo-600">Rp{{ car.price }}/hari</span>
                            <button
                                @click="rentCar(car.id)"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm"
                            >
                                Sewa Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="selectedCar" class="bg-white rounded-lg shadow-lg p-6 mb-12">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Dipilih: {{ selectedCar.name }}</h3>
                <p class="text-gray-600">Anda dapat melanjutkan pemesanan rental.</p>
            </div>
        </div>

        <!-- Modals -->
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

    </div>
</template>

<script>
import LoginModal from './components/LoginModal.vue';
import RegisterModal from './components/RegisterModal.vue';

export default {
    name: 'App',
    components: { LoginModal, RegisterModal },
    data() {
        return {
            activeModal: null, // null | 'login' | 'register'
            user: null,
            token: null,
            selectedCar: null,
            cars: [
                { id: 1, name: 'Honda Civic',   description: 'Sedan andal dan irit bahan bakar',   price: '500.000' },
                { id: 2, name: 'Toyota Camry',  description: 'Sedan mid-size yang nyaman',          price: '600.000' },
                { id: 3, name: 'BMW X5',        description: 'SUV mewah dengan fitur premium',      price: '1.500.000' },
            ],
        };
    },
    computed: {
        primaryRole() {
            return this.user?.roles?.[0]?.name ?? '';
        },
    },
    methods: {
        onLoginSuccess({ user, token }) {
            this.user = user;
            this.token = token;
            this.activeModal = null;
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },
        async handleLogout() {
            try {
                await window.axios.post('/api/auth/logout');
            } catch (_) {
                // ignore
            }
            this.user = null;
            this.token = null;
            delete window.axios.defaults.headers.common['Authorization'];
        },
        rentCar(carId) {
            if (!this.user) {
                this.activeModal = 'login';
                return;
            }
            this.selectedCar = this.cars.find((car) => car.id === carId);
            alert(`Anda memilih: ${this.selectedCar.name}`);
        },
    },
};
</script>

<style scoped>
/* Component styles here */
</style>
