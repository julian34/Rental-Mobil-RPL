<template>
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Popular Destinations</h2>
                    <p class="text-gray-500 mt-1">Best deals in top locations worldwide</p>
                </div>
                <a href="#" class="text-blue-700 text-sm font-medium hover:underline hidden sm:block">View all destinations &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div
                    v-for="dest in destinations"
                    :key="dest.city"
                    class="group relative rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-shadow cursor-pointer"
                >
                    <!-- Card background -->
                    <div
                        class="h-52 w-full transition-transform duration-300 group-hover:scale-105"
                        :style="{ background: dest.gradient }"
                    >
                        <!-- City icon/illustration -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-7xl select-none opacity-80">{{ dest.icon }}</span>
                        </div>
                        <!-- Dark overlay at bottom -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    </div>

                    <!-- Card content -->
                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                        <div class="flex items-end justify-between">
                            <div>
                                <h3 class="text-lg font-bold leading-tight">{{ dest.city }}</h3>
                                <p class="text-xs text-gray-300">{{ dest.country }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-300">from</p>
                                <p class="text-lg font-bold text-yellow-400">{{ dest.price }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-6 sm:hidden">
                <a href="#" class="text-blue-700 text-sm font-medium hover:underline">View all destinations &rarr;</a>
            </div>

        </div>
    </section>
</template>

<script>
export default {
    name: 'PopularDestinations',
    data() {
        return {
            destinations: [
                { city: 'Dubai',         country: 'UAE',            price: '$8/day',  icon: '🏙️',  gradient: 'linear-gradient(135deg, #f59e0b, #d97706)' },
                { city: 'London',        country: 'United Kingdom', price: '$22/day', icon: '🎡',  gradient: 'linear-gradient(135deg, #6366f1, #4338ca)' },
                { city: 'Las Vegas',     country: 'USA',            price: '$15/day', icon: '🎰',  gradient: 'linear-gradient(135deg, #ec4899, #be185d)' },
                { city: 'Bali',          country: 'Indonesia',      price: '$10/day', icon: '🌴',  gradient: 'linear-gradient(135deg, #10b981, #059669)' },
                { city: 'Paris',         country: 'France',         price: '$30/day', icon: '🗼',  gradient: 'linear-gradient(135deg, #3b82f6, #1d4ed8)' },
                { city: 'Tokyo',         country: 'Japan',          price: '$25/day', icon: '⛩️',  gradient: 'linear-gradient(135deg, #ef4444, #b91c1c)' },
                { city: 'New York',      country: 'USA',            price: '$35/day', icon: '🗽',  gradient: 'linear-gradient(135deg, #64748b, #334155)' },
                { city: 'Sydney',        country: 'Australia',      price: '$28/day', icon: '🦘',  gradient: 'linear-gradient(135deg, #0ea5e9, #0369a1)' },
            ],
        };
    },
};
</script>
