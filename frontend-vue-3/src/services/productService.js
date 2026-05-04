/**
 * services/productService.js
 * --------------------------
 * Semua fungsi untuk CRUD produk.
 * Memanggil endpoint /api/products dari Laravel.
 */

import { get, postForm, del } from './api.js'

/**
 * Helper: Build URL gambar produk dari nama file.
 * Gambar disimpan di public/uploads/products/ di Laravel.
 *
 * @param {string|null} imageName - Nama file gambar
 * @returns {string|null} URL lengkap gambar atau null
 */
export function getProductImageUrl(imageName) {
  if (!imageName) return null
  return `http://127.0.0.1:8000/uploads/products/${imageName}`
}

/**
 * Helper: Build URL avatar user dari nama file.
 *
 * @param {string|null} imageName
 * @returns {string|null}
 */
export function getAvatarUrl(imageName) {
  if (!imageName) return null
  return `http://127.0.0.1:8000/uploads/avatars/${imageName}`
}

const productService = {
  /**
   * Mengambil semua produk.
   *
   * @returns {{ data, error }}
   */
  async getAll() {
    return await get('/products')
  },

  /**
   * Mengambil detail satu produk berdasarkan ID.
   *
   * @param {number|string} id - ID produk
   * @returns {{ data, error }}
   */
  async getOne(id) {
    return await get(`/products/${id}`)
  },

  /**
   * Membuat produk baru.
   * Menggunakan FormData karena ada upload gambar (opsional).
   *
   * @param {{ name, description?, price, stock, image? }} payload
   * @returns {{ data, error }}
   */
  async create(payload) {
    const formData = new FormData()

    formData.append('name', payload.name)
    formData.append('price', payload.price)
    formData.append('stock', payload.stock)

    if (payload.description) formData.append('description', payload.description)
    if (payload.image)       formData.append('image', payload.image)

    return await postForm('/products', formData)
  },

  /**
   * Mengupdate produk yang sudah ada.
   * Laravel tidak support file via PUT, jadi kita pakai POST + _method=PUT.
   *
   * @param {number|string} id
   * @param {{ name?, description?, price?, stock?, image? }} payload
   * @returns {{ data, error }}
   */
  async update(id, payload) {
    const formData = new FormData()

    // Method spoofing: kirim POST tapi Laravel baca sebagai PUT
    formData.append('_method', 'PUT')

    if (payload.name)        formData.append('name', payload.name)
    if (payload.description !== undefined) formData.append('description', payload.description)
    if (payload.price)       formData.append('price', payload.price)
    if (payload.stock)       formData.append('stock', payload.stock)
    if (payload.image)       formData.append('image', payload.image)

    return await postForm(`/products/${id}`, formData)
  },

  /**
   * Menghapus produk berdasarkan ID.
   *
   * @param {number|string} id
   * @returns {{ data, error }}
   */
  async remove(id) {
    return await del(`/products/${id}`)
  },
}

export default productService
