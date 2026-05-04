<template>
  <!--
    RegisterView.vue
    ----------------
    Halaman registrasi user baru.
    Setelah berhasil, user diarahkan ke halaman login.
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

      <h1 class="auth-title">Buat akun baru</h1>
      <p class="auth-subtitle">Isi form di bawah untuk mendaftar</p>

      <!-- Form registrasi -->
      <form class="auth-form" @submit.prevent="handleRegister" novalidate>

        <div class="form-group">
          <label for="name" class="form-label required">Nama Lengkap</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="form-input"
            placeholder="John Doe"
            autocomplete="name"
            required
          />
        </div>

        <div class="form-group">
          <label for="email" class="form-label required">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-input"
            placeholder="john@mail.com"
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
            placeholder="Min. 6 karakter"
            autocomplete="new-password"
            required
          />
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label required">Konfirmasi Password</label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            class="form-input"
            placeholder="Ulangi password"
            autocomplete="new-password"
            required
          />
        </div>

        <!-- Pesan error dari API -->
        <p v-if="errorMsg" class="form-error" role="alert">{{ errorMsg }}</p>

        <button
          type="submit"
          class="btn btn-primary w-full"
          :disabled="isLoading"
          style="margin-top:0.25rem;"
        >
          <span v-if="isLoading" class="spinner" style="width:14px;height:14px;border-width:2px;border-color:rgba(255,255,255,0.3);border-top-color:white;"></span>
          {{ isLoading ? 'Mendaftar...' : 'Daftar' }}
        </button>
      </form>

      <p class="auth-divider">
        Sudah punya akun?
        <RouterLink to="/login">Masuk di sini</RouterLink>
      </p>
    </div>
  </main>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import authService from '../services/authService.js'
import { useAlert } from '../composables/useAlert.js'

const router = useRouter()
const { success } = useAlert()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const isLoading = ref(false)
const errorMsg  = ref('')

/**
 * Proses registrasi:
 * 1. Validasi form di sisi client
 * 2. Panggil authService.register()
 * 3. Jika berhasil → tampilkan alert → redirect ke login
 * 4. Jika gagal → tampilkan error
 */
async function handleRegister() {
  errorMsg.value = ''

  // Validasi sederhana sebelum kirim ke API
  if (!form.name || !form.email || !form.password) {
    errorMsg.value = 'Semua field wajib diisi.'
    return
  }

  if (form.password !== form.password_confirmation) {
    errorMsg.value = 'Password dan konfirmasi password tidak sama.'
    return
  }

  if (form.password.length < 6) {
    errorMsg.value = 'Password minimal 6 karakter.'
    return
  }

  isLoading.value = true

  const { error } = await authService.register(form)

  if (error) {
    errorMsg.value = error
  } else {
    success('Registrasi berhasil!', 'Silakan masuk dengan akun baru Anda.')
    router.push('/login')
  }

  isLoading.value = false
}
</script>
