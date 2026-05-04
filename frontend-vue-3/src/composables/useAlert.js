/**
 * composables/useAlert.js
 * -----------------------
 * Composable untuk menampilkan alert/notifikasi popup.
 *
 * Cara pakai di komponen:
 *   import { useAlert } from '@/composables/useAlert.js'
 *   const { showAlert } = useAlert()
 *   showAlert('success', 'Berhasil!', 'Data telah disimpan.')
 *
 * Composable ini menggunakan state yang di-share (singleton pattern)
 * sehingga alert yang dipicu dari halaman mana pun akan muncul
 * di AlertPopup.vue yang ada di App.vue.
 */

import { ref } from 'vue'

// State di luar fungsi agar di-share di seluruh aplikasi
// (tidak dibuat ulang setiap kali useAlert() dipanggil)
const alerts = ref([])

// Counter untuk membuat ID unik setiap alert
let counter = 0

export function useAlert() {
  /**
   * Menampilkan alert baru.
   *
   * @param {'success'|'error'|'warning'|'info'} type - Jenis alert
   * @param {string} title   - Judul alert
   * @param {string} message - Pesan detail (opsional)
   * @param {number} duration - Durasi tampil dalam ms (default 4000)
   */
  function showAlert(type, title, message = '', duration = 4000) {
    const id = ++counter

    // Tambahkan alert baru ke array
    alerts.value.push({ id, type, title, message })

    // Otomatis hilang setelah durasi tertentu
    if (duration > 0) {
      setTimeout(() => {
        removeAlert(id)
      }, duration)
    }
  }

  /**
   * Menghapus alert berdasarkan ID.
   *
   * @param {number} id
   */
  function removeAlert(id) {
    alerts.value = alerts.value.filter((a) => a.id !== id)
  }

  // Shortcut untuk tiap jenis alert
  const success = (title, message) => showAlert('success', title, message)
  const error   = (title, message) => showAlert('error',   title, message)
  const warning = (title, message) => showAlert('warning', title, message)
  const info    = (title, message) => showAlert('info',    title, message)

  return {
    alerts,       // reactive array of alerts (dipakai oleh AlertPopup.vue)
    showAlert,
    removeAlert,
    success,
    error,
    warning,
    info,
  }
}
