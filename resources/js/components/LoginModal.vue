<template>
    <!-- Backdrop -->
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="$emit('close')"
    >
        <!-- Modal card -->
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-blue-500 px-8 py-6">
                <h2 class="text-2xl font-bold text-white">Selamat datang</h2>
                <p class="text-indigo-100 text-sm mt-1">Masuk ke akun Anda</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleLogin" class="px-8 py-6 space-y-5">

                <!-- Error alert -->
                <div
                    v-if="errorMessage"
                    class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm"
                >
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ errorMessage }}
                </div>

                <!-- Email / Username -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email / Username</label>
                    <input
                        v-model="form.login"
                        type="text"
                        placeholder="email@contoh.com atau username_anda"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    />
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="••••••••"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition pr-10"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-2">
                    <p class="text-sm text-gray-500">
                        Belum punya akun?
                        <button type="button" @click="$emit('switch-to-register')" class="text-indigo-600 hover:underline font-medium">
                            Daftar
                        </button>
                    </p>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            {{ loading ? 'Masuk…' : 'Masuk' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: 'LoginModal',
    emits: ['close', 'login-success', 'switch-to-register'],
    data() {
        return {
            form: {
                login: '',
                password: '',
            },
            showPassword: false,
            loading: false,
            errorMessage: '',
        };
    },
    methods: {
        async handleLogin() {
            this.loading = true;
            this.errorMessage = '';

            try {
                const response = await window.axios.post('/api/auth/login', this.form);
                this.$emit('login-success', response.data);
            } catch (error) {
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
                    this.errorMessage = Object.values(errors).flat()[0];
                } else if (error.response?.status === 401) {
                    this.errorMessage = 'Username/email atau password salah.';
                } else {
                    this.errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                }
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
