<template>
    <div class="space-y-5">

        <!-- Status summary strip -->
        <div class="grid grid-cols-3 gap-4">
            <div v-for="s in statusSummary" :key="s.label"
                class="rounded-xl p-4 flex items-center gap-4 shadow-sm border" :class="s.bg">
                <div class="text-3xl">{{ s.icon }}</div>
                <div>
                    <p class="text-2xl font-bold" :class="s.text">{{ s.count }}</p>
                    <p class="text-xs font-semibold" :class="s.text">{{ s.label }}</p>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
            <div class="flex gap-2">
                <input v-model="search" type="text" placeholder="Search car..."
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-48" />
                <select v-model="filterStatus" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All</option>
                    <option>Available</option>
                    <option>Rented</option>
                    <option>Maintenance</option>
                </select>
            </div>
            <p class="text-xs text-gray-400">Click the status badge to update it inline</p>
        </div>

        <!-- Cards grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div
                v-for="car in filtered"
                :key="car.id"
                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition"
            >
                <!-- Car header -->
                <div class="h-24 flex items-center justify-center text-4xl" :style="{ background: car.color }">
                    🚗
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 text-sm">{{ car.name }}</h3>
                    <p class="text-xs text-gray-500 mb-3">{{ car.brand }} · {{ car.type }} · {{ car.plate }}</p>

                    <!-- Status selector -->
                    <div class="mb-3">
                        <label class="text-xs text-gray-400 mb-1 block">Status</label>
                        <div class="flex gap-2 flex-wrap">
                            <button
                                v-for="s in statuses"
                                :key="s.val"
                                @click="setStatus(car, s.val)"
                                :class="car.status === s.val ? s.active : s.inactive"
                                class="px-2 py-1 rounded-full text-xs font-semibold transition border"
                            >
                                {{ s.val }}
                            </button>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="text-xs text-gray-400 mb-1 block">Notes</label>
                        <input
                            v-model="car.notes"
                            type="text"
                            placeholder="e.g. Scheduled service Apr 20"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                        />
                    </div>

                    <!-- Last updated -->
                    <p class="text-[10px] text-gray-300 mt-2">Updated: {{ car.updatedAt }}</p>
                </div>
            </div>
        </div>

        <div v-if="filtered.length === 0" class="text-center py-12 text-gray-400">No cars match your filter.</div>

        <!-- Maintenance schedule -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Maintenance Schedule</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="text-xs text-gray-500 uppercase tracking-wide">
                            <th class="text-left px-5 py-3">Car</th>
                            <th class="text-left px-5 py-3">Type</th>
                            <th class="text-left px-5 py-3">Scheduled</th>
                            <th class="text-left px-5 py-3">Workshop</th>
                            <th class="text-left px-5 py-3">Est. Cost</th>
                            <th class="text-left px-5 py-3">Done</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="m in maintenanceLog" :key="m.id" class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ m.car }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ m.type }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ m.date }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ m.workshop }}</td>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ m.cost }}</td>
                            <td class="px-5 py-3">
                                <button @click="m.done = !m.done"
                                    :class="m.done ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                    class="px-2 py-0.5 rounded-full text-xs font-semibold transition">
                                    {{ m.done ? '✓ Done' : 'Pending' }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    name: 'CarStatus',
    data() {
        return {
            search: '',
            filterStatus: '',
            statuses: [
                { val: 'Available',   active: 'bg-green-500 text-white border-green-500',    inactive: 'bg-white text-green-600 border-green-300 hover:bg-green-50'   },
                { val: 'Rented',      active: 'bg-blue-500 text-white border-blue-500',      inactive: 'bg-white text-blue-600 border-blue-300 hover:bg-blue-50'     },
                { val: 'Maintenance', active: 'bg-orange-400 text-white border-orange-400',  inactive: 'bg-white text-orange-600 border-orange-300 hover:bg-orange-50'},
            ],
            cars: [
                { id: 1, name: 'Toyota Avanza',    brand: 'Toyota',     type: 'MPV',      plate: 'B 1234 ABC', status: 'Available',   notes: '', updatedAt: '2026-04-13', color: '#dbeafe' },
                { id: 2, name: 'Honda Jazz',        brand: 'Honda',      type: 'Hatchback',plate: 'D 5678 DEF', status: 'Rented',      notes: 'Customer: Siti Rahayu', updatedAt: '2026-04-08', color: '#dcfce7' },
                { id: 3, name: 'Mitsubishi Pajero', brand: 'Mitsubishi', type: 'SUV',      plate: 'B 9012 GHI', status: 'Maintenance', notes: 'Oil change + brake check', updatedAt: '2026-04-11', color: '#fef3c7' },
                { id: 4, name: 'Toyota Innova',     brand: 'Toyota',     type: 'MPV',      plate: 'F 3456 JKL', status: 'Rented',      notes: 'Customer: Dewi', updatedAt: '2026-04-10', color: '#ede9fe' },
                { id: 5, name: 'Daihatsu Xenia',    brand: 'Daihatsu',   type: 'MPV',      plate: 'B 7890 MNO', status: 'Available',   notes: '', updatedAt: '2026-04-09', color: '#fce7f3' },
                { id: 6, name: 'Honda Brio',        brand: 'Honda',      type: 'Hatchback',plate: 'D 2345 PQR', status: 'Available',   notes: '', updatedAt: '2026-04-07', color: '#dbeafe' },
                { id: 7, name: 'Toyota Fortuner',   brand: 'Toyota',     type: 'SUV',      plate: 'B 6789 STU', status: 'Maintenance', notes: 'Tire replacement', updatedAt: '2026-04-13', color: '#dcfce7' },
                { id: 8, name: 'Suzuki Ertiga',     brand: 'Suzuki',     type: 'MPV',      plate: 'E 1111 VWX', status: 'Available',   notes: '', updatedAt: '2026-04-12', color: '#fef3c7' },
            ],
            maintenanceLog: [
                { id: 1, car: 'Mitsubishi Pajero', type: 'Oil Change + Brake',  date: '2026-04-11', workshop: 'Auto Servis Jaya',  cost: 'Rp 850.000', done: false },
                { id: 2, car: 'Toyota Fortuner',   type: 'Tire Replacement',     date: '2026-04-13', workshop: 'Ban Maju Makmur',   cost: 'Rp 2.400.000', done: false },
                { id: 3, car: 'Honda Jazz',         type: 'Annual Service',       date: '2026-04-20', workshop: 'Honda Authorized',  cost: 'Rp 600.000', done: false },
                { id: 4, car: 'Daihatsu Xenia',     type: 'AC Repair',            date: '2026-04-05', workshop: 'Bengkel Dingin',    cost: 'Rp 450.000', done: true  },
            ],
        };
    },
    computed: {
        filtered() {
            const s = this.search.toLowerCase();
            return this.cars.filter(c =>
                (!s || c.name.toLowerCase().includes(s) || c.plate.toLowerCase().includes(s))
                && (!this.filterStatus || c.status === this.filterStatus)
            );
        },
        statusSummary() {
            const avail = this.cars.filter(c => c.status === 'Available').length;
            const rented = this.cars.filter(c => c.status === 'Rented').length;
            const maint  = this.cars.filter(c => c.status === 'Maintenance').length;
            return [
                { label: 'Available',   count: avail,  icon: '✅', bg: 'bg-green-50 border-green-200',  text: 'text-green-700'  },
                { label: 'Rented',      count: rented, icon: '🚗', bg: 'bg-blue-50 border-blue-200',    text: 'text-blue-700'   },
                { label: 'Maintenance', count: maint,  icon: '🔧', bg: 'bg-orange-50 border-orange-200',text: 'text-orange-700' },
            ];
        },
    },
    methods: {
        setStatus(car, status) {
            car.status = status;
            car.updatedAt = new Date().toISOString().split('T')[0];
        },
    },
};
</script>
