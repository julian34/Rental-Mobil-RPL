<template>
    <section class="relative min-h-[540px] flex flex-col items-center justify-center overflow-hidden">

        <!-- Background gradient simulating a road/travel photo -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#0a1a3c] via-[#0d2b5e] to-[#1a4a8a]"></div>
        <!-- Subtle road pattern overlay -->
        <div class="absolute inset-0 opacity-10"
            style="background-image: repeating-linear-gradient(90deg, transparent, transparent 80px, rgba(255,255,255,0.3) 80px, rgba(255,255,255,0.3) 82px);">
        </div>

        <div class="relative z-10 w-full max-w-5xl mx-auto px-4 py-16 text-center">

            <!-- Headline -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-3 leading-tight">
                Safest Place to Rent a Car.<br>
                <span class="text-yellow-400">Low Prices</span> &minus; No Surprises
            </h1>

            <!-- Trust badges -->
            <div class="flex flex-wrap justify-center gap-6 mt-4 mb-8">
                <div class="flex items-center gap-2 text-white text-sm">
                    <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span>8.5 million trusted clients</span>
                </div>
                <div class="flex items-center gap-2 text-white text-sm">
                    <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span>24/7 customer support</span>
                </div>
                <div class="flex items-center gap-2 text-white text-sm">
                    <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span>Free cancellation</span>
                </div>
            </div>

            <!-- Search form card -->
            <div class="bg-white rounded-2xl shadow-2xl p-5 text-left">

                <!-- Tabs -->
                <div class="flex gap-2 mb-5">
                    <button
                        @click="sameLocation = true"
                        :class="sameLocation ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-full text-sm font-medium transition"
                    >
                        Same pick-up &amp; drop-off
                    </button>
                    <button
                        @click="sameLocation = false"
                        :class="!sameLocation ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-full text-sm font-medium transition"
                    >
                        Different drop-off location
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    <!-- Pick-up location -->
                    <div class="lg:col-span-2 relative">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Pick-up Location</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input
                                v-model="form.pickupLocation"
                                type="text"
                                placeholder="City, airport, address..."
                                class="w-full pl-9 pr-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition"
                            />
                        </div>
                    </div>

                    <!-- Drop-off location (if different) -->
                    <div v-if="!sameLocation" class="lg:col-span-2 relative">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Drop-off Location</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input
                                v-model="form.dropoffLocation"
                                type="text"
                                placeholder="City, airport, address..."
                                class="w-full pl-9 pr-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition"
                            />
                        </div>
                    </div>

                    <!-- Pick-up date -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Pick-up Date</label>
                        <input
                            v-model="form.pickupDate"
                            type="date"
                            class="w-full px-3 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition"
                        />
                    </div>

                    <!-- Pick-up time -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Pick-up Time</label>
                        <select
                            v-model="form.pickupTime"
                            class="w-full px-3 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition bg-white"
                        >
                            <option v-for="t in times" :key="t" :value="t">{{ t }}</option>
                        </select>
                    </div>

                    <!-- Drop-off date -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Drop-off Date</label>
                        <input
                            v-model="form.dropoffDate"
                            type="date"
                            class="w-full px-3 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition"
                        />
                    </div>

                    <!-- Drop-off time -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Drop-off Time</label>
                        <select
                            v-model="form.dropoffTime"
                            class="w-full px-3 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition bg-white"
                        >
                            <option v-for="t in times" :key="t" :value="t">{{ t }}</option>
                        </select>
                    </div>

                </div>

                <!-- Second row: driver age + promo + search -->
                <div class="flex flex-col sm:flex-row items-end gap-3 mt-3">
                    <div class="w-full sm:w-48">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Driver Age</label>
                        <select
                            v-model="form.driverAge"
                            class="w-full px-3 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition bg-white"
                        >
                            <option v-for="a in ages" :key="a" :value="a">{{ a }} years</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Promo Code (optional)</label>
                        <input
                            v-model="form.promoCode"
                            type="text"
                            placeholder="Enter promo code"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition"
                        />
                    </div>
                    <button
                        @click="$emit('search', form)"
                        class="w-full sm:w-auto px-10 py-3 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold rounded-xl text-sm transition shadow-lg"
                    >
                        Search
                    </button>
                </div>

            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'HeroSearch',
    emits: ['search'],
    data() {
        const today = new Date().toISOString().split('T')[0];
        const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
        return {
            sameLocation: true,
            form: {
                pickupLocation: '',
                dropoffLocation: '',
                pickupDate: today,
                pickupTime: '10:00',
                dropoffDate: tomorrow,
                dropoffTime: '10:00',
                driverAge: 30,
                promoCode: '',
            },
            times: ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00',
                    '09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00',
                    '18:00','19:00','20:00','21:00','22:00','23:00'],
            ages: Array.from({ length: 50 }, (_, i) => i + 18),
        };
    },
};
</script>
