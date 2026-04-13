<template>
    <div class="space-y-5">
        <!-- Summary cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div
                v-for="s in summary"
                :key="s.label"
                class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
            >
                <p
                    class="text-xs text-gray-400 uppercase tracking-wide font-medium"
                >
                    {{ s.label }}
                </p>
                <p class="text-xl font-bold text-gray-900 mt-1">
                    {{ s.value }}
                </p>
                <p class="text-xs mt-1" :class="s.sub_cls">{{ s.sub }}</p>
            </div>
        </div>

        <!-- Toolbar -->
        <div
            class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center"
        >
            <div class="flex gap-2 flex-wrap">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search customer or method..."
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-56"
                />
                <select
                    v-model="filterStatus"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >
                    <option value="">All Status</option>
                    <option>Paid</option>
                    <option>Pending</option>
                    <option>Failed</option>
                    <option>Refunded</option>
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
                Add Payment
            </button>
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
                            <th class="text-left px-5 py-3">Invoice</th>
                            <th class="text-left px-5 py-3">Customer</th>
                            <th class="text-left px-5 py-3">Rental #</th>
                            <th class="text-left px-5 py-3">Method</th>
                            <th class="text-left px-5 py-3">Amount</th>
                            <th class="text-left px-5 py-3">Date</th>
                            <th class="text-left px-5 py-3">Status</th>
                            <th class="text-center px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="p in filtered"
                            :key="p.id"
                            class="border-b border-gray-50 hover:bg-gray-50"
                        >
                            <td
                                class="px-5 py-3 font-mono text-xs text-blue-600"
                            >
                                {{ p.invoice }}
                            </td>
                            <td class="px-5 py-3 font-medium text-gray-800">
                                {{ p.customer }}
                            </td>
                            <td
                                class="px-5 py-3 font-mono text-xs text-gray-500"
                            >
                                #{{ String(p.rentalId).padStart(4, "0") }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="flex items-center gap-1 text-gray-600"
                                >
                                    <span>{{ methodIcon(p.method) }}</span
                                    >{{ p.method }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-semibold text-gray-800">
                                {{ p.amount }}
                            </td>
                            <td class="px-5 py-3 text-gray-500">
                                {{ p.date }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                    :class="statusClass(p.status)"
                                >
                                    {{ p.status }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <button
                                        @click="openModal(p)"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"
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
                                        @click="printInvoice(p)"
                                        class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg"
                                        title="Print Invoice"
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
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filtered.length === 0">
                            <td
                                colspan="8"
                                class="text-center py-10 text-gray-400"
                            >
                                No payment records found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400"
            >
                {{ filtered.length }} payment(s) shown
            </div>
        </div>

        <!-- Modal -->
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
                        {{ form.id ? "Edit Payment" : "Add Payment" }}
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
                        <label class="label">Customer</label>
                        <input
                            v-model="form.customer"
                            type="text"
                            class="input-field"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Amount (Rp)</label>
                            <input
                                v-model.number="form.amountRaw"
                                type="number"
                                class="input-field"
                            />
                        </div>
                        <div>
                            <label class="label">Method</label>
                            <select
                                v-model="form.method"
                                class="input-field bg-white"
                            >
                                <option>Transfer Bank</option>
                                <option>Credit Card</option>
                                <option>Cash</option>
                                <option>QRIS</option>
                                <option>OVO</option>
                                <option>GoPay</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Payment Date</label>
                        <input
                            v-model="form.date"
                            type="date"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label class="label">Status</label>
                        <select
                            v-model="form.status"
                            class="input-field bg-white"
                        >
                            <option>Paid</option>
                            <option>Pending</option>
                            <option>Failed</option>
                            <option>Refunded</option>
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
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "PaymentData",
    data() {
        return {
            search: "",
            filterStatus: "",
            modal: false,
            form: {},
            nextId: 10,
            payments: [
                {
                    id: 1,
                    invoice: "INV-2026-001",
                    customer: "Budi Santoso",
                    rentalId: 1,
                    method: "Transfer Bank",
                    amount: "Rp 1.050.000",
                    amountRaw: 1050000,
                    date: "2026-04-10",
                    status: "Paid",
                },
                {
                    id: 2,
                    invoice: "INV-2026-002",
                    customer: "Siti Rahayu",
                    rentalId: 2,
                    method: "Credit Card",
                    amount: "Rp 560.000",
                    amountRaw: 560000,
                    date: "2026-04-08",
                    status: "Paid",
                },
                {
                    id: 3,
                    invoice: "INV-2026-003",
                    customer: "Ahmad Fauzi",
                    rentalId: 3,
                    method: "QRIS",
                    amount: "Rp 2.550.000",
                    amountRaw: 2550000,
                    date: "2026-04-11",
                    status: "Pending",
                },
                {
                    id: 4,
                    invoice: "INV-2026-004",
                    customer: "Dewi Kurniawati",
                    rentalId: 4,
                    method: "Cash",
                    amount: "Rp 1.650.000",
                    amountRaw: 1650000,
                    date: "2026-04-10",
                    status: "Paid",
                },
                {
                    id: 5,
                    invoice: "INV-2026-005",
                    customer: "Rudi Hermawan",
                    rentalId: 5,
                    method: "GoPay",
                    amount: "Rp 0",
                    amountRaw: 0,
                    date: "2026-04-09",
                    status: "Refunded",
                },
                {
                    id: 6,
                    invoice: "INV-2026-006",
                    customer: "Lina Marlina",
                    rentalId: 6,
                    method: "OVO",
                    amount: "Rp 440.000",
                    amountRaw: 440000,
                    date: "2026-04-07",
                    status: "Paid",
                },
                {
                    id: 7,
                    invoice: "INV-2026-007",
                    customer: "Hendra Wijaya",
                    rentalId: 7,
                    method: "Transfer Bank",
                    amount: "Rp 2.850.000",
                    amountRaw: 2850000,
                    date: "2026-04-13",
                    status: "Pending",
                },
            ],
        };
    },
    computed: {
        filtered() {
            const s = this.search.toLowerCase();
            return this.payments.filter(
                (p) =>
                    (!s ||
                        p.customer.toLowerCase().includes(s) ||
                        p.method.toLowerCase().includes(s)) &&
                    (!this.filterStatus || p.status === this.filterStatus),
            );
        },
        summary() {
            const paid = this.payments
                .filter((p) => p.status === "Paid")
                .reduce((a, p) => a + p.amountRaw, 0);
            const pending = this.payments.filter(
                (p) => p.status === "Pending",
            ).length;
            const failed = this.payments.filter(
                (p) => p.status === "Failed",
            ).length;
            const total = this.payments.reduce((a, p) => a + p.amountRaw, 0);
            return [
                {
                    label: "Total Revenue",
                    value: `Rp ${paid.toLocaleString("id-ID")}`,
                    sub: "Paid invoices",
                    sub_cls: "text-green-500",
                },
                {
                    label: "Total Invoices",
                    value: this.payments.length,
                    sub: "All time",
                    sub_cls: "text-gray-400",
                },
                {
                    label: "Pending",
                    value: pending,
                    sub: "Awaiting payment",
                    sub_cls: "text-yellow-500",
                },
                {
                    label: "Failed/Refunded",
                    value:
                        failed +
                        this.payments.filter((p) => p.status === "Refunded")
                            .length,
                    sub: "Needs attention",
                    sub_cls: "text-red-400",
                },
            ];
        },
    },
    methods: {
        statusClass(s) {
            return (
                {
                    Paid: "bg-green-100 text-green-700",
                    Pending: "bg-yellow-100 text-yellow-700",
                    Failed: "bg-red-100 text-red-600",
                    Refunded: "bg-gray-100 text-gray-600",
                }[s] || ""
            );
        },
        methodIcon(m) {
            return (
                {
                    "Transfer Bank": "🏦",
                    "Credit Card": "💳",
                    Cash: "💵",
                    QRIS: "📱",
                    OVO: "💜",
                    GoPay: "🟢",
                }[m] || "💰"
            );
        },
        openModal(p = null) {
            this.form = p
                ? { ...p }
                : {
                      id: null,
                      customer: "",
                      amountRaw: 0,
                      method: "Transfer Bank",
                      date: new Date().toISOString().split("T")[0],
                      status: "Pending",
                      rentalId: null,
                  };
            this.modal = true;
        },
        save() {
            const amount = `Rp ${Number(this.form.amountRaw).toLocaleString("id-ID")}`;
            if (this.form.id) {
                const idx = this.payments.findIndex(
                    (p) => p.id === this.form.id,
                );
                if (idx !== -1)
                    this.payments.splice(idx, 1, {
                        ...this.payments[idx],
                        ...this.form,
                        amount,
                    });
            } else {
                const id = this.nextId++;
                this.payments.push({
                    ...this.form,
                    id,
                    amount,
                    invoice: `INV-2026-${String(id).padStart(3, "0")}`,
                });
            }
            this.modal = false;
        },
        printInvoice(p) {
            alert(
                `Printing invoice ${p.invoice} for ${p.customer} — ${p.amount}`,
            );
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
