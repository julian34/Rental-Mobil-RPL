<template>
    <div class="space-y-5">
        <!-- Toolbar -->
        <div
            class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center"
        >
            <div class="flex gap-2 flex-wrap">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search customer or car..."
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-56"
                />
                <select
                    v-model="filterStatus"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >
                    <option value="">All Status</option>
                    <option>Pending</option>
                    <option>Active</option>
                    <option>Completed</option>
                    <option>Cancelled</option>
                </select>
            </div>
            <button
                @click="openModal()"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shrink-0"
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
                Record Transaction
            </button>
        </div>

        <!-- Summary pills -->
        <div class="flex flex-wrap gap-3">
            <div
                v-for="s in summaryStats"
                :key="s.label"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium"
                :class="s.cls"
            >
                <span>{{ s.icon }}</span> {{ s.label }}:
                <strong>{{ s.val }}</strong>
            </div>
        </div>

        <!-- Table -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr
                            class="text-xs text-gray-500 uppercase tracking-wide"
                        >
                            <th class="text-left px-5 py-3">ID</th>
                            <th class="text-left px-5 py-3">Customer</th>
                            <th class="text-left px-5 py-3">Car</th>
                            <th class="text-left px-5 py-3">Pick-up</th>
                            <th class="text-left px-5 py-3">Return</th>
                            <th class="text-left px-5 py-3">Days</th>
                            <th class="text-left px-5 py-3">Total</th>
                            <th class="text-left px-5 py-3">Status</th>
                            <th class="text-center px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="r in filtered"
                            :key="r.id"
                            class="border-b border-gray-50 hover:bg-gray-50"
                        >
                            <td
                                class="px-5 py-3 font-mono text-xs text-gray-500"
                            >
                                #{{ String(r.id).padStart(4, "0") }}
                            </td>
                            <td class="px-5 py-3 font-medium text-gray-800">
                                {{ r.customer }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ r.car }}</td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ r.pickup }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ r.returnDate }}
                            </td>
                            <td class="px-5 py-3 text-gray-600 text-center">
                                {{ r.days }}
                            </td>
                            <td class="px-5 py-3 font-semibold text-gray-800">
                                {{ r.total }}
                            </td>
                            <td class="px-5 py-3">
                                <select
                                    v-model="r.status"
                                    @change="updateStatus(r)"
                                    class="text-xs rounded-full px-2 py-0.5 font-semibold border-0 focus:ring-1 focus:ring-blue-400 cursor-pointer"
                                    :class="statusClass(r.status)"
                                >
                                    <option>Pending</option>
                                    <option>Active</option>
                                    <option>Completed</option>
                                    <option>Cancelled</option>
                                </select>
                            </td>
                            <td class="px-5 py-3">
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <button
                                        @click="openModal(r)"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Edit"
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
                                        @click="viewDetail(r)"
                                        class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg transition"
                                        title="Detail"
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
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filtered.length === 0">
                            <td
                                colspan="9"
                                class="text-center py-10 text-gray-400"
                            >
                                No transactions found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400"
            >
                {{ filtered.length }} transaction(s) shown
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div
            v-if="modal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="modal = false"
        >
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div
                    class="px-6 py-4 border-b border-gray-100 flex items-center justify-between"
                >
                    <h3 class="font-bold text-gray-900">
                        {{
                            form.id ? "Edit Transaction" : "Record Transaction"
                        }}
                    </h3>
                    <button
                        @click="modal = false"
                        class="text-gray-400 hover:text-gray-700"
                    >
                        ✕
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="label">Customer Name</label>
                        <input
                            v-model="form.customer"
                            type="text"
                            class="input-field"
                            placeholder="Customer name"
                        />
                    </div>
                    <div>
                        <label class="label">Car</label>
                        <select v-model="form.car" class="input-field bg-white">
                            <option>Toyota Avanza</option>
                            <option>Honda Jazz</option>
                            <option>Mitsubishi Pajero</option>
                            <option>Toyota Innova</option>
                            <option>Daihatsu Xenia</option>
                            <option>Honda Brio</option>
                            <option>Toyota Fortuner</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Pick-up Date</label>
                            <input
                                v-model="form.pickup"
                                type="date"
                                class="input-field"
                            />
                        </div>
                        <div>
                            <label class="label">Return Date</label>
                            <input
                                v-model="form.returnDate"
                                type="date"
                                class="input-field"
                            />
                        </div>
                    </div>
                    <div>
                        <label class="label">Status</label>
                        <select
                            v-model="form.status"
                            class="input-field bg-white"
                        >
                            <option>Pending</option>
                            <option>Active</option>
                            <option>Completed</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 pb-5 flex justify-end gap-3">
                    <button
                        @click="modal = false"
                        class="px-4 py-2 text-sm text-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        @click="save"
                        class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition"
                    >
                        {{ form.id ? "Save" : "Record" }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div
            v-if="detail"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="detail = null"
        >
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">
                    Transaction Detail #{{ String(detail.id).padStart(4, "0") }}
                </h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Customer</dt>
                        <dd class="font-medium">{{ detail.customer }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Car</dt>
                        <dd class="font-medium">{{ detail.car }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Pick-up</dt>
                        <dd>{{ detail.pickup }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Return</dt>
                        <dd>{{ detail.returnDate }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Days</dt>
                        <dd>{{ detail.days }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <dt class="text-gray-500">Total</dt>
                        <dd class="font-bold text-blue-700">
                            {{ detail.total }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                :class="statusClass(detail.status)"
                                >{{ detail.status }}</span
                            >
                        </dd>
                    </div>
                </dl>
                <button
                    @click="detail = null"
                    class="mt-5 w-full py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "RentalTransactions",
    data() {
        return {
            search: "",
            filterStatus: "",
            modal: false,
            detail: null,
            form: {},
            nextId: 10,
            transactions: [
                {
                    id: 1,
                    customer: "Budi Santoso",
                    car: "Toyota Avanza",
                    pickup: "2026-04-10",
                    returnDate: "2026-04-13",
                    days: 3,
                    total: "Rp 1.050.000",
                    status: "Active",
                },
                {
                    id: 2,
                    customer: "Siti Rahayu",
                    car: "Honda Jazz",
                    pickup: "2026-04-08",
                    returnDate: "2026-04-10",
                    days: 2,
                    total: "Rp 560.000",
                    status: "Completed",
                },
                {
                    id: 3,
                    customer: "Ahmad Fauzi",
                    car: "Mitsubishi Pajero",
                    pickup: "2026-04-11",
                    returnDate: "2026-04-14",
                    days: 3,
                    total: "Rp 2.550.000",
                    status: "Pending",
                },
                {
                    id: 4,
                    customer: "Dewi Kurniawati",
                    car: "Toyota Innova",
                    pickup: "2026-04-10",
                    returnDate: "2026-04-13",
                    days: 3,
                    total: "Rp 1.650.000",
                    status: "Active",
                },
                {
                    id: 5,
                    customer: "Rudi Hermawan",
                    car: "Daihatsu Xenia",
                    pickup: "2026-04-09",
                    returnDate: "2026-04-09",
                    days: 0,
                    total: "Rp 0",
                    status: "Cancelled",
                },
                {
                    id: 6,
                    customer: "Lina Marlina",
                    car: "Honda Brio",
                    pickup: "2026-04-07",
                    returnDate: "2026-04-09",
                    days: 2,
                    total: "Rp 440.000",
                    status: "Completed",
                },
                {
                    id: 7,
                    customer: "Hendra Wijaya",
                    car: "Toyota Fortuner",
                    pickup: "2026-04-13",
                    returnDate: "2026-04-16",
                    days: 3,
                    total: "Rp 2.850.000",
                    status: "Pending",
                },
            ],
        };
    },
    computed: {
        filtered() {
            return this.transactions.filter((t) => {
                const s = this.search.toLowerCase();
                return (
                    (!s ||
                        t.customer.toLowerCase().includes(s) ||
                        t.car.toLowerCase().includes(s)) &&
                    (!this.filterStatus || t.status === this.filterStatus)
                );
            });
        },
        summaryStats() {
            const counts = {
                Pending: 0,
                Active: 0,
                Completed: 0,
                Cancelled: 0,
            };
            this.transactions.forEach((t) => counts[t.status]++);
            return [
                {
                    label: "Pending",
                    val: counts.Pending,
                    icon: "⏳",
                    cls: "bg-yellow-50 text-yellow-700",
                },
                {
                    label: "Active",
                    val: counts.Active,
                    icon: "✅",
                    cls: "bg-green-50 text-green-700",
                },
                {
                    label: "Completed",
                    val: counts.Completed,
                    icon: "🏁",
                    cls: "bg-gray-100 text-gray-600",
                },
                {
                    label: "Cancelled",
                    val: counts.Cancelled,
                    icon: "❌",
                    cls: "bg-red-50 text-red-600",
                },
            ];
        },
    },
    methods: {
        statusClass(s) {
            return (
                {
                    Pending: "bg-yellow-100 text-yellow-700",
                    Active: "bg-green-100 text-green-700",
                    Completed: "bg-gray-100 text-gray-600",
                    Cancelled: "bg-red-100 text-red-600",
                }[s] || ""
            );
        },
        openModal(r = null) {
            this.form = r
                ? { ...r }
                : {
                      id: null,
                      customer: "",
                      car: "Toyota Avanza",
                      pickup: "",
                      returnDate: "",
                      status: "Pending",
                  };
            this.modal = true;
        },
        save() {
            if (this.form.id) {
                const idx = this.transactions.findIndex(
                    (t) => t.id === this.form.id,
                );
                if (idx !== -1)
                    this.transactions.splice(idx, 1, {
                        ...this.transactions[idx],
                        ...this.form,
                    });
            } else {
                this.transactions.push({
                    ...this.form,
                    id: this.nextId++,
                    days: 1,
                    total: "Rp 0",
                });
            }
            this.modal = false;
        },
        updateStatus(r) {
            // In real app: call API to update status
        },
        viewDetail(r) {
            this.detail = r;
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
.label {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgb(107, 114, 128);
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    display: block;
}
</style>
