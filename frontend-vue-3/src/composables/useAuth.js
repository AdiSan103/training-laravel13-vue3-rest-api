/**
 * composables/useAuth.js
 * ----------------------
 * Composable untuk mengelola state autentikasi user.
 *
 * Menyediakan:
 * - isLoggedIn: apakah user sedang login
 * - currentUser: data user yang sedang login
 * - Fungsi untuk fetch/update data user
 */

import { ref, computed } from 'vue'
import { getToken } from '../services/api.js'
import authService from '../services/authService.js'

// State di-share antar komponen (singleton)
const currentUser = ref(null)

export function useAuth() {
  /**
   * Computed: true jika token ada di localStorage
   * Dipakai untuk navigation guard di router
   */
  const isLoggedIn = computed(() => !!getToken())

  /**
   * Ambil data user dari API dan simpan ke state.
   * Dipanggil saat App.vue pertama kali load.
   */
  async function fetchCurrentUser() {
    if (!getToken()) return

    const { data, error } = await authService.getMe()
    if (!error && data?.data) {
      currentUser.value = data.data
    }
  }

  /**
   * Set data user secara langsung (setelah update profil misalnya)
   *
   * @param {Object} user
   */
  function setCurrentUser(user) {
    currentUser.value = user
  }

  /**
   * Reset state user (dipanggil saat logout)
   */
  function clearCurrentUser() {
    currentUser.value = null
  }

  return {
    currentUser,
    isLoggedIn,
    fetchCurrentUser,
    setCurrentUser,
    clearCurrentUser,
  }
}
