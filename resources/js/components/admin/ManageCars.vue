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
                    placeholder="🔍 Search car name or brand..."
                    class="flex-1 min-w-0 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                />
                <select
                    v-model="filterType"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white hover:border-gray-300 transition"
                >
                    <option value="">All Types</option>
                    <option>Sedan</option>
                    <option>SUV</option>
                    <option>MPV</option>
                    <option>Hatchback</option>
                    <option>Pickup</option>
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

        <!-- Table -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr
                            class="text-xs text-gray-600 uppercase tracking-wide font-semibold"
                        >
                            <th class="text-left px-5 py-3">Car</th>
                            <th class="text-left px-5 py-3">Brand / Type</th>
                            <th class="text-left px-5 py-3">Plate</th>
                            <th class="text-left px-5 py-3">Seats</th>
                            <th class="text-left px-5 py-3">Rate/Day</th>
                            <th class="text-left px-5 py-3">Status</th>
                            <th class="text-center px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="car in filteredCars"
                            :key="car.id"
                            class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors"
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0 font-bold"
                                        :style="{ background: car.color }"
                                    >
                                        🚗
                                    </div>
                                    <span class="font-medium text-gray-800">{{
                                        car.name
                                    }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ car.brand }} · {{ car.type }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="font-mono text-xs bg-gray-100 px-2.5 py-1 rounded border border-gray-200"
                                    >{{ car.plate }}</span
                                >
                            </td>
                            <td class="px-5 py-3 text-gray-600 font-medium">
                                {{ car.seats }}
                            </td>
                            <td class="px-5 py-3 font-semibold text-gray-900">
                                {{ car.rate }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold inline-block"
                                    :class="statusClass(car.status)"
                                >
                                    {{ car.status }}
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
                                        @click="deleteCar(car.id)"
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
                        {{ form.id ? "✏️ Edit Car" : "➕ Add New Car" }}
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
                    <div class="col-span-2">
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Car Name</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="e.g. Toyota Avanza"
                            class="input-field"
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
                            >Type</label
                        >
                        <select
                            v-model="form.type"
                            class="input-field bg-white"
                        >
                            <option>Sedan</option>
                            <option>SUV</option>
                            <option>MPV</option>
                            <option>Hatchback</option>
                            <option>Pickup</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >License Plate</label
                        >
                        <input
                            v-model="form.plate"
                            type="text"
                            placeholder="e.g. B 1234 ABC"
                            class="input-field font-mono uppercase"
                        />
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Seats</label
                        >
                        <input
                            v-model.number="form.seats"
                            type="number"
                            min="2"
                            max="12"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Transmission</label
                        >
                        <select
                            v-model="form.transmission"
                            class="input-field bg-white"
                        >
                            <option>Automatic</option>
                            <option>Manual</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Rate / Day (Rp)</label
                        >
                        <input
                            v-model="form.rateRaw"
                            type="number"
                            placeholder="350000"
                            class="input-field"
                        />
                    </div>
                    <div class="col-span-2">
                        <label
                            class="text-xs font-bold text-gray-700 uppercase mb-2 block"
                            >Status</label
                        >
                        <select
                            v-model="form.status"
                            class="input-field bg-white"
                        >
                            <option>Available</option>
                            <option>Rented</option>
                            <option>Maintenance</option>
                        </select>
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
                        {{ form.id ? "💾 Save Changes" : "✅ Add Car" }}
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
            filterType: "",
            modal: false,
            form: {},
            nextId: 10,
            cars: [
                {
                    id: 1,
                    name: "Toyota Avanza",
                    brand: "Toyota",
                    type: "MPV",
                    plate: "B 1234 ABC",
                    seats: 7,
                    transmission: "Automatic",
                    rate: "Rp 350.000",
                    rateRaw: 350000,
                    status: "Available",
                    color: "#dbeafe",
                },
                {
                    id: 2,
                    name: "Honda Jazz",
                    brand: "Honda",
                    type: "Hatchback",
                    plate: "D 5678 DEF",
                    seats: 5,
                    transmission: "Manual",
                    rate: "Rp 280.000",
                    rateRaw: 280000,
                    status: "Rented",
                    color: "#dcfce7",
                },
                {
                    id: 3,
                    name: "Mitsubishi Pajero",
                    brand: "Mitsubishi",
                    type: "SUV",
                    plate: "B 9012 GHI",
                    seats: 7,
                    transmission: "Automatic",
                    rate: "Rp 850.000",
                    rateRaw: 850000,
                    status: "Maintenance",
                    color: "#fef3c7",
                },
                {
                    id: 4,
                    name: "Toyota Innova",
                    brand: "Toyota",
                    type: "MPV",
                    plate: "F 3456 JKL",
                    seats: 7,
                    transmission: "Automatic",
                    rate: "Rp 550.000",
                    rateRaw: 550000,
                    status: "Available",
                    color: "#ede9fe",
                },
                {
                    id: 5,
                    name: "Daihatsu Xenia",
                    brand: "Daihatsu",
                    type: "MPV",
                    plate: "B 7890 MNO",
                    seats: 7,
                    transmission: "Manual",
                    rate: "Rp 300.000",
                    rateRaw: 300000,
                    status: "Rented",
                    color: "#fce7f3",
                },
                {
                    id: 6,
                    name: "Honda Brio",
                    brand: "Honda",
                    type: "Hatchback",
                    plate: "D 2345 PQR",
                    seats: 5,
                    transmission: "Automatic",
                    rate: "Rp 220.000",
                    rateRaw: 220000,
                    status: "Available",
                    color: "#dbeafe",
                },
                {
                    id: 7,
                    name: "Toyota Fortuner",
                    brand: "Toyota",
                    type: "SUV",
                    plate: "B 6789 STU",
                    seats: 7,
                    transmission: "Automatic",
                    rate: "Rp 950.000",
                    rateRaw: 950000,
                    status: "Available",
                    color: "#dcfce7",
                },
            ],
        };
    },
    computed: {
        filteredCars() {
            return this.cars.filter((c) => {
                const matchSearch =
                    !this.search ||
                    c.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    c.brand.toLowerCase().includes(this.search.toLowerCase());
                const matchType =
                    !this.filterType || c.type === this.filterType;
                return matchSearch && matchType;
            });
        },
    },
    methods: {
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
                      id: null,
                      name: "",
                      brand: "",
                      type: "MPV",
                      plate: "",
                      seats: 7,
                      transmission: "Automatic",
                      rateRaw: 0,
                      status: "Available",
                      color: "#dbeafe",
                  };
            this.modal = true;
        },
        saveCar() {
            const rate = `Rp ${Number(this.form.rateRaw).toLocaleString("id-ID")}`;
            if (this.form.id) {
                const idx = this.cars.findIndex((c) => c.id === this.form.id);
                if (idx !== -1)
                    this.cars.splice(idx, 1, { ...this.form, rate });
            } else {
                this.cars.push({ ...this.form, id: this.nextId++, rate });
            }
            this.modal = false;
        },
        deleteCar(id) {
            if (confirm("Delete this car?")) {
                this.cars = this.cars.filter((c) => c.id !== id);
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
