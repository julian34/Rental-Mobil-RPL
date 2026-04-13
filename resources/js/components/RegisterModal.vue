<template>
    <!-- Backdrop -->
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 overflow-y-auto py-6"
        @click.self="$emit('close')"
    >
        <!-- Modal card -->
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">

            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-teal-500 px-8 py-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold text-white">Daftar Akun</h2>
                <p class="text-green-100 text-sm mt-1">Buat akun pelanggan baru</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleRegister" class="px-8 py-6 space-y-4">

                <!-- Error alert -->
                <div
                    v-if="errorMessage"
                    class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm"
                >
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ errorMessage }}</span>
                </div>

                <!-- Success alert -->
                <div
                    v-if="successMessage"
                    class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm"
                >
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ successMessage }}
                </div>

                <!-- Row: Nama + Username -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.nama"
                            type="text"
                            placeholder="Nama lengkap"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        />
                        <p v-if="errors.nama" class="text-red-500 text-xs mt-1">{{ errors.nama }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.username"
                            type="text"
                            placeholder="username_anda"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        />
                        <p v-if="errors.username" class="text-red-500 text-xs mt-1">{{ errors.username }}</p>
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                        Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        v-model="form.alamat"
                        rows="2"
                        placeholder="Jl. Contoh No. 1, Kota, Provinsi"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition resize-none"
                    ></textarea>
                    <p v-if="errors.alamat" class="text-red-500 text-xs mt-1">{{ errors.alamat }}</p>
                </div>

                <!-- Row: No. Telepon + No. KTP -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            No. Telepon <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.no_telepon"
                            type="tel"
                            placeholder="08xxxxxxxxxx"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        />
                        <p v-if="errors.no_telepon" class="text-red-500 text-xs mt-1">{{ errors.no_telepon }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            No. KTP <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.no_ktp"
                            type="text"
                            placeholder="16 digit NIK"
                            maxlength="16"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        />
                        <p v-if="errors.no_ktp" class="text-red-500 text-xs mt-1">{{ errors.no_ktp }}</p>
                    </div>
                </div>

                <!-- No. SIM -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                        No. SIM <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.no_sim"
                        type="text"
                        placeholder="Nomor SIM Anda"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    />
                    <p v-if="errors.no_sim" class="text-red-500 text-xs mt-1">{{ errors.no_sim }}</p>
                </div>

                <!-- Row: Password + Confirm Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Min. 8 karakter"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition pr-9"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-gray-600"
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
                        <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.password_confirmation"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Ulangi password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-2">
                    <p class="text-sm text-gray-500">
                        Sudah punya akun?
                        <button type="button" @click="$emit('switch-to-login')" class="text-indigo-600 hover:underline font-medium">
                            Masuk
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
                            class="px-6 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            {{ loading ? 'Mendaftar…' : 'Daftar' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RegisterModal',
    emits: ['close', 'register-success', 'switch-to-login'],
    data() {
        return {
            form: {
                nama: '',
                username: '',
                alamat: '',
                no_telepon: '',
                no_ktp: '',
                no_sim: '',
                password: '',
                password_confirmation: '',
            },
            showPassword: false,
            loading: false,
            errorMessage: '',
            successMessage: '',
            errors: {},
        };
    },
    methods: {
        async handleRegister() {
            this.loading = true;
            this.errorMessage = '';
            this.successMessage = '';
            this.errors = {};

            try {
                const response = await window.axios.post('/api/auth/register-customer', this.form);
                this.successMessage = 'Registrasi berhasil! Mengalihkan…';
                setTimeout(() => {
                    this.$emit('register-success', response.data);
                }, 800);
            } catch (error) {
                if (error.response?.status === 422) {
                    const serverErrors = error.response.data.errors ?? {};
                    this.errors = serverErrors;
                    this.errorMessage = Object.values(serverErrors).flat()[0];
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
