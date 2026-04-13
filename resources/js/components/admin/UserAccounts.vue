<template>
    <div class="space-y-5">
        <!-- Tabs: Customer / Owner / Staff -->
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                :class="
                    activeTab === tab.key
                        ? 'bg-white text-blue-700 shadow-sm font-semibold'
                        : 'text-gray-500 hover:text-gray-700'
                "
                class="px-5 py-2 rounded-lg text-sm transition"
            >
                {{ tab.label }}
                <span
                    class="ml-1.5 text-xs rounded-full px-1.5 py-0.5"
                    :class="
                        activeTab === tab.key
                            ? 'bg-blue-100 text-blue-700'
                            : 'bg-gray-200 text-gray-500'
                    "
                >
                    {{ usersByTab(tab.key).length }}
                </span>
            </button>
        </div>

        <!-- Toolbar -->
        <div
            class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center"
        >
            <input
                v-model="search"
                type="text"
                :placeholder="`Search ${activeTab.toLowerCase()}...`"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-56"
            />
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
                Add {{ activeTab }}
            </button>
        </div>

        <!-- User table -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr
                            class="text-xs text-gray-500 uppercase tracking-wide"
                        >
                            <th class="text-left px-5 py-3">User</th>
                            <th class="text-left px-5 py-3">Username</th>
                            <th
                                class="text-left px-5 py-3 hidden sm:table-cell"
                            >
                                Contact
                            </th>
                            <th
                                v-if="activeTab === 'Customer'"
                                class="text-left px-5 py-3 hidden lg:table-cell"
                            >
                                KTP / SIM
                            </th>
                            <th
                                v-if="activeTab === 'Owner'"
                                class="text-left px-5 py-3 hidden lg:table-cell"
                            >
                                Fleet
                            </th>
                            <th
                                v-if="activeTab === 'Staff'"
                                class="text-left px-5 py-3 hidden lg:table-cell"
                            >
                                Position
                            </th>
                            <th class="text-left px-5 py-3">Role</th>
                            <th class="text-left px-5 py-3">Status</th>
                            <th class="text-center px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="u in filteredUsers"
                            :key="u.id"
                            class="border-b border-gray-50 hover:bg-gray-50"
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0"
                                        :style="{ background: u.color }"
                                    >
                                        {{ u.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ u.name }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ u.email }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td
                                class="px-5 py-3 font-mono text-xs text-gray-600"
                            >
                                {{ u.username }}
                            </td>
                            <td
                                class="px-5 py-3 text-gray-600 hidden sm:table-cell"
                            >
                                {{ u.phone }}
                            </td>
                            <td
                                v-if="activeTab === 'Customer'"
                                class="px-5 py-3 text-gray-500 text-xs hidden lg:table-cell"
                            >
                                <div>KTP: {{ u.ktp }}</div>
                                <div>SIM: {{ u.sim }}</div>
                            </td>
                            <td
                                v-if="activeTab === 'Owner'"
                                class="px-5 py-3 text-gray-600 hidden lg:table-cell"
                            >
                                {{ u.fleet }} cars
                            </td>
                            <td
                                v-if="activeTab === 'Staff'"
                                class="px-5 py-3 text-gray-600 hidden lg:table-cell"
                            >
                                {{ u.position }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                    :class="roleClass(u.role)"
                                    >{{ u.role }}</span
                                >
                            </td>
                            <td class="px-5 py-3">
                                <button
                                    @click="u.active = !u.active"
                                    :class="
                                        u.active
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-gray-100 text-gray-500'
                                    "
                                    class="px-2 py-0.5 rounded-full text-xs font-semibold transition"
                                >
                                    {{ u.active ? "Active" : "Inactive" }}
                                </button>
                            </td>
                            <td class="px-5 py-3">
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <button
                                        @click="openModal(u)"
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
                                        @click="resetPassword(u)"
                                        class="p-1.5 text-yellow-500 hover:bg-yellow-50 rounded-lg"
                                        title="Reset Password"
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
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="deleteUser(u.id)"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg"
                                        title="Delete"
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
                        <tr v-if="filteredUsers.length === 0">
                            <td
                                :colspan="activeTab === 'Customer' ? 8 : 7"
                                class="text-center py-10 text-gray-400"
                            >
                                No {{ activeTab.toLowerCase() }}s found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400"
            >
                {{ filteredUsers.length }} {{ activeTab.toLowerCase() }}(s)
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div
            v-if="modal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="modal = false"
        >
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            >
                <div
                    class="px-6 py-4 border-b border-gray-100 flex items-center justify-between"
                >
                    <h3 class="font-bold text-gray-900">
                        {{ form.id ? "Edit User" : "Add " + activeTab }}
                    </h3>
                    <button
                        @click="modal = false"
                        class="text-gray-400 hover:text-gray-700"
                    >
                        ✕
                    </button>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="label">Full Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label class="label">Username</label>
                        <input
                            v-model="form.username"
                            type="text"
                            class="input-field font-mono"
                        />
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label class="label">Phone</label>
                        <input
                            v-model="form.phone"
                            type="text"
                            class="input-field"
                        />
                    </div>
                    <div>
                        <label class="label">Role</label>
                        <select
                            v-model="form.role"
                            class="input-field bg-white"
                        >
                            <option>Customer</option>
                            <option>Owner</option>
                            <option>Staff</option>
                            <option>Administrator</option>
                        </select>
                    </div>
                    <template
                        v-if="
                            activeTab === 'Customer' || form.role === 'Customer'
                        "
                    >
                        <div>
                            <label class="label">No. KTP</label>
                            <input
                                v-model="form.ktp"
                                type="text"
                                maxlength="16"
                                class="input-field font-mono"
                            />
                        </div>
                        <div>
                            <label class="label">No. SIM</label>
                            <input
                                v-model="form.sim"
                                type="text"
                                class="input-field font-mono"
                            />
                        </div>
                        <div class="col-span-2">
                            <label class="label">Address</label>
                            <textarea
                                v-model="form.address"
                                rows="2"
                                class="input-field resize-none"
                            ></textarea>
                        </div>
                    </template>
                    <template
                        v-if="activeTab === 'Staff' || form.role === 'Staff'"
                    >
                        <div class="col-span-2">
                            <label class="label">Position</label>
                            <select
                                v-model="form.position"
                                class="input-field bg-white"
                            >
                                <option>Receptionist</option>
                                <option>Driver</option>
                                <option>Mechanic</option>
                                <option>Finance</option>
                                <option>Marketing</option>
                            </select>
                        </div>
                    </template>
                    <div v-if="!form.id" class="col-span-2">
                        <label class="label">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="input-field"
                            placeholder="Min 8 characters"
                        />
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
                        @click="saveUser"
                        class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition"
                    >
                        {{ form.id ? "Save Changes" : "Create User" }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const COLORS = [
    "#3b82f6",
    "#10b981",
    "#f59e0b",
    "#ef4444",
    "#8b5cf6",
    "#ec4899",
    "#06b6d4",
    "#84cc16",
];
export default {
    name: "UserAccounts",
    data() {
        return {
            activeTab: "Customer",
            search: "",
            modal: false,
            form: {},
            nextId: 100,
            tabs: [
                { key: "Customer", label: "Customer" },
                { key: "Owner", label: "Owner" },
                { key: "Staff", label: "Staff" },
            ],
            users: [
                // Customers
                {
                    id: 1,
                    name: "Budi Santoso",
                    username: "budi.s",
                    email: "budi@mail.com",
                    phone: "081234567890",
                    role: "Customer",
                    ktp: "3271010101800001",
                    sim: "SIM-001",
                    active: true,
                    color: COLORS[0],
                    address: "Jakarta Selatan",
                },
                {
                    id: 2,
                    name: "Siti Rahayu",
                    username: "siti.r",
                    email: "siti@mail.com",
                    phone: "082345678901",
                    role: "Customer",
                    ktp: "3271020202850002",
                    sim: "SIM-002",
                    active: true,
                    color: COLORS[1],
                    address: "Bandung",
                },
                {
                    id: 3,
                    name: "Ahmad Fauzi",
                    username: "ahmad.f",
                    email: "ahmad@mail.com",
                    phone: "083456789012",
                    role: "Customer",
                    ktp: "3271030303900003",
                    sim: "SIM-003",
                    active: true,
                    color: COLORS[2],
                    address: "Surabaya",
                },
                {
                    id: 4,
                    name: "Dewi Kurniawati",
                    username: "dewi.k",
                    email: "dewi@mail.com",
                    phone: "084567890123",
                    role: "Customer",
                    ktp: "3271040404880004",
                    sim: "SIM-004",
                    active: false,
                    color: COLORS[3],
                    address: "Malang",
                },
                {
                    id: 5,
                    name: "Rudi Hermawan",
                    username: "rudi.h",
                    email: "rudi@mail.com",
                    phone: "085678901234",
                    role: "Customer",
                    ktp: "3271050505920005",
                    sim: "SIM-005",
                    active: true,
                    color: COLORS[4],
                    address: "Yogyakarta",
                },
                // Owners
                {
                    id: 6,
                    name: "PT. Maju Jaya",
                    username: "majujaya",
                    email: "maju@corp.com",
                    phone: "0215551001",
                    role: "Owner",
                    fleet: 12,
                    active: true,
                    color: COLORS[5],
                },
                {
                    id: 7,
                    name: "CV. Berkah Trans",
                    username: "berkahtrans",
                    email: "berkah@corp.com",
                    phone: "0215551002",
                    role: "Owner",
                    fleet: 8,
                    active: true,
                    color: COLORS[6],
                },
                {
                    id: 8,
                    name: "Hendra Wijaya",
                    username: "hendra.w",
                    email: "hendra@mail.com",
                    phone: "081298765432",
                    role: "Owner",
                    fleet: 3,
                    active: true,
                    color: COLORS[7],
                },
                // Staff
                {
                    id: 9,
                    name: "Rina Wulandari",
                    username: "rina.w",
                    email: "rina@eb.com",
                    phone: "087700001111",
                    role: "Staff",
                    position: "Receptionist",
                    active: true,
                    color: COLORS[0],
                },
                {
                    id: 10,
                    name: "Dian Prakoso",
                    username: "dian.p",
                    email: "dian@eb.com",
                    phone: "087700002222",
                    role: "Staff",
                    position: "Mechanic",
                    active: true,
                    color: COLORS[2],
                },
                {
                    id: 11,
                    name: "Agus Salim",
                    username: "agus.s",
                    email: "agus@eb.com",
                    phone: "087700003333",
                    role: "Staff",
                    position: "Finance",
                    active: true,
                    color: COLORS[3],
                },
                {
                    id: 12,
                    name: "Maya Sari",
                    username: "maya.s",
                    email: "maya@eb.com",
                    phone: "087700004444",
                    role: "Staff",
                    position: "Driver",
                    active: false,
                    color: COLORS[4],
                },
            ],
        };
    },
    computed: {
        filteredUsers() {
            const s = this.search.toLowerCase();
            return this.usersByTab(this.activeTab).filter(
                (u) =>
                    !s ||
                    u.name.toLowerCase().includes(s) ||
                    (u.username || "").toLowerCase().includes(s) ||
                    (u.email || "").toLowerCase().includes(s),
            );
        },
    },
    methods: {
        usersByTab(tab) {
            return this.users.filter((u) => u.role === tab);
        },
        roleClass(role) {
            return (
                {
                    Customer: "bg-blue-100 text-blue-700",
                    Owner: "bg-purple-100 text-purple-700",
                    Staff: "bg-green-100 text-green-700",
                    Administrator: "bg-red-100 text-red-700",
                }[role] || "bg-gray-100 text-gray-600"
            );
        },
        openModal(u = null) {
            this.form = u
                ? { ...u }
                : {
                      id: null,
                      name: "",
                      username: "",
                      email: "",
                      phone: "",
                      role: this.activeTab,
                      ktp: "",
                      sim: "",
                      position: "Receptionist",
                      fleet: 0,
                      address: "",
                      active: true,
                      password: "",
                  };
            this.modal = true;
        },
        saveUser() {
            if (this.form.id) {
                const idx = this.users.findIndex((u) => u.id === this.form.id);
                if (idx !== -1)
                    this.users.splice(idx, 1, {
                        ...this.users[idx],
                        ...this.form,
                    });
            } else {
                this.users.push({
                    ...this.form,
                    id: this.nextId++,
                    color: COLORS[this.nextId % COLORS.length],
                });
            }
            this.modal = false;
        },
        resetPassword(u) {
            alert(`Password reset link sent to ${u.email}`);
        },
        deleteUser(id) {
            if (confirm("Delete this user?")) {
                this.users = this.users.filter((u) => u.id !== id);
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
.label {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgb(107, 114, 128);
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    display: block;
}
</style>
