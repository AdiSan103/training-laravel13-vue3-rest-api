<template>
  <!--
    AlertPopup.vue
    --------------
    Komponen untuk menampilkan notifikasi/alert floating di pojok kanan atas.
    Dipasang sekali di App.vue dan akan merender semua alert yang aktif.
  -->
  <div class="alert-container" role="region" aria-label="Notifikasi" aria-live="polite">
    <div
      v-for="alert in alerts"
      :key="alert.id"
      class="alert-item"
      :class="`alert-${alert.type}`"
      role="alert"
    >
      <!-- Ikon sesuai jenis alert -->
      <span class="alert-icon" :class="`alert-${alert.type}`" aria-hidden="true">
        <!-- Success: centang -->
        <svg v-if="alert.type === 'success'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        <!-- Error: silang -->
        <svg v-else-if="alert.type === 'error'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        <!-- Warning: segitiga -->
        <svg v-else-if="alert.type === 'warning'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <!-- Info: huruf i -->
        <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
      </span>

      <!-- Konten alert -->
      <div class="alert-content">
        <p class="alert-title">{{ alert.title }}</p>
        <p v-if="alert.message" class="alert-message">{{ alert.message }}</p>
      </div>

      <!-- Tombol tutup manual -->
      <button
        class="alert-close"
        @click="removeAlert(alert.id)"
        aria-label="Tutup notifikasi"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { useAlert } from '../composables/useAlert.js'

// Ambil state dan fungsi dari composable
const { alerts, removeAlert } = useAlert()
</script>
