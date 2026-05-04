/**
 * services/api.js
 * ---------------
 * Base layer untuk semua request ke Laravel API.
 *
 * Fungsi utama:
 * - Menyimpan BASE_URL agar mudah diganti
 * - Otomatis menambahkan header Authorization dari localStorage
 * - Mengembalikan format { data, error } yang konsisten
 *   sehingga setiap pemanggilan bisa langsung cek error-nya
 */

// URL dasar API Laravel — sesuaikan jika port berbeda, alangkah baiknya jika di simpan di .env, namun untuk demo ini kita hardcode saja
const BASE_URL = "http://127.0.0.1:8000/api";

/**
 * Mengambil JWT token dari localStorage.
 * Token disimpan saat login berhasil.
 *
 * @returns {string|null} token atau null jika belum login
 */
export function getToken() {
  return localStorage.getItem("token");
}

/**
 * Menyimpan token ke localStorage setelah login berhasil.
 *
 * @param {string} token - JWT token dari response login
 */
export function setToken(token) {
  localStorage.setItem("token", token);
}

/**
 * Menghapus token dari localStorage saat logout.
 */
export function removeToken() {
  localStorage.removeItem("token");
}

/**
 * Fungsi utama untuk melakukan HTTP request ke API.
 *
 * Mengembalikan object { data, error }:
 * - Jika berhasil: { data: responseData, error: null }
 * - Jika gagal:    { data: null, error: pesanError }
 *
 * @param {string} endpoint   - Path endpoint, contoh: '/login'
 * @param {Object} options    - Opsi fetch (method, body, dll)
 * @param {boolean} isFormData - Jika true, jangan set Content-Type (biarkan browser atur boundary multipart)
 */
export async function request(endpoint, options = {}, isFormData = false) {
  const token = getToken();

  // Header dasar — selalu pakai Accept: application/json
  // agar Laravel selalu return JSON, bukan HTML redirect
  const headers = {
    Accept: "application/json",
    ...(!isFormData && { "Content-Type": "application/json" }),
    ...(token && { Authorization: `Bearer ${token}` }),
    ...options.headers,
  };

  try {
    const response = await fetch(`${BASE_URL}${endpoint}`, {
      ...options,
      headers,
    });

    // Coba parse response sebagai JSON
    const json = await response.json();

    // Jika status HTTP bukan 2xx, anggap error
    if (!response.ok) {
      // Laravel validation error (422) mengembalikan { errors: {...} }
      // Ambil pesan pertama dari errors jika ada
      if (json.errors) {
        const firstError = Object.values(json.errors)[0];
        return {
          data: null,
          error: Array.isArray(firstError) ? firstError[0] : firstError,
        };
      }

      // Error lain: gunakan field message atau error dari response
      return {
        data: null,
        error: json.message || json.error || "Terjadi kesalahan",
      };
    }

    return { data: json, error: null };
  } catch (err) {
    // Error jaringan (server mati, tidak ada koneksi, dll)
    console.error("Network error:", err);
    return {
      data: null,
      error: "Tidak dapat terhubung ke server. Cek koneksi internet.",
    };
  }
}

/**
 * Shortcut untuk GET request
 */
export function get(endpoint) {
  return request(endpoint, { method: "GET" });
}

/**
 * Shortcut untuk POST request dengan JSON body
 */
export function post(endpoint, body) {
  return request(endpoint, {
    method: "POST",
    body: JSON.stringify(body),
  });
}

/**
 * Shortcut untuk POST request dengan FormData (multipart/form-data)
 * Dipakai untuk upload file (gambar produk, avatar, dll)
 *
 * @param {string} endpoint
 * @param {FormData} formData
 */
export function postForm(endpoint, formData) {
  return request(
    endpoint,
    {
      method: "POST",
      body: formData,
    },
    true,
  ); // isFormData = true → tidak set Content-Type manual
}

/**
 * Shortcut untuk DELETE request
 */
export function del(endpoint) {
  return request(endpoint, { method: "DELETE" });
}
