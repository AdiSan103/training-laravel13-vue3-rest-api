/**
 * services/authService.js
 * -----------------------
 * Semua fungsi yang berhubungan dengan autentikasi user.
 * Memanggil fungsi dari api.js agar tidak perlu menulis fetch manual.
 */

import { get, post, postForm, setToken, removeToken } from './api.js'

const authService = {
  /**
   * Login user.
   * Jika berhasil, token disimpan ke localStorage secara otomatis.
   *
   * @param {string} email
   * @param {string} password
   * @returns {{ data, error }}
   */
  async login(email, password) {
    const result = await post('/login', { email, password })

    // Simpan token jika login berhasil
    if (result.data?.data?.token) {
      setToken(result.data.data.token)
    }

    return result
  },

  /**
   * Registrasi user baru.
   *
   * @param {{ name, email, password, password_confirmation }} payload
   * @returns {{ data, error }}
   */
  async register(payload) {
    return await post('/register', payload)
  },

  /**
   * Mendapatkan data user yang sedang login.
   * Membutuhkan token yang valid di header.
   *
   * @returns {{ data, error }}
   */
  async getMe() {
    return await get('/me')
  },

  /**
   * Logout user.
   * Token dihapus dari localStorage setelah berhasil logout.
   *
   * @returns {{ data, error }}
   */
  async logout() {
    const result = await post('/logout')

    // Hapus token terlepas dari hasil API
    // Ini memastikan user selalu ter-logout di sisi client
    removeToken()

    return result
  },

  /**
   * Update profil user (name, password, avatar).
   * Menggunakan FormData karena bisa ada upload file (avatar).
   *
   * @param {{ name?, password?, password_confirmation?, avatar? }} payload
   * @returns {{ data, error }}
   */
  async updateProfile(payload) {
    // Gunakan FormData agar bisa mengirim file
    const formData = new FormData()

    if (payload.name)     formData.append('name', payload.name)
    if (payload.password) {
      formData.append('password', payload.password)
      formData.append('password_confirmation', payload.password_confirmation)
    }
    if (payload.avatar)   formData.append('avatar', payload.avatar)

    return await postForm('/update-profile', formData)
  },
}

export default authService
