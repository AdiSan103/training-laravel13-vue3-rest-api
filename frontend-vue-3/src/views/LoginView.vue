<template>
  <!--
    LoginView.vue
    -------------
    Halaman login. Menampilkan form email & password.
    Setelah berhasil login, user diarahkan ke dashboard.
  -->
  <main class="auth-page">
    <div class="auth-card">
      <!-- Logo / Brand -->
      <div class="auth-logo">
        <div class="auth-logo-icon" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 8h14M5 8a2 2 0 1 0-4 0v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8m-4-4v4M9 4v4"/>
          </svg>
        </div>
        <span style="font-weight:600;font-size:1rem;">Inventory</span>
      </div>

      <h1 class="auth-title">Masuk ke akun</h1>
      <p class="auth-subtitle">Masukkan email dan password Anda</p>

      <!-- Form login -->
      <form class="auth-form" @submit.prevent="handleLogin" novalidate>

        <div class="form-group">
          <label for="email" class="form-label required">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-input"
            placeholder="admin@mail.com"
            autocomplete="email"
            required
          />
        </div>

        <div class="form-group">
          <label for="password" class="form-label required">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="form-input"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          />
        </div>

        <!-- Pesan error dari API -->
        <p v-if="errorMsg" class="form-error" role="alert">{{ errorMsg }}</p>

        <!-- Tombol submit -->
        <button
          type="submit"
          class="btn btn-primary w-full"
          :disabled="isLoading"
          style="margin-top:0.25rem;"
        >
          <span v-if="isLoading" class="spinner" style="width:14px;height:14px;border-width:2px;border-color:rgba(255,255,255,0.3);border-top-color:white;"></span>
          {{ isLoading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>

      <!-- Link ke halaman register -->
      <p class="auth-divider">
        Belum punya akun?
        <RouterLink to="/register">Daftar sekarang</RouterLink>
      </p>
    </div>
  </main>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import authService from '../services/authService.js'
import { useAuth } from '../composables/useAuth.js'
import { useAlert } from '../composables/useAlert.js'

const router = useRouter()
const { fetchCurrentUser } = useAuth()
const { success } = useAlert()

// State form — reactive membuat semua property reaktif sekaligus
const form = reactive({
  email: '',
  password: '',
})

const isLoading = ref(false)
const errorMsg  = ref('')

/**
 * Proses login:
 * 1. Validasi form sederhana
 * 2. Panggil authService.login()
 * 3. Jika berhasil → fetch data user → redirect ke dashboard
 * 4. Jika gagal → tampilkan pesan error
 */
async function handleLogin() {
  // Reset error sebelum coba lagi
  errorMsg.value = ''

  // Validasi sederhana sebelum kirim ke API
  if (!form.email || !form.password) {
    errorMsg.value = 'Email dan password wajib diisi.'
    return
  }

  isLoading.value = true

  const { data, error } = await authService.login(form.email, form.password)

  if (error) {
    errorMsg.value = error
  } else {
    // Ambil data user setelah login berhasil
    await fetchCurrentUser()
    success('Selamat datang!', `Login sebagai ${data.data.user.name}`)
    router.push('/dashboard')
  }

  isLoading.value = false
}
</script>
