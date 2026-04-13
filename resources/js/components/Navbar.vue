<template>
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <div class="flex items-center">
                    <a href="#" class="flex items-center gap-1">
                        <span class="text-2xl font-bold text-blue-700 tracking-tight">economy</span>
                        <span class="text-2xl font-bold text-gray-800 tracking-tight">bookings</span>
                    </a>
                </div>

                <!-- Nav Links (desktop) -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                    <a href="#" class="hover:text-blue-700 transition">Travel Blog</a>
                    <a href="#" class="hover:text-blue-700 transition">Help Center</a>
                    <a href="#" class="hover:text-blue-700 transition">List Your Cars</a>
                </nav>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <template v-if="user">
                        <a href="#" class="hidden sm:flex items-center gap-1 text-sm text-gray-600 hover:text-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            My Booking
                        </a>
                        <div class="hidden sm:flex items-center gap-2 text-sm text-gray-700">
                            <div class="w-8 h-8 rounded-full bg-blue-700 flex items-center justify-center text-white font-bold text-sm">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>
                            <span class="font-medium">{{ user.name }}</span>
                        </div>
                        <button
                            @click="$emit('logout')"
                            class="px-4 py-2 text-sm border border-gray-300 rounded-full text-gray-600 hover:bg-gray-50 transition"
                        >
                            Sign Out
                        </button>
                    </template>
                    <template v-else>
                        <button
                            @click="$emit('open-login')"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 border border-blue-700 rounded-full hover:bg-blue-50 transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Sign In
                        </button>
                    </template>
                </div>

            </div>
        </div>
    </header>
</template>

<script>
export default {
    name: 'Navbar',
    props: {
        user: { type: Object, default: null },
    },
    emits: ['open-login', 'open-register', 'logout'],
};
</script>
