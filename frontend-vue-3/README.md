# 📦 Inventory App — Vue 3 + Vite

Aplikasi manajemen inventaris berbasis web yang dibangun dengan **Vue 3** dan **Vite**, terkoneksi ke backend **Laravel + JWT Auth**.

Proyek ini dibuat sebagai **bahan pembelajaran** mahasiswa yang ingin memahami cara membangun aplikasi SPA (Single Page Application) modern secara terstruktur — mulai dari autentikasi, konsumsi REST API, hingga manajemen state komponen.

---

## 🗂️ Daftar Isi

- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Fitur Aplikasi](#-fitur-aplikasi)
- [Struktur Folder](#-struktur-folder)
- [Prasyarat](#-prasyarat)
- [Cara Clone & Install](#-cara-clone--install)
- [Konfigurasi](#-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Panduan Penggunaan](#-panduan-penggunaan)
- [Penjelasan Konsep Penting](#-penjelasan-konsep-penting)
- [Alur Data Aplikasi](#-alur-data-aplikasi)

---

## 🧰 Teknologi yang Digunakan

| Teknologi                                            | Versi  | Keterangan                                          |
| ---------------------------------------------------- | ------ | --------------------------------------------------- |
| [Vue 3](https://vuejs.org/)                          | ^3.4   | Framework JavaScript utama (Composition API)        |
| [Vite](https://vitejs.dev/)                          | ^5.0   | Build tool & development server yang sangat cepat   |
| [Vue Router](https://router.vuejs.org/)              | ^4.3   | Routing SPA (navigasi antar halaman)                |
| Vanilla CSS                                          | —      | Styling tanpa framework CSS (monokrom, shadcn-like) |
| Fetch API                                            | Native | HTTP request ke backend (tanpa Axios)               |
| [Laravel](https://laravel.com/)                      | 11/13  | Backend REST API (terpisah, harus jalan sendiri)    |
| [JWT Auth](https://github.com/tymondesigns/jwt-auth) | —      | Autentikasi berbasis token di sisi backend          |

> **Catatan:** Proyek ini **hanya frontend**. Backend Laravel harus disiapkan dan dijalankan secara terpisah.

---

## ✨ Fitur Aplikasi

| Halaman               | Fitur                                                          |
| --------------------- | -------------------------------------------------------------- |
| **Login**             | Form login, validasi client-side, simpan JWT token             |
| **Register**          | Form registrasi user baru                                      |
| **Dashboard**         | Tabel produk, filter pencarian & stok, statistik, hapus produk |
| **Detail Produk**     | Tampilan lengkap satu produk, tombol edit & hapus              |
| **Tambah Produk**     | Form tambah produk baru dengan upload gambar                   |
| **Edit Produk**       | Form edit produk (1 komponen reuse untuk create & edit)        |
| **Profil**            | Lihat & update nama, password, dan avatar                      |
| **Logout**            | Invalidasi token, redirect ke login                            |
| **Alert Popup**       | Notifikasi aksi (sukses/gagal) di semua halaman                |
| **Konfirmasi Delete** | Modal dialog sebelum menghapus data                            |

---

## 📁 Struktur Folder

```
inventory-app/
│
├── index.html                      # HTML utama — titik masuk aplikasi
├── vite.config.js                  # Konfigurasi Vite
├── package.json                    # Daftar dependencies dan scripts
│
└── src/                            # Semua kode sumber Vue
    │
    ├── main.js                     # Entry point: inisialisasi Vue + Router
    ├── App.vue                     # Komponen root: layout utama aplikasi
    │
    ├── assets/
    │   └── main.css                # CSS global (variabel, reset, komponen)
    │
    ├── services/                   # 📡 Layer komunikasi dengan API
    │   ├── api.js                  # Base fetch wrapper (get, post, postForm, del)
    │   ├── authService.js          # Fungsi login, register, me, logout, updateProfile
    │   └── productService.js       # Fungsi CRUD produk
    │
    ├── composables/                # 🔁 Logika reaktif yang bisa dipakai ulang
    │   ├── useAlert.js             # Manajemen notifikasi popup global
    │   └── useAuth.js              # State user yang sedang login
    │
    ├── router/
    │   └── index.js                # Definisi route + navigation guard (proteksi halaman)
    │
    ├── components/                 # 🧩 Komponen kecil yang dipakai ulang
    │   ├── AppSidebar.vue          # Sidebar navigasi + tombol logout
    │   ├── AlertPopup.vue          # Notifikasi floating (success/error/warning)
    │   └── ConfirmModal.vue        # Dialog konfirmasi sebelum hapus data
    │
    └── views/                      # 📄 Halaman-halaman utama aplikasi
        ├── LoginView.vue           # Halaman login
        ├── RegisterView.vue        # Halaman registrasi
        ├── DashboardView.vue       # Daftar produk + filter + statistik
        ├── ProductDetailView.vue   # Detail satu produk
        ├── ProductFormView.vue     # Form tambah & edit produk (1 file, 2 mode)
        └── ProfileView.vue         # Profil user + form update
```

### Penjelasan Singkat Tiap Folder

#### `src/services/` — Layer API

Berisi semua fungsi yang bertugas **berkomunikasi dengan backend**. Tidak ada `fetch()` yang ditulis langsung di dalam View atau Component — semuanya dipusatkan di sini agar mudah diubah.

- `api.js` → fungsi dasar: `get()`, `post()`, `postForm()`, `del()`, manajemen token
- `authService.js` → semua endpoint `/login`, `/register`, `/me`, `/logout`, `/update-profile`
- `productService.js` → semua endpoint `/products` (CRUD)

#### `src/composables/` — Logika Reaktif Reusable

Composable adalah fungsi yang menggunakan fitur reaktivitas Vue (`ref`, `computed`) dan bisa dipanggil di banyak komponen berbeda.

- `useAlert.js` → menyimpan daftar alert aktif; dipanggil di `AlertPopup.vue` dan di View mana pun
- `useAuth.js` → menyimpan data user yang sedang login; dipanggil di Sidebar, Profile, App.vue

#### `src/router/` — Manajemen Halaman

Mengatur halaman mana yang bisa diakses tanpa login (public) dan mana yang butuh token (protected), menggunakan **navigation guard**.

#### `src/components/` — Komponen Reusable

Komponen kecil yang dipakai lebih dari satu kali atau bersifat global (alert, modal, sidebar).

#### `src/views/` — Halaman Utama

Setiap file mewakili satu halaman. View boleh memanggil composable dan service, lalu hasilnya ditampilkan ke template.

---

## 🛠️ Prasyarat

Pastikan software berikut sudah terpasang di komputer Anda sebelum memulai.

### Wajib

| Software    | Versi Minimum | Cara Cek         |
| ----------- | ------------- | ---------------- |
| **Node.js** | v18 ke atas   | `node --version` |
| **npm**     | v8 ke atas    | `npm --version`  |
| **Git**     | —             | `git --version`  |

> Download Node.js di [https://nodejs.org](https://nodejs.org) — pilih versi **LTS**.

### Backend

Backend Laravel harus sudah berjalan di `http://127.0.0.1:8000` dengan konfigurasi:

- JWT Auth terpasang dan dikonfigurasi
- CORS diizinkan untuk `http://localhost:5173`
- Migration dan seeder sudah dijalankan

---

## 🚀 Cara Clone & Install

Ikuti langkah-langkah berikut secara berurutan.

### Langkah 1 — Clone Repository

```bash
git clone https://github.com/username/inventory-app.git
```

Masuk ke folder proyek:

```bash
cd inventory-app
```

### Langkah 2 — Install Dependencies

Perintah ini akan mengunduh semua package yang dibutuhkan (Vue, Vue Router, Vite) ke folder `node_modules/`.

```bash
npm install
```

> Proses ini membutuhkan koneksi internet. Tunggu hingga selesai.

### Langkah 3 — Selesai

Tidak ada langkah tambahan. Tidak perlu file `.env` karena URL API sudah dikonfigurasi langsung di `src/services/api.js`.

---

## ⚙️ Konfigurasi

### Mengubah URL API Backend

Jika backend berjalan di port atau host yang berbeda, buka file berikut:

```
src/services/api.js
```

Ubah nilai `BASE_URL` di baris paling atas:

```javascript
// Sebelum (default)
const BASE_URL = "http://127.0.0.1:8000/api";

// Jika backend di port berbeda
const BASE_URL = "http://localhost:8080/api";
```

### Mengubah URL Gambar

Jika URL gambar produk dan avatar berubah, buka `src/services/productService.js`:

```javascript
// Gambar produk
export function getProductImageUrl(imageName) {
  return `http://127.0.0.1:8000/uploads/products/${imageName}`;
}

// Avatar user
export function getAvatarUrl(imageName) {
  return `http://127.0.0.1:8000/uploads/avatars/${imageName}`;
}
```

---

## ▶️ Menjalankan Aplikasi

### Mode Development (untuk belajar & coding)

```bash
npm run dev
```

Setelah berhasil, terminal akan menampilkan:

```
  VITE v5.x.x  ready in xxx ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
```

Buka browser dan akses **http://localhost:5173**

> Vite mendukung **Hot Module Replacement (HMR)**: setiap perubahan kode langsung terlihat di browser tanpa perlu refresh manual.

### Mode Production (untuk deploy)

```bash
npm run build
```

Hasil build ada di folder `dist/`. Isi folder ini yang di-upload ke server.

Untuk preview hasil build di lokal:

```bash
npm run preview
```

---

## 📖 Panduan Penggunaan

### 1. Registrasi Akun Baru

1. Buka `http://localhost:5173`
2. Anda akan otomatis diarahkan ke halaman **Login**
3. Klik link **"Daftar sekarang"** di bawah form
4. Isi formulir: Nama, Email, Password, Konfirmasi Password
5. Klik tombol **"Daftar"**
6. Jika berhasil, Anda akan diarahkan ke halaman Login dengan notifikasi sukses

### 2. Login

1. Masukkan **Email** dan **Password** yang sudah didaftarkan
2. Klik tombol **"Masuk"**
3. Jika berhasil, Anda akan masuk ke **Dashboard**

> JWT Token akan otomatis disimpan di `localStorage` browser Anda dan dipakai di setiap request berikutnya.

### 3. Dashboard — Melihat Daftar Produk

Setelah login, Anda akan melihat:

- **Statistik**: Total Produk, Total Stok, Stok Habis, Stok Menipis
- **Tabel Produk**: semua produk yang tersedia

**Filter Produk (statis/client-side):**

- Ketik di kolom **"Cari produk..."** untuk filter berdasarkan nama atau deskripsi
- Gunakan dropdown **"Semua Stok"** untuk filter berdasarkan status stok:
  - _Tersedia_ → stok > 5
  - _Menipis_ → stok 1–5
  - _Habis_ → stok 0

### 4. Menambah Produk Baru

1. Klik tombol **"Tambah Produk"** di Dashboard atau menu sidebar
2. Isi form: Nama (wajib), Deskripsi, Harga (wajib), Stok (wajib)
3. Upload gambar produk (opsional, maks. 2MB, format JPG/PNG)
4. Klik **"Tambah Produk"**
5. Setelah berhasil, Anda akan kembali ke Dashboard

### 5. Melihat Detail Produk

1. Di tabel Dashboard, klik ikon 👁️ (mata) pada baris produk
2. Halaman detail menampilkan gambar, harga, stok, deskripsi, dan tanggal

### 6. Mengedit Produk

1. Di tabel Dashboard atau halaman detail, klik ikon ✏️ (pensil)
2. Form akan terisi otomatis dengan data produk yang ada
3. Ubah field yang diinginkan
4. Upload gambar baru jika perlu (gambar lama akan tergantikan)
5. Klik **"Simpan Perubahan"**

### 7. Menghapus Produk

1. Di tabel Dashboard atau halaman detail, klik ikon 🗑️ (tempat sampah)
2. Dialog konfirmasi akan muncul
3. Klik **"Hapus"** untuk konfirmasi, atau **"Batal"** untuk membatalkan
4. Produk akan dihapus beserta gambarnya dari server

> ⚠️ Penghapusan **tidak dapat dibatalkan**. Pastikan Anda yakin sebelum mengkonfirmasi.

### 8. Update Profil

1. Klik menu **"Profil"** di sidebar
2. Anda dapat mengubah:
   - **Nama**: isi field nama dengan nama baru
   - **Password**: isi field password baru (min. 6 karakter) + konfirmasi
   - **Avatar**: upload foto profil baru (JPG/PNG, maks. 2MB)
3. Field yang **dikosongkan tidak akan diubah**
4. Klik **"Simpan Perubahan"**

### 9. Logout

1. Klik tombol **"Keluar"** di bagian bawah sidebar
2. Token akan diinvalidasi di server
3. Anda akan diarahkan kembali ke halaman Login

---

## 💡 Penjelasan Konsep Penting

Bagian ini menjelaskan konsep yang dipakai dalam kode agar lebih mudah dipelajari.

### Composition API vs Options API

Proyek ini menggunakan **Composition API** (`<script setup>`), cara penulisan Vue 3 yang lebih modern:

```vue
<!-- Composition API (dipakai di proyek ini) -->
<script setup>
import { ref, onMounted } from "vue";

const nama = ref(""); // variabel reaktif
onMounted(() => {
  // lifecycle hook
  console.log("Komponen siap");
});
</script>
```

### `ref()` dan `reactive()`

Kedua fungsi ini membuat data menjadi **reaktif** — artinya saat nilainya berubah, tampilan di template otomatis ikut berubah.

```javascript
import { ref, reactive } from "vue";

// ref: untuk satu nilai (string, number, boolean)
const isLoading = ref(false);
isLoading.value = true; // akses via .value

// reactive: untuk object dengan banyak field
const form = reactive({ nama: "", email: "" });
form.nama = "John"; // akses langsung tanpa .value
```

### Composable (`useAlert`, `useAuth`)

Composable adalah fungsi yang membungkus logika reaktif agar bisa digunakan di banyak komponen:

```javascript
// Di composable (useAlert.js)
const alerts = ref([])
export function useAlert() {
  function showAlert(type, title) { ... }
  return { alerts, showAlert }
}

// Di komponen mana pun
import { useAlert } from '../composables/useAlert.js'
const { showAlert } = useAlert()
showAlert('success', 'Berhasil!')
```

### Navigation Guard (Proteksi Halaman)

Route guard mencegah user yang belum login mengakses halaman protected:

```javascript
router.beforeEach((to, from, next) => {
  const isLoggedIn = !!getToken();

  if (to.meta.requiresAuth && !isLoggedIn) {
    next("/login"); // paksa ke login
  } else {
    next(); // lanjut ke halaman tujuan
  }
});
```

### Format Response API

Semua response dari fungsi di `services/` menggunakan format `{ data, error }`:

```javascript
// Cara konsumsi di View
const { data, error } = await productService.getAll();

if (error) {
  // tampilkan pesan error
} else {
  products.value = data.data;
}
```

---

## 🔄 Alur Data Aplikasi

```
User melakukan aksi (klik tombol)
         │
         ▼
    View (.vue)         ← menampilkan data, menangani event
         │
         ▼
    Service (.js)       ← memanggil API, mengembalikan { data, error }
         │
         ▼
    api.js (fetch)      ← menambahkan token, mengirim request HTTP
         │
         ▼
  Laravel Backend       ← memproses request, query database
         │
         ▼
  Response JSON         ← { status, message, data }
         │
         ▼
    api.js              ← parse response, tangani error
         │
         ▼
    Service             ← kembalikan { data, error } ke View
         │
         ▼
    View                ← update state → template auto re-render
         │
         ▼
  useAlert.js           ← tampilkan notifikasi popup
```

---

## 🧑‍💻 Tips untuk Mahasiswa

1. **Mulai dari `main.js`** — pahami urutan inisialisasi aplikasi
2. **Baca `router/index.js`** — pahami route mana yang protected dan mana yang public
3. **Pahami `services/api.js`** — ini adalah jantung komunikasi dengan backend
4. **Coba modifikasi `main.css`** — ubah variabel CSS untuk melihat efeknya pada seluruh tampilan
5. **Gunakan Vue DevTools** — extension browser untuk inspect state dan component tree secara real-time
   - Download: [Chrome](https://chrome.google.com/webstore/detail/vuejs-devtools) | [Firefox](https://addons.mozilla.org/en-US/firefox/addon/vue-js-devtools/)

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan **edukasi**. Bebas digunakan dan dimodifikasi untuk pembelajaran.
