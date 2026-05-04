<template>
  <!--
    App.vue
    -------
    Komponen root yang membungkus seluruh aplikasi.

    Struktur:
    - Jika user sudah login → tampilkan sidebar + konten
    - Jika belum login → tampilkan halaman auth saja (tanpa sidebar)
    - AlertPopup selalu ada di semua halaman
  -->

  <!-- Alert popup global (muncul di pojok kanan atas) -->
  <AlertPopup />

  <!-- Layout dengan sidebar (halaman protected) -->
  <div v-if="isLoggedIn" class="app-layout">
    <!-- Tombol hamburger untuk mobile -->
    <button
      class="hamburger-btn"
      @click="sidebarRef?.toggle()"
      aria-label="Buka menu navigasi"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <line x1="3" y1="6" x2="21" y2="6" />
        <line x1="3" y1="12" x2="21" y2="12" />
        <line x1="3" y1="18" x2="21" y2="18" />
      </svg>
    </button>

    <!-- Sidebar navigasi -->
    <AppSidebar ref="sidebarRef" />

    <!-- Konten halaman -->
    <main class="app-main">
      <!-- RouterView merender komponen sesuai route aktif -->
      <RouterView />
    </main>
  </div>

  <!-- Layout tanpa sidebar (halaman auth: login, register) -->
  <RouterView v-else />
</template>

<script setup>
import { ref, onMounted } from "vue";
import AppSidebar from "./components/AppSidebar.vue";
import AlertPopup from "./components/AlertPopup.vue";
import { useAuth } from "./composables/useAuth.js";

const { isLoggedIn, fetchCurrentUser } = useAuth();

// Ref ke komponen AppSidebar untuk memanggil toggle() dari sini
const sidebarRef = ref(null);

/**
 * Saat aplikasi pertama kali dibuka:
 * Jika ada token di localStorage → ambil data user dari API.
 * Ini memastikan state user terisi meski user refresh halaman.
 */
onMounted(() => {
  fetchCurrentUser();
});
</script>

<style>
/* Tombol hamburger — hanya tampil di mobile */
.hamburger-btn {
  display: none;
  position: fixed;
  top: 0.75rem;
  left: 0.75rem;
  z-index: 200;
  background-color: var(--background);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 0.5rem;
  box-shadow: var(--shadow-sm);
  cursor: pointer;
}

@media (max-width: 768px) {
  .hamburger-btn {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Beri padding atas pada konten agar tidak tertutup tombol hamburger */
  .app-main .page-content {
    padding-top: 3.5rem;
  }
}
</style>
