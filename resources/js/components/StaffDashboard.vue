<template>
    <div class="flex h-screen bg-gray-100 overflow-hidden">
        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-[#1a2332] to-[#0f1419] text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-xl lg:shadow-none"
        >
            <!-- Logo -->
            <div
                class="flex items-center gap-2 px-6 py-5 border-b border-white/10 bg-gradient-to-r from-teal-600 to-teal-700"
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
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold leading-tight text-white">
                        Staff Panel
                    </p>
                    <p class="text-xs text-teal-100">Maintenance & Delivery</p>
                </div>
            </div>

            <!-- User info -->
            <div class="px-6 py-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-full bg-teal-500 flex items-center justify-center font-bold text-sm"
                    >
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white truncate">
                            {{ user.name }}
                        </p>
                        <p class="text-xs text-teal-300">Staff</p>
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
                            ? 'bg-teal-600 text-white shadow-md'
                            : 'text-gray-300 hover:bg-white/10 hover:text-white'
                    "
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
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
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden text-gray-500 hover:text-gray-700 p-1.5 hover:bg-gray-100 rounded-lg transition"
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
                            {{ currentMenu.sub }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 ml-auto">
                    <span
                        class="text-xs text-gray-500 hidden sm:inline-block whitespace-nowrap"
                        >{{ today }}</span
                    >
                    <div
                        class="w-8 h-8 rounded-full bg-teal-600 flex items-center justify-center text-white text-sm font-bold"
                    >
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    <!-- ===================== PAGE: DELIVERY TASKS ===================== -->
                    <div v-if="activePage === 'deliveries'" class="space-y-5">
                        <div
                            v-if="loadingDeliveries"
                            class="text-center py-16 text-gray-400"
                        >
                            <svg
                                class="w-8 h-8 mx-auto mb-3 animate-spin text-teal-400"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8z"
                                />
                            </svg>
                            Memuat tugas pengantaran...
                        </div>

                        <template v-else>
                            <!-- Delivery summary cards -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div
                                    class="rounded-xl p-4 flex items-center gap-3 shadow-sm border bg-yellow-50 border-yellow-200"
                                >
                                    <div class="text-2xl">⏳</div>
                                    <div>
                                        <p
                                            class="text-xl font-bold text-yellow-700"
                                        >
                                            {{ deliveryPending.length }}
                                        </p>
                                        <p
                                            class="text-[10px] font-semibold text-yellow-700"
                                        >
                                            Menunggu
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl p-4 flex items-center gap-3 shadow-sm border bg-blue-50 border-blue-200"
                                >
                                    <div class="text-2xl">🚗</div>
                                    <div>
                                        <p
                                            class="text-xl font-bold text-blue-700"
                                        >
                                            {{ deliveryOnTheWay.length }}
                                        </p>
                                        <p
                                            class="text-[10px] font-semibold text-blue-700"
                                        >
                                            Dalam Perjalanan
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl p-4 flex items-center gap-3 shadow-sm border bg-orange-50 border-orange-200"
                                >
                                    <div class="text-2xl">🏪</div>
                                    <div>
                                        <p
                                            class="text-xl font-bold text-orange-700"
                                        >
                                            {{ workshopDeliveries.length }}
                                        </p>
                                        <p
                                            class="text-[10px] font-semibold text-orange-700"
                                        >
                                            Ke Bengkel
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl p-4 flex items-center gap-3 shadow-sm border bg-green-50 border-green-200"
                                >
                                    <div class="text-2xl">👤</div>
                                    <div>
                                        <p
                                            class="text-xl font-bold text-green-700"
                                        >
                                            {{ customerDeliveries.length }}
                                        </p>
                                        <p
                                            class="text-[10px] font-semibold text-green-700"
                                        >
                                            Ke Pelanggan
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter tabs -->
                            <div class="flex gap-2">
                                <button
                                    v-for="f in deliveryFilters"
                                    :key="f.val"
                                    @click="deliveryFilter = f.val"
                                    :class="
                                        deliveryFilter === f.val
                                            ? 'bg-teal-600 text-white'
                                            : 'bg-white text-gray-600 hover:bg-gray-50'
                                    "
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 transition"
                                >
                                    {{ f.label }}
                                </button>
                            </div>

                            <!-- Active delivery tasks -->
                            <div
                                v-for="task in filteredDeliveries"
                                :key="task.id"
                                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                            >
                                <div class="flex items-stretch">
                                    <!-- Color bar -->
                                    <div
                                        class="w-2 shrink-0"
                                        :class="{
                                            'bg-yellow-400':
                                                task.status === 'pending',
                                            'bg-blue-500':
                                                task.status === 'on_the_way',
                                            'bg-green-500':
                                                task.status === 'delivered',
                                        }"
                                    ></div>
                                    <div class="flex-1 p-5">
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-3"
                                        >
                                            <div class="flex-1">
                                                <div
                                                    class="flex items-center gap-2 mb-1 flex-wrap"
                                                >
                                                    <!-- Type badge -->
                                                    <span
                                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                                        :class="
                                                            task.type ===
                                                            'workshop_delivery'
                                                                ? 'bg-orange-100 text-orange-700'
                                                                : 'bg-purple-100 text-purple-700'
                                                        "
                                                    >
                                                        {{
                                                            task.type ===
                                                            "workshop_delivery"
                                                                ? "🏪 Antar ke Bengkel"
                                                                : "👤 Antar ke Pelanggan"
                                                        }}
                                                    </span>
                                                    <!-- Status badge -->
                                                    <span
                                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                                        :class="{
                                                            'bg-yellow-100 text-yellow-700':
                                                                task.status ===
                                                                'pending',
                                                            'bg-blue-100 text-blue-700':
                                                                task.status ===
                                                                'on_the_way',
                                                            'bg-green-100 text-green-700':
                                                                task.status ===
                                                                'delivered',
                                                        }"
                                                    >
                                                        {{
                                                            deliveryStatusLabel(
                                                                task.status,
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] text-gray-400"
                                                        >{{
                                                            formatDateTime(
                                                                task.created_at,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                                <h4
                                                    class="font-bold text-gray-900 mb-1"
                                                >
                                                    {{ task.car?.brand }}
                                                    {{ task.car?.model }}
                                                    <span
                                                        class="text-xs font-normal text-gray-400 ml-1"
                                                        >{{
                                                            task.car
                                                                ?.license_plate
                                                        }}</span
                                                    >
                                                </h4>
                                                <div
                                                    class="flex items-start gap-1 mb-2"
                                                >
                                                    <span class="text-sm"
                                                        >📍</span
                                                    >
                                                    <p
                                                        class="text-sm text-gray-700 font-medium"
                                                    >
                                                        {{ task.destination }}
                                                    </p>
                                                </div>

                                                <!-- Additional info -->
                                                <div
                                                    class="flex flex-wrap gap-3 text-xs text-gray-500"
                                                >
                                                    <span
                                                        v-if="
                                                            task.type ===
                                                                'workshop_delivery' &&
                                                            task.maintenance_record
                                                        "
                                                    >
                                                        🔧 Perbaikan:
                                                        {{
                                                            task
                                                                .maintenance_record
                                                                .description
                                                        }}
                                                    </span>
                                                    <span
                                                        v-if="
                                                            task.type ===
                                                                'customer_delivery' &&
                                                            task.transaction
                                                                ?.user
                                                        "
                                                    >
                                                        👤 Pelanggan:
                                                        <strong>{{
                                                            task.transaction
                                                                .user.name
                                                        }}</strong>
                                                    </span>
                                                    <span
                                                        v-if="
                                                            task.type ===
                                                                'customer_delivery' &&
                                                            task.transaction
                                                                ?.invoice_number
                                                        "
                                                    >
                                                        📋
                                                        {{
                                                            task.transaction
                                                                .invoice_number
                                                        }}
                                                    </span>
                                                    <span v-if="task.notes"
                                                        >📝
                                                        {{ task.notes }}</span
                                                    >
                                                    <span
                                                        v-if="task.picked_up_at"
                                                        >🕐 Berangkat:
                                                        {{
                                                            formatDateTime(
                                                                task.picked_up_at,
                                                            )
                                                        }}</span
                                                    >
                                                    <span
                                                        v-if="task.delivered_at"
                                                        >✅ Sampai:
                                                        {{
                                                            formatDateTime(
                                                                task.delivered_at,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Previous report -->
                                        <div
                                            v-if="task.staff_report"
                                            class="bg-gray-50 rounded-lg p-3 mb-3"
                                        >
                                            <p
                                                class="text-xs text-gray-400 mb-1 font-semibold"
                                            >
                                                Laporan sebelumnya:
                                            </p>
                                            <p class="text-sm text-gray-700">
                                                {{ task.staff_report }}
                                            </p>
                                        </div>

                                        <!-- Action form (only for non-delivered) -->
                                        <div
                                            v-if="task.status !== 'delivered'"
                                            class="bg-teal-50 rounded-lg p-4"
                                        >
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2"
                                                >Laporan Pengantaran</label
                                            >
                                            <textarea
                                                v-model="task._report"
                                                rows="2"
                                                placeholder="Tulis laporan... (misal: Mobil sudah tiba di bengkel, kondisi baik)"
                                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 mb-3"
                                            ></textarea>
                                            <div class="flex flex-wrap gap-2">
                                                <button
                                                    v-if="
                                                        task.status ===
                                                        'pending'
                                                    "
                                                    @click="
                                                        updateDelivery(
                                                            task,
                                                            'on_the_way',
                                                        )
                                                    "
                                                    :disabled="
                                                        deliveryUpdating ===
                                                        task.id
                                                    "
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition disabled:opacity-50"
                                                >
                                                    {{
                                                        deliveryUpdating ===
                                                        task.id
                                                            ? "Memproses..."
                                                            : "🚗 Berangkat Antar"
                                                    }}
                                                </button>
                                                <button
                                                    @click="
                                                        updateDelivery(
                                                            task,
                                                            'delivered',
                                                        )
                                                    "
                                                    :disabled="
                                                        deliveryUpdating ===
                                                        task.id
                                                    "
                                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition disabled:opacity-50"
                                                >
                                                    {{
                                                        deliveryUpdating ===
                                                        task.id
                                                            ? "Memproses..."
                                                            : "✅ Sudah Diantar"
                                                    }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="filteredDeliveries.length === 0"
                                class="text-center py-16 text-gray-400"
                            >
                                <p class="text-4xl mb-3">📦</p>
                                <p class="font-semibold">
                                    Tidak ada tugas pengantaran{{
                                        deliveryFilter !== "all"
                                            ? " dengan filter ini"
                                            : ""
                                    }}.
                                </p>
                            </div>
                        </template>
                    </div>

                    <!-- ===================== PAGE: MAINTENANCE TASKS ===================== -->
                    <div v-else-if="activePage === 'tasks'" class="space-y-5">
                        <div
                            v-if="loading"
                            class="text-center py-16 text-gray-400"
                        >
                            <svg
                                class="w-8 h-8 mx-auto mb-3 animate-spin text-teal-400"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8z"
                                />
                            </svg>
                            Memuat tugas...
                        </div>

                        <template v-else>
                            <!-- Summary cards -->
                            <div class="grid grid-cols-3 gap-4">
                                <div
                                    class="rounded-xl p-4 flex items-center gap-4 shadow-sm border bg-yellow-50 border-yellow-200"
                                >
                                    <div class="text-3xl">⏳</div>
                                    <div>
                                        <p
                                            class="text-2xl font-bold text-yellow-700"
                                        >
                                            {{ pendingTasks.length }}
                                        </p>
                                        <p
                                            class="text-xs font-semibold text-yellow-700"
                                        >
                                            Menunggu
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl p-4 flex items-center gap-4 shadow-sm border bg-blue-50 border-blue-200"
                                >
                                    <div class="text-3xl">🔧</div>
                                    <div>
                                        <p
                                            class="text-2xl font-bold text-blue-700"
                                        >
                                            {{ inProgressTasks.length }}
                                        </p>
                                        <p
                                            class="text-xs font-semibold text-blue-700"
                                        >
                                            Dalam Proses
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl p-4 flex items-center gap-4 shadow-sm border bg-green-50 border-green-200"
                                >
                                    <div class="text-3xl">✅</div>
                                    <div>
                                        <p
                                            class="text-2xl font-bold text-green-700"
                                        >
                                            {{ completedTasks.length }}
                                        </p>
                                        <p
                                            class="text-xs font-semibold text-green-700"
                                        >
                                            Selesai
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Active tasks list -->
                            <div
                                v-for="task in activeTasks"
                                :key="task.id"
                                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                            >
                                <div class="flex items-stretch">
                                    <div
                                        class="w-2 shrink-0"
                                        :class="
                                            task.status === 'pending'
                                                ? 'bg-yellow-400'
                                                : 'bg-blue-500'
                                        "
                                    ></div>
                                    <div class="flex-1 p-5">
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-4"
                                        >
                                            <div class="flex-1">
                                                <div
                                                    class="flex items-center gap-2 mb-1"
                                                >
                                                    <span
                                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                                        :class="
                                                            task.status ===
                                                            'pending'
                                                                ? 'bg-yellow-100 text-yellow-700'
                                                                : 'bg-blue-100 text-blue-700'
                                                        "
                                                    >
                                                        {{
                                                            task.status ===
                                                            "pending"
                                                                ? "Menunggu"
                                                                : "Dalam Proses"
                                                        }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] text-gray-400"
                                                        >{{
                                                            formatDateTime(
                                                                task.created_at,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                                <h4
                                                    class="font-bold text-gray-900 mb-1"
                                                >
                                                    {{ task.car?.brand }}
                                                    {{ task.car?.model }}
                                                    <span
                                                        class="text-xs font-normal text-gray-400 ml-1"
                                                        >{{
                                                            task.car
                                                                ?.license_plate
                                                        }}</span
                                                    >
                                                </h4>
                                                <p
                                                    class="text-sm text-gray-600 mb-2"
                                                >
                                                    {{ task.description }}
                                                </p>
                                                <div
                                                    class="flex flex-wrap gap-4 text-xs text-gray-500"
                                                >
                                                    <span
                                                        >🏪
                                                        <strong>{{
                                                            task.workshop_name
                                                        }}</strong></span
                                                    >
                                                    <span
                                                        >💰
                                                        <strong
                                                            >Rp
                                                            {{
                                                                formatPrice(
                                                                    task.repair_cost,
                                                                )
                                                            }}</strong
                                                        ></span
                                                    >
                                                    <span v-if="task.started_at"
                                                        >🕐 Mulai:
                                                        {{
                                                            formatDate(
                                                                task.started_at,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            v-if="task.staff_notes"
                                            class="bg-gray-50 rounded-lg p-3 mb-4"
                                        >
                                            <p
                                                class="text-xs text-gray-400 mb-1 font-semibold"
                                            >
                                                Catatan terakhir:
                                            </p>
                                            <p class="text-sm text-gray-700">
                                                {{ task.staff_notes }}
                                            </p>
                                        </div>

                                        <div class="bg-teal-50 rounded-lg p-4">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2"
                                                >Laporan Progress
                                                Perawatan</label
                                            >
                                            <textarea
                                                v-model="task._notes"
                                                rows="3"
                                                placeholder="Tulis laporan progress perbaikan... (misal: Kampas rem sudah diganti, tinggal balancing roda)"
                                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 mb-3"
                                            ></textarea>
                                            <div class="flex flex-wrap gap-2">
                                                <button
                                                    v-if="
                                                        task.status ===
                                                        'pending'
                                                    "
                                                    @click="
                                                        updateTask(
                                                            task,
                                                            'in_progress',
                                                        )
                                                    "
                                                    :disabled="
                                                        !task._notes?.trim() ||
                                                        updatingId === task.id
                                                    "
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition disabled:opacity-50"
                                                >
                                                    {{
                                                        updatingId === task.id
                                                            ? "Menyimpan..."
                                                            : "🔧 Mulai Pengerjaan"
                                                    }}
                                                </button>
                                                <button
                                                    v-if="
                                                        task.status ===
                                                        'in_progress'
                                                    "
                                                    @click="
                                                        updateTask(
                                                            task,
                                                            'in_progress',
                                                        )
                                                    "
                                                    :disabled="
                                                        !task._notes?.trim() ||
                                                        updatingId === task.id
                                                    "
                                                    class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg transition disabled:opacity-50"
                                                >
                                                    {{
                                                        updatingId === task.id
                                                            ? "Menyimpan..."
                                                            : "📝 Update Progress"
                                                    }}
                                                </button>
                                                <button
                                                    @click="
                                                        updateTask(
                                                            task,
                                                            'completed',
                                                        )
                                                    "
                                                    :disabled="
                                                        !task._notes?.trim() ||
                                                        updatingId === task.id
                                                    "
                                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition disabled:opacity-50"
                                                >
                                                    {{
                                                        updatingId === task.id
                                                            ? "Menyimpan..."
                                                            : "✓ Selesai Perbaikan"
                                                    }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="activeTasks.length === 0"
                                class="text-center py-16 text-gray-400"
                            >
                                <p class="text-4xl mb-3">🎉</p>
                                <p class="font-semibold">
                                    Tidak ada tugas perawatan aktif saat ini.
                                </p>
                                <p class="text-sm">
                                    Semua maintenance sudah selesai!
                                </p>
                            </div>

                            <!-- Completed tasks -->
                            <div
                                v-if="completedTasks.length > 0"
                                class="bg-white rounded-xl shadow-sm border border-gray-100"
                            >
                                <button
                                    @click="showCompleted = !showCompleted"
                                    class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition"
                                >
                                    <h3 class="font-semibold text-gray-800">
                                        📋 Riwayat Perawatan Selesai ({{
                                            completedTasks.length
                                        }})
                                    </h3>
                                    <span class="text-gray-400 text-sm">{{
                                        showCompleted ? "▲" : "▼"
                                    }}</span>
                                </button>
                                <div
                                    v-if="showCompleted"
                                    class="overflow-x-auto border-t border-gray-100"
                                >
                                    <table class="w-full text-sm">
                                        <thead
                                            class="bg-gray-50 border-b border-gray-100"
                                        >
                                            <tr
                                                class="text-xs text-gray-500 uppercase tracking-wide"
                                            >
                                                <th class="text-left px-5 py-3">
                                                    Kendaraan
                                                </th>
                                                <th class="text-left px-5 py-3">
                                                    Deskripsi
                                                </th>
                                                <th class="text-left px-5 py-3">
                                                    Bengkel
                                                </th>
                                                <th
                                                    class="text-right px-5 py-3"
                                                >
                                                    Biaya
                                                </th>
                                                <th class="text-left px-5 py-3">
                                                    Catatan Akhir
                                                </th>
                                                <th class="text-left px-5 py-3">
                                                    Selesai
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="t in completedTasks"
                                                :key="t.id"
                                                class="border-b border-gray-50 hover:bg-gray-50"
                                            >
                                                <td
                                                    class="px-5 py-3 font-medium text-gray-800 whitespace-nowrap"
                                                >
                                                    {{ t.car?.brand }}
                                                    {{ t.car?.model }}
                                                </td>
                                                <td
                                                    class="px-5 py-3 text-gray-600 text-xs max-w-[150px]"
                                                >
                                                    {{ t.description }}
                                                </td>
                                                <td
                                                    class="px-5 py-3 text-gray-600 text-xs"
                                                >
                                                    {{ t.workshop_name }}
                                                </td>
                                                <td
                                                    class="px-5 py-3 text-right font-semibold text-gray-800"
                                                >
                                                    Rp
                                                    {{
                                                        formatPrice(
                                                            t.repair_cost,
                                                        )
                                                    }}
                                                </td>
                                                <td
                                                    class="px-5 py-3 text-xs text-gray-500 max-w-[200px]"
                                                >
                                                    {{ t.staff_notes || "-" }}
                                                </td>
                                                <td
                                                    class="px-5 py-3 text-xs text-gray-500"
                                                >
                                                    {{
                                                        formatDate(
                                                            t.completed_at,
                                                        )
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- ===================== PAGE: KONDISI KENDARAAN ===================== -->
                    <div v-else-if="activePage === 'cars'" class="space-y-5">
                        <div
                            v-if="loadingCars"
                            class="text-center py-16 text-gray-400"
                        >
                            <svg
                                class="w-8 h-8 mx-auto mb-3 animate-spin text-teal-400"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8z"
                                />
                            </svg>
                            Memuat kondisi kendaraan...
                        </div>
                        <template v-else>
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
                            >
                                <div
                                    v-for="car in allCars"
                                    :key="car.car_id"
                                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
                                >
                                    <div
                                        class="flex items-start justify-between mb-3"
                                    >
                                        <div>
                                            <h4
                                                class="font-bold text-gray-900 text-sm"
                                            >
                                                {{ car.brand }} {{ car.model }}
                                            </h4>
                                            <p class="text-xs text-gray-500">
                                                {{ car.license_plate }} ·
                                                {{ car.car_code }}
                                            </p>
                                        </div>
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                            :class="{
                                                'bg-green-100 text-green-700':
                                                    car.car_status ===
                                                    'Available',
                                                'bg-blue-100 text-blue-700':
                                                    car.car_status === 'Rented',
                                                'bg-orange-100 text-orange-700':
                                                    car.car_status ===
                                                    'Maintenance',
                                            }"
                                        >
                                            {{ car.car_status }}
                                        </span>
                                    </div>
                                    <div
                                        class="space-y-1 text-xs text-gray-600"
                                    >
                                        <p>
                                            <span class="text-gray-400"
                                                >Tahun:</span
                                            >
                                            {{ car.year || "-" }}
                                        </p>
                                        <p>
                                            <span class="text-gray-400"
                                                >Warna:</span
                                            >
                                            {{ car.color || "-" }}
                                        </p>
                                        <p>
                                            <span class="text-gray-400"
                                                >Harga/Hari:</span
                                            >
                                            Rp
                                            {{
                                                formatPrice(
                                                    car.rental_price_per_day,
                                                )
                                            }}
                                        </p>
                                        <p>
                                            <span class="text-gray-400"
                                                >Kondisi:</span
                                            >
                                            {{
                                                car.car_condition ||
                                                "Tidak ada catatan"
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </main>
        </div>

        <!-- Toast notification -->
        <div
            v-if="toast"
            class="fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-lg text-sm font-medium"
            :class="
                toast.type === 'success'
                    ? 'bg-green-600 text-white'
                    : 'bg-red-600 text-white'
            "
        >
            {{ toast.msg }}
        </div>
    </div>
</template>

<script>
export default {
    name: "StaffDashboard",
    props: {
        user: { type: Object, required: true },
    },
    emits: ["logout"],
    data() {
        return {
            activePage: "deliveries",
            sidebarOpen: false,
            toast: null,

            // Maintenance
            loading: false,
            updatingId: null,
            showCompleted: false,
            tasks: [],

            // Deliveries
            loadingDeliveries: false,
            deliveryUpdating: null,
            deliveries: [],
            deliveryFilter: "active",
            deliveryFilters: [
                { val: "active", label: "Aktif" },
                { val: "workshop", label: "🏪 Ke Bengkel" },
                { val: "customer", label: "👤 Ke Pelanggan" },
                { val: "all", label: "Semua" },
            ],

            // Cars
            loadingCars: false,
            allCars: [],

            menuItems: [
                {
                    key: "deliveries",
                    label: "Tugas Pengantaran",
                    sub: "Antar ke bengkel & pelanggan",
                    icon: "🚗",
                    badge: null,
                },
                {
                    key: "tasks",
                    label: "Perawatan & Progress",
                    sub: "Laporan maintenance",
                    icon: "🔧",
                    badge: null,
                },
                {
                    key: "cars",
                    label: "Kondisi Kendaraan",
                    sub: "Lihat semua mobil",
                    icon: "📋",
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
        today() {
            return new Date().toLocaleDateString("id-ID", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            });
        },
        // Maintenance
        pendingTasks() {
            return this.tasks.filter((t) => t.status === "pending");
        },
        inProgressTasks() {
            return this.tasks.filter((t) => t.status === "in_progress");
        },
        completedTasks() {
            return this.tasks.filter((t) => t.status === "completed");
        },
        activeTasks() {
            return this.tasks.filter((t) => t.status !== "completed");
        },
        // Deliveries
        deliveryPending() {
            return this.deliveries.filter((d) => d.status === "pending");
        },
        deliveryOnTheWay() {
            return this.deliveries.filter((d) => d.status === "on_the_way");
        },
        workshopDeliveries() {
            return this.deliveries.filter(
                (d) =>
                    d.type === "workshop_delivery" && d.status !== "delivered",
            );
        },
        customerDeliveries() {
            return this.deliveries.filter(
                (d) =>
                    d.type === "customer_delivery" && d.status !== "delivered",
            );
        },
        filteredDeliveries() {
            if (this.deliveryFilter === "active")
                return this.deliveries.filter((d) => d.status !== "delivered");
            if (this.deliveryFilter === "workshop")
                return this.deliveries.filter(
                    (d) => d.type === "workshop_delivery",
                );
            if (this.deliveryFilter === "customer")
                return this.deliveries.filter(
                    (d) => d.type === "customer_delivery",
                );
            return this.deliveries;
        },
    },
    watch: {
        activePage(val) {
            if (val === "cars" && this.allCars.length === 0) this.fetchCars();
            if (val === "tasks" && this.tasks.length === 0) this.fetchTasks();
            if (val === "deliveries" && this.deliveries.length === 0)
                this.fetchDeliveries();
        },
    },
    mounted() {
        this.fetchDeliveries();
        this.fetchTasks();
    },
    methods: {
        // ---- Delivery Tasks ----
        async fetchDeliveries() {
            this.loadingDeliveries = true;
            try {
                const res = await window.axios.get("/api/staff/my-deliveries");
                this.deliveries = (res.data.tasks || []).map((d) => ({
                    ...d,
                    _report: "",
                }));
                this.updateBadges();
            } catch (e) {
                this.showToast("error", "Gagal memuat tugas pengantaran.");
            } finally {
                this.loadingDeliveries = false;
            }
        },
        async updateDelivery(task, status) {
            this.deliveryUpdating = task.id;
            try {
                await window.axios.patch(
                    `/api/staff/deliveries/${task.id}/status`,
                    {
                        status: status,
                        staff_report: task._report || null,
                    },
                );
                this.showToast(
                    "success",
                    status === "delivered"
                        ? "Pengantaran selesai!"
                        : "Status pengantaran diperbarui.",
                );
                await this.fetchDeliveries();
            } catch (err) {
                this.showToast(
                    "error",
                    err.response?.data?.message ??
                        "Gagal memperbarui status pengantaran.",
                );
            } finally {
                this.deliveryUpdating = null;
            }
        },
        deliveryStatusLabel(status) {
            return (
                {
                    pending: "Menunggu",
                    on_the_way: "Dalam Perjalanan",
                    delivered: "Selesai",
                }[status] || status
            );
        },

        // ---- Maintenance Tasks ----
        async fetchTasks() {
            this.loading = true;
            try {
                const res = await window.axios.get("/api/staff/my-tasks");
                this.tasks = (res.data.records || []).map((t) => ({
                    ...t,
                    _notes: "",
                }));
                this.updateBadges();
            } catch (e) {
                this.showToast("error", "Gagal memuat tugas maintenance.");
            } finally {
                this.loading = false;
            }
        },
        async updateTask(task, status) {
            if (!task._notes?.trim()) return;
            this.updatingId = task.id;
            try {
                await window.axios.patch(
                    `/api/staff/maintenance/${task.id}/progress`,
                    {
                        staff_notes: task._notes,
                        status: status,
                    },
                );
                this.showToast(
                    "success",
                    status === "completed"
                        ? `Maintenance ${task.car?.brand} ${task.car?.model} selesai!`
                        : "Progress perawatan diperbarui.",
                );
                await this.fetchTasks();
            } catch (err) {
                this.showToast(
                    "error",
                    err.response?.data?.message ??
                        "Gagal memperbarui progress.",
                );
            } finally {
                this.updatingId = null;
            }
        },

        // ---- Cars ----
        async fetchCars() {
            this.loadingCars = true;
            try {
                const res = await window.axios.get("/api/cars");
                this.allCars = res.data.cars || [];
            } catch (e) {
                this.showToast("error", "Gagal memuat data kendaraan.");
            } finally {
                this.loadingCars = false;
            }
        },

        // ---- Badges ----
        updateBadges() {
            const dBadge = this.deliveries.filter(
                (d) => d.status !== "delivered",
            ).length;
            const mBadge = this.tasks.filter(
                (t) => t.status !== "completed",
            ).length;
            const dItem = this.menuItems.find((m) => m.key === "deliveries");
            const tItem = this.menuItems.find((m) => m.key === "tasks");
            if (dItem) dItem.badge = dBadge > 0 ? dBadge : null;
            if (tItem) tItem.badge = mBadge > 0 ? mBadge : null;
        },

        // ---- Helpers ----
        formatPrice(val) {
            if (!val) return "0";
            return Number(val).toLocaleString("id-ID");
        },
        formatDate(val) {
            if (!val) return "-";
            return new Date(val).toLocaleDateString("id-ID", {
                day: "2-digit",
                month: "short",
                year: "numeric",
            });
        },
        formatDateTime(val) {
            if (!val) return "-";
            return new Date(val).toLocaleDateString("id-ID", {
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        },
        showToast(type, msg) {
            this.toast = { type, msg };
            setTimeout(() => {
                this.toast = null;
            }, 3000);
        },
    },
};
</script>
