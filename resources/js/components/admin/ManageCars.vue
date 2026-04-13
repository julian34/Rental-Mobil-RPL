<template>
    <div class="space-y-5">
        <!-- Toolbar -->
        <div
            class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center bg-white rounded-xl p-4 shadow-sm border border-gray-100"
        >
            <div class="flex gap-2 flex-wrap w-full sm:w-auto">
                <input
                    v-model="search"
                    type="text"
                    placeholder="🔍 Search car model or brand..."
                    class="flex-1 min-w-0 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                />
                <select
                    v-model="filterStatus"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white hover:border-gray-300 transition"
                >
                    <option value="">All Status</option>
                    <option>Available</option>
                    <option>Rented</option>
                    <option>Maintenance</option>
                </select>
            </div>
            <button
                @click="openModal()"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md flex-shrink-0"
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
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Add Car
            </button>
        </div>

        <!-- Loading state -->
        <div v-if="fetching" class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center text-gray-400">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-8 h-8 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <p class="text-sm font-medium">Loading cars...</p>
            </div>
        </div>

        <!-- Error state -->
        <div v-else-if="fetchError" class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
            <p class="text-red-600 font-medium text-sm">{{ fetchError }}</p>
            <button @click="fetchCars" class="mt-3 px-4 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Retry</button>
        </div>

        <!-- Table -->
        <div
            v-else
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr
                            class="text-xs text-gray-600 uppercase tracking-wide font-semibold"
                        >
                            <th class="text-left px-5 py-3">Car</th>
                            <th class="text-left px-5 py-3">Code</th>
                            <th class="text-left px-5 py-3">Plate</th>
                            <th class="text-left px-5 py-3">Year</th>
                            <th class="text-left px-5 py-3">Rate/Day</th>
                            <th class="text-left px-5 py-3">Status</th>
                            <th class="text-center px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="car in filteredCars"
                            :key="car.car_id"
                            class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors"
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0 font-bold bg-blue-50"
                                    >
                                        🚗
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-800">{{ car.brand }} {{ car.model }}</span>
                                        <p v-if="car.color" class="text-xs text-gray-400">{{ car.color }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="font-mono text-xs bg-blue-50 px-2.5 py-1 rounded border border-blue-200 text-blue-700"
                                    >{{ car.car_code }}</span
                                >
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="font-mono text-xs bg-gray-100 px-2.5 py-1 rounded border border-gray-200"
                                    >{{ car.license_plate }}</span
                                >
                            </td>
                            <td class="px-5 py-3 text-gray-600 font-medium">
                                {{ car.year || '-' }}
                            </td>
                            <td class="px-5 py-3 font-semibold text-gray-900">
                                Rp {{ Number(car.rental_price_per_day).toLocaleString("id-ID") }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold inline-block"
                                    :class="statusClass(car.car_status)"
                                >
                                    {{ car.car_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div
                                    class="flex items-center justify-center gap-1"
                                >
                                    <button
                                        @click="openModal(car)"
                                        class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                        title="Edit car"
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
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="deleteCar(car.car_id)"
                                        class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-colors"
                                        title="Delete car"
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
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredCars.length === 0">
                            <td
                                colspan="7"
                                class="text-center py-10 text-gray-400"
                            >
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-3xl">🚗</span>
                                    <p class="font-medium">No cars found.</p>
                                    <p class="text-xs">
                                        Try adjusting your filters or add a new
                                        car.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400"
            >
                Showing {{ filteredCars.length }} of {{ cars.length }} cars
            </div>
        </div>

        <!-- Modal -->
        <div
            v-if="modal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 animate-fade-in"
            @click.self="modal = false"
        >
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            >
                <div
                    class="sticky top-0 px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-50 to-transparent"
                >
                    <h3 class="font-bold text-lg text-gray-900">
                        {{ form.car_id ? "✏️ Edit Car" : "➕ Add New Car" }}
                    </h3>
                    <button
                        @click="modal = false"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-lg transition-colors"
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Car Code</label
                        >
                        <input
                            v-model="form.car_code"
                            type="text"
                            placeholder="e.g. CAR-001"
                            class="input-field font-mono uppercase"
                        />
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >License Plate</label
                        >
                        <input
                            v-model="form.license_plate"
                            type="text"
                            placeholder="e.g. B 1234 ABC"
                            class="input-field font-mono uppercase"
                        />
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Brand</label
                        >
                        <input
                            v-model="form.brand"
                            type="text"
                            placeholder="e.g. Toyota"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Model</label
                        >
                        <input
                            v-model="form.model"
                            type="text"
                            placeholder="e.g. Avanza"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Year</label
                        >
                        <input
                            v-model.number="form.year"
                            type="number"
                            min="1900"
                            max="2099"
                            placeholder="e.g. 2024"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Color</label
                        >
                        <input
                            v-model="form.color"
                            type="text"
                            placeholder="e.g. White"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Rate / Day (Rp)</label
                        >
                        <input
                            v-model="form.rental_price_per_day"
                            type="number"
                            placeholder="350000"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Status</label
                        >
                        <select
                            v-model="form.car_status"
                            class="input-field bg-white"
                        >
                            <option>Available</option>
                            <option>Rented</option>
                            <option>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Condition Notes</label
                        >
                        <textarea
                            v-model="form.car_condition"
                            rows="2"
                            placeholder="e.g. Good condition, minor scratch on rear bumper"
                            class="input-field"
                        ></textarea>
                    </div>
                </div>
                <div
                    class="px-6 pb-5 flex justify-end gap-3 border-t border-gray-100 pt-4"
                >
                    <button
                        @click="modal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="saveCar"
                        class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md"
                    >
                        {{ form.car_id ? "💾 Save Changes" : "✅ Add Car" }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ManageCars",
    data() {
        return {
            search: "",
            filterStatus: "",
            modal: false,
            loading: false,
            fetching: false,
            fetchError: null,
            form: {},
            cars: [],
        };
    },
    computed: {
        filteredCars() {
            return this.cars.filter((c) => {
                const matchSearch =
                    !this.search ||
                    c.model.toLowerCase().includes(this.search.toLowerCase()) ||
                    c.brand.toLowerCase().includes(this.search.toLowerCase()) ||
                    c.car_code.toLowerCase().includes(this.search.toLowerCase());
                const matchStatus =
                    !this.filterStatus || c.car_status === this.filterStatus;
                return matchSearch && matchStatus;
            });
        },
    },
    mounted() {
        this.fetchCars();
    },
    methods: {
        async fetchCars() {
            this.fetching = true;
            this.fetchError = null;
            try {
                const res = await window.axios.get("/api/cars");
                this.cars = res.data.cars;
            } catch (e) {
                console.error("Failed to fetch cars", e);
                this.fetchError = e.response?.data?.message || "Failed to load cars. Please try again.";
            } finally {
                this.fetching = false;
            }
        },
        statusClass(status) {
            return (
                {
                    Available: "bg-green-100 text-green-700",
                    Rented: "bg-blue-100 text-blue-700",
                    Maintenance: "bg-orange-100 text-orange-700",
                }[status] || "bg-gray-100 text-gray-600"
            );
        },
        openModal(car = null) {
            this.form = car
                ? { ...car }
                : {
                      car_id: null,
                      car_code: "",
                      license_plate: "",
                      brand: "",
                      model: "",
                      year: null,
                      color: "",
                      rental_price_per_day: 0,
                      car_status: "Available",
                      car_condition: "",
                  };
            this.modal = true;
        },
        async saveCar() {
            this.loading = true;
            try {
                const payload = {
                    car_code: this.form.car_code,
                    license_plate: this.form.license_plate,
                    brand: this.form.brand,
                    model: this.form.model,
                    year: this.form.year || null,
                    color: this.form.color || null,
                    rental_price_per_day: Number(this.form.rental_price_per_day),
                    car_status: this.form.car_status,
                    car_condition: this.form.car_condition || null,
                };

                if (this.form.car_id) {
                    await window.axios.put(`/api/cars/${this.form.car_id}`, payload);
                } else {
                    await window.axios.post("/api/cars", payload);
                }
                this.modal = false;
                await this.fetchCars();
            } catch (e) {
                const msg =
                    e.response?.data?.message ||
                    Object.values(e.response?.data?.errors || {}).flat().join("\n") ||
                    "Failed to save car";
                alert(msg);
            } finally {
                this.loading = false;
            }
        },
        async deleteCar(id) {
            if (!confirm("Delete this car?")) return;
            try {
                await window.axios.delete(`/api/cars/${id}`);
                await this.fetchCars();
            } catch (e) {
                alert("Failed to delete car");
            }
        },
    },
};
</script>

<style scoped>
.input-field {
    width: 100%;
    border: 1px solid rgb(229, 231, 235);
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}
.input-field:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgb(59, 130, 246);
}
</style>
