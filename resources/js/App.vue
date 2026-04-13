<template>
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-indigo-100">
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-indigo-600">
                            Rental Mobil
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button
                            class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
                        >
                            Login
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Welcome to Rental Mobil
                </h2>
                <p class="text-xl text-gray-600">
                    Find and rent the perfect vehicle for your journey
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div
                    v-for="car in cars"
                    :key="car.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition"
                >
                    <div
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 h-40 flex items-center justify-center"
                    >
                        <span class="text-4xl">🚗</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            {{ car.name }}
                        </h3>
                        <p class="text-gray-600 mb-4">{{ car.description }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-indigo-600"
                                >${{ car.price }}/day</span
                            >
                            <button
                                @click="rentCar(car.id)"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                            >
                                Rent Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="selectedCar"
                class="bg-white rounded-lg shadow-lg p-6 mb-12"
            >
                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                    Selected: {{ selectedCar.name }}
                </h3>
                <p class="text-gray-600">
                    You can proceed with the rental booking.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "App",
    data() {
        return {
            selectedCar: null,
            cars: [
                {
                    id: 1,
                    name: "Honda Civic",
                    description: "Reliable and fuel-efficient sedan",
                    price: 50,
                },
                {
                    id: 2,
                    name: "Toyota Camry",
                    description: "Comfortable mid-size sedan",
                    price: 60,
                },
                {
                    id: 3,
                    name: "BMW X5",
                    description: "Luxury SUV with premium features",
                    price: 150,
                },
            ],
        };
    },
    methods: {
        rentCar(carId) {
            this.selectedCar = this.cars.find((car) => car.id === carId);
            alert(`You selected: ${this.selectedCar.name}`);
        },
    },
};
</script>

<style scoped>
/* Component styles here */
</style>
