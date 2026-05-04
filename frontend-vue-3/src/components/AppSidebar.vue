<template>
  <!--
    AppSidebar.vue
    --------------
    Komponen sidebar navigasi yang tampil di semua halaman protected.
    Di mobile, sidebar bisa dibuka/tutup via toggle button.
  -->

  <!-- Overlay gelap di belakang sidebar (hanya di mobile) -->
  <div
    class="sidebar-overlay"
    :class="{ show: isOpen }"
    @click="closeSidebar"
    aria-hidden="true"
  ></div>

  <!-- Sidebar utama -->
  <aside class="sidebar" :class="{ open: isOpen }" aria-label="Navigasi utama">

    <!-- Logo / Brand -->
    <div class="sidebar-logo">
      <div class="sidebar-logo-icon" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 8h14M5 8a2 2 0 1 0-4 0v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8m-4-4v4M9 4v4"/>
        </svg>
      </div>
      <span class="sidebar-logo-text">Inventory</span>
    </div>

    <!-- Menu navigasi -->
    <nav class="sidebar-nav" aria-label="Menu utama">
      <span class="sidebar-section-label">Menu</span>

      <!-- router-link akan otomatis mendapat class 'active' jika route cocok -->
      <RouterLink
        to="/dashboard"
        class="sidebar-link"
        active-class="active"
        @click="closeSidebar"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
        </svg>
        Dashboard
      </RouterLink>

      <RouterLink
        to="/products/create"
        class="sidebar-link"
        active-class="active"
        @click="closeSidebar"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 5v14m-7-7h14"/>
        </svg>
        Tambah Produk
      </RouterLink>

      <span class="sidebar-section-label">Akun</span>

      <RouterLink
        to="/profile"
        class="sidebar-link"
        active-class="active"
        @click="closeSidebar"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/>
        </svg>
        Profil
      </RouterLink>
    </nav>

    <!-- Tombol logout di bawah -->
    <div class="sidebar-footer">
      <!-- Tampilkan info user yang login -->
      <div v-if="currentUser" class="sidebar-user">
        <div class="sidebar-user-info">
          <p class="sidebar-user-name">{{ currentUser.name }}</p>
          <p class="sidebar-user-email">{{ currentUser.email }}</p>
        </div>
      </div>

      <button
        class="sidebar-logout-btn"
        @click="handleLogout"
        :disabled="isLoggingOut"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        {{ isLoggingOut ? 'Keluar...' : 'Keluar' }}
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth.js'
import { useAlert } from '../composables/useAlert.js'
import authService from '../services/authService.js'

const router = useRouter()
const { currentUser, clearCurrentUser } = useAuth()
const { success, error } = useAlert()

// State loading tombol logout
const isLoggingOut = ref(false)

// State sidebar mobile (open/close)
const isOpen = ref(false)

// Expose fungsi toggle agar bisa dipanggil dari parent (App.vue)
defineExpose({ toggle: () => { isOpen.value = !isOpen.value } })

function closeSidebar() {
  isOpen.value = false
}

/**
 * Proses logout:
 * 1. Panggil API logout
 * 2. Hapus state user
 * 3. Redirect ke halaman login
 */
async function handleLogout() {
  isLoggingOut.value = true

  const { error: err } = await authService.logout()

  if (err) {
    error('Gagal logout', err)
  } else {
    clearCurrentUser()
    success('Berhasil keluar', 'Sampai jumpa!')
    router.push('/login')
  }

  isLoggingOut.value = false
}
</script>

<style scoped>
/* Info user di footer sidebar */
.sidebar-user {
  padding: 0.75rem;
  margin-bottom: 0.25rem;
  border-radius: var(--radius-sm);
  background-color: var(--muted);
}

.sidebar-user-name {
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--foreground);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sidebar-user-email {
  font-size: 0.75rem;
  color: var(--muted-foreground);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
