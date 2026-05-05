# 📦 Inventory App — Project Starter Kit

> Panduan umum sebelum memulai pengembangan. Baca dokumen ini terlebih dahulu sebelum menyentuh kode.

Proyek ini adalah aplikasi manajemen inventaris berbasis web yang terdiri dari dua bagian terpisah:

| Bagian       | Teknologi             | Port                    |
| ------------ | --------------------- | ----------------------- |
| **Backend**  | Laravel 13 + JWT Auth | `http://127.0.0.1:8000` |
| **Frontend** | Vue 3 + Vite          | `http://localhost:5173` |

Keduanya berkomunikasi melalui **REST API** — backend menyediakan endpoint, frontend mengonsumsinya menggunakan `fetch()`.

\*\* Anda bisa dapatkan starter template untuk postman (.json) dan database (.sql) pada folder .schema \*\*

## 🗂️ Daftar Isi

- [Gambaran Arsitektur](#-gambaran-arsitektur)
- [Skema Database](#-skema-database)
- [Packages yang Digunakan](#-packages-yang-digunakan)
- [Standar Penulisan Kode](#-standar-penulisan-kode)
- [Standar REST API — Laravel](#-standar-rest-api--laravel)
- [Standar Struktur Folder — Vue 3](#-standar-struktur-folder--vue-3)
- [Cara Memulai](#-cara-memulai)

---

## 🏗️ Gambaran Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                        FRONTEND                             │
│                    Vue 3 + Vite                             │
│                  localhost:5173                             │
│                                                             │
│  views/       → Halaman (Login, Dashboard, Produk, Profil) │
│  components/  → Komponen kecil reusable (Sidebar, Modal)   │
│  composables/ → Logika reaktif shared (useAlert, useAuth)  │
│  services/    → Komunikasi dengan API (fetch wrapper)      │
│  router/      → Navigasi + proteksi halaman                │
└───────────────────────────┬─────────────────────────────────┘
                            │ HTTP Request
                            │ JSON Response
                            │ Bearer Token (JWT)
┌───────────────────────────▼─────────────────────────────────┐
│                        BACKEND                              │
│                  Laravel 13 + JWT Auth                      │
│                  127.0.0.1:8000                             │
│                                                             │
│  routes/api.php     → Definisi semua endpoint              │
│  Controllers/       → Logika bisnis + response             │
│  Models/            → Representasi tabel database          │
│  public/uploads/    → Penyimpanan file gambar              │
└───────────────────────────┬─────────────────────────────────┘
                            │ Query
┌───────────────────────────▼─────────────────────────────────┐
│                       DATABASE                              │
│                    MySQL 8.x                                │
│                                                             │
│  users     → Data akun user                                │
│  products  → Data produk inventaris                        │
└─────────────────────────────────────────────────────────────┘
```

---

## 🗃️ Skema Database

### Tabel `users`

Tabel bawaan Laravel dengan tambahan satu kolom `avatar`.

```sql
CREATE TABLE users (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name              VARCHAR(255)    NOT NULL,
    email             VARCHAR(255)    NOT NULL UNIQUE,
    email_verified_at TIMESTAMP       NULL,
    password          VARCHAR(255)    NOT NULL,
    avatar            VARCHAR(255)    NULL,       -- nama file foto profil
    remember_token    VARCHAR(100)    NULL,
    created_at        TIMESTAMP       NULL,
    updated_at        TIMESTAMP       NULL
);
```

```php
// Migration Laravel
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('avatar')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

---

### Tabel `products`

```sql
CREATE TABLE products (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255)    NOT NULL,
    description TEXT            NULL,
    stock       INT             NOT NULL,
    price       DECIMAL(10, 2)  NOT NULL,  -- contoh: 99999999.99
    image       VARCHAR(255)    NULL,      -- nama file gambar produk
    created_at  TIMESTAMP       NULL,
    updated_at  TIMESTAMP       NULL
);
```

```php
// Migration Laravel
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->integer('stock');
    $table->decimal('price', 10, 2);
    $table->string('image')->nullable();
    $table->timestamps();
});
```

---

### Relasi Antar Tabel

```
users ──────────── (tidak ada relasi langsung ke products)
                   Setiap user bisa login dan mengelola semua produk.
                   Tidak ada kepemilikan produk per user di versi ini.
```

### Catatan Penyimpanan File

Kolom `image` dan `avatar` hanya menyimpan **nama file**, bukan path lengkap.

```
Yang tersimpan di DB  : 1716888123_aB3xYz.jpg
URL lengkap di browser: http://127.0.0.1:8000/uploads/products/1716888123_aB3xYz.jpg
```

Struktur folder penyimpanan file di Laravel:

```
public/
└── uploads/
    ├── products/   ← gambar produk
    └── avatars/    ← foto profil user
```

---

## 📦 Packages yang Digunakan

### Backend — Laravel

| Package             | Versi | Fungsi                         | Instalasi                         |
| ------------------- | ----- | ------------------------------ | --------------------------------- |
| `laravel/framework` | ^13.x | Framework PHP utama            | Sudah termasuk                    |
| `tymon/jwt-auth`    | ^2.x  | Autentikasi berbasis JWT token | `composer require tymon/jwt-auth` |

#### Konfigurasi `tymon/jwt-auth`

Setelah install, jalankan:

```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

Lalu konfigurasi `config/auth.php`:

```php
'defaults' => [
    'guard'     => 'api',       // ubah dari 'web' ke 'api'
    'passwords' => 'users',
],

'guards' => [
    'api' => [
        'driver'   => 'jwt',    // driver menggunakan jwt
        'provider' => 'users',
    ],
],
```

---

### Frontend — Vue 3

| Package              | Versi | Fungsi                                  |
| -------------------- | ----- | --------------------------------------- |
| `vue`                | ^3.4  | Framework JavaScript utama              |
| `vue-router`         | ^4.3  | Routing SPA antar halaman               |
| `vite`               | ^5.0  | Build tool & development server         |
| `@vitejs/plugin-vue` | ^5.0  | Plugin Vite untuk memproses file `.vue` |

> **Tidak menggunakan Axios.** Semua HTTP request menggunakan `fetch()` native yang sudah tersedia di browser modern, dibungkus dalam `src/services/api.js`.

---

## ✍️ Standar Penulisan Kode

Konsistensi kode sangat penting agar mudah dibaca oleh siapa pun di tim. Ikuti standar berikut di seluruh proyek.

### PHP / Laravel

#### Penamaan (Naming Convention)

| Jenis         | Format              | Contoh                                |
| ------------- | ------------------- | ------------------------------------- |
| Class & Model | `PascalCase`        | `ProductController`, `User`           |
| Method        | `camelCase`         | `getProductById()`, `updateProfile()` |
| Variabel      | `camelCase`         | `$imageName`, `$uploadPath`           |
| Konstanta     | `UPPER_SNAKE_CASE`  | `MAX_FILE_SIZE`                       |
| Tabel DB      | `snake_case` plural | `products`, `users`                   |
| Kolom DB      | `snake_case`        | `created_at`, `email_verified_at`     |
| Route         | `kebab-case`        | `/update-profile`, `/get-all`         |

#### Struktur Controller

Setiap method di controller mengikuti urutan: **validasi → proses → response**.

```php
public function store(Request $request)
{
    // 1. Validasi input
    $request->validate([...]);

    // 2. Proses bisnis (upload, create, update, dll)
    $product = Product::create([...]);

    // 3. Kembalikan response yang seragam
    return $this->response('Product created successfully', $product, 201);
}
```

#### Format Response Seragam

**Selalu** gunakan format response yang sama di semua controller:

```php
// Helper method di setiap controller
private function response($message, $data = null, $status = 200)
{
    return response()->json([
        'status'  => $status,
        'message' => $message,
        'data'    => $data,
    ], $status);
}
```

```json
{
  "status": 200,
  "message": "Product retrieved successfully",
  "data": {
    "id": 1,
    "name": "Laptop ASUS",
    "price": 10000000,
    "stock": 5
  }
}
```

#### Komentar Kode

Gunakan DocBlock untuk method dan komentar inline untuk logika yang tidak langsung jelas:

```php
/**
 * Menyimpan produk baru ke database.
 *
 * @param Request $request
 * @return JsonResponse
 */
public function store(Request $request)
{
    // Buat nama file unik agar tidak ada duplikat
    $imageName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
}
```

---

### JavaScript / Vue 3

#### Penamaan (Naming Convention)

| Jenis           | Format                     | Contoh                                |
| --------------- | -------------------------- | ------------------------------------- |
| Komponen Vue    | `PascalCase`               | `AppSidebar.vue`, `ConfirmModal.vue`  |
| File View       | `PascalCase` + `View`      | `DashboardView.vue`, `LoginView.vue`  |
| Composable      | `camelCase` + `use` prefix | `useAlert.js`, `useAuth.js`           |
| Service         | `camelCase` + `Service`    | `authService.js`, `productService.js` |
| Variabel/fungsi | `camelCase`                | `isLoading`, `handleSubmit`           |
| Konstanta       | `UPPER_SNAKE_CASE`         | `BASE_URL`, `MAX_FILE_SIZE`           |
| CSS class       | `kebab-case`               | `.card-header`, `.btn-primary`        |
| CSS variable    | `--kebab-case`             | `--primary`, `--border-radius`        |

#### Struktur `<script setup>`

Ikuti urutan penulisan berikut di setiap komponen:

```vue
<script setup>
// 1. Import
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAlert } from '../composables/useAlert.js'
import productService from '../services/productService.js'

// 2. Props & Emits
const props = defineProps({ ... })
const emit  = defineEmits([...])

// 3. Composables
const router       = useRouter()
const { success }  = useAlert()

// 4. State (ref & reactive)
const isLoading = ref(false)
const products  = ref([])

// 5. Computed
const filteredProducts = computed(() => [...])

// 6. Fungsi / Methods
function handleSubmit() { ... }
async function fetchData() { ... }

// 7. Lifecycle hooks
onMounted(fetchData)
</script>
```

#### Komentar Kode

Setiap file, fungsi penting, dan logika yang tidak langsung jelas wajib diberi komentar:

```javascript
/**
 * Mengambil semua produk dari API.
 * Dipanggil saat komponen pertama kali dimount.
 */
async function fetchProducts() {
  isLoading.value = true;

  const { data, error } = await productService.getAll();

  // Jika error, tampilkan notifikasi dan hentikan proses
  if (error) {
    showError("Gagal memuat produk", error);
    return;
  }

  products.value = data.data;
  isLoading.value = false;
}
```

---

## 🌐 Standar REST API — Laravel

### Desain URL Endpoint

| Aksi        | Method     | URL                  | Controller Method |
| ----------- | ---------- | -------------------- | ----------------- |
| Ambil semua | `GET`      | `/api/products`      | `index()`         |
| Buat baru   | `POST`     | `/api/products`      | `store()`         |
| Ambil satu  | `GET`      | `/api/products/{id}` | `show()`          |
| Update      | `PUT/POST` | `/api/products/{id}` | `update()`        |
| Hapus       | `DELETE`   | `/api/products/{id}` | `destroy()`       |

> **Catatan khusus upload file:** Gunakan `POST` + field `_method=PUT` di body karena PHP tidak bisa membaca `FormData` dari method `PUT`.

### HTTP Status Code

| Situasi                                              | Status Code                 |
| ---------------------------------------------------- | --------------------------- |
| Request berhasil (data dikembalikan)                 | `200 OK`                    |
| Resource berhasil dibuat                             | `201 Created`               |
| Request berhasil (tidak ada data)                    | `200 OK`                    |
| Validasi gagal                                       | `422 Unprocessable Entity`  |
| Tidak terautentikasi (token tidak ada/invalid)       | `401 Unauthorized`          |
| Tidak diizinkan (sudah login tapi tidak punya akses) | `403 Forbidden`             |
| Resource tidak ditemukan                             | `404 Not Found`             |
| Error di server                                      | `500 Internal Server Error` |

### Struktur Route API (`routes/api.php`)

```php
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Public routes — bisa diakses tanpa token
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes — wajib membawa JWT token
Route::middleware('auth:api')->group(function () {
    Route::get('/me',             [AuthController::class, 'me']);
    Route::post('/logout',        [AuthController::class, 'logout']);
    Route::post('/update-profile',[AuthController::class, 'updateProfile']);

    // apiResource otomatis buat 5 route sekaligus (index, store, show, update, destroy)
    Route::apiResource('products', ProductController::class);
});
```

### Validasi Input

Selalu validasi input di dalam controller menggunakan `$request->validate()`. Jika validasi gagal, Laravel otomatis mengembalikan response `422` dengan detail error.

```php
$request->validate([
    'name'  => 'required|string|max:255',
    'price' => 'required|numeric|min:0',
    'stock' => 'required|integer|min:0',
    'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
]);
```

### Header Wajib di Setiap Request

```
Accept: application/json        ← wajib, agar Laravel selalu return JSON
Authorization: Bearer {token}   ← wajib untuk protected route
```

> Tanpa header `Accept: application/json`, Laravel akan mengembalikan halaman HTML saat terjadi error — bukan JSON.

### Alur Autentikasi JWT

```
1. Client POST /api/login dengan { email, password }
2. Server verifikasi kredensial
3. Server generate JWT token dan kembalikan ke client
4. Client simpan token di localStorage
5. Client sertakan token di setiap request berikutnya:
   Header → Authorization: Bearer eyJhbGci...
6. Server verifikasi token → proses request → kembalikan data
7. Saat logout, server invalidasi token agar tidak bisa dipakai lagi
```

---

## 📁 Standar Struktur Folder — Vue 3

### Prinsip Dasar

| Prinsip                    | Penjelasan                                                                |
| -------------------------- | ------------------------------------------------------------------------- |
| **Separation of Concerns** | Pisahkan logika API, state, tampilan, dan routing ke folder masing-masing |
| **Reusability**            | Komponen dan composable harus bisa dipakai di lebih dari satu tempat      |
| **Single Responsibility**  | Setiap file punya satu tanggung jawab yang jelas                          |
| **Flat over Nested**       | Hindari folder bersarang terlalu dalam — cukup 2 level                    |

### Struktur Folder

```
src/
│
├── assets/             # File statis: CSS global, gambar, font
│   └── main.css        # Satu file CSS global dengan CSS variables
│
├── services/           # Semua komunikasi dengan backend API
│   ├── api.js          # Base layer: fetch wrapper, token management
│   ├── authService.js  # Endpoint autentikasi
│   └── productService.js # Endpoint produk
│
├── composables/        # Logika reaktif yang bisa dipakai ulang (Custom Hooks)
│   ├── useAlert.js     # State dan fungsi untuk notifikasi global
│   └── useAuth.js      # State user yang sedang login
│
├── router/             # Konfigurasi Vue Router
│   └── index.js        # Definisi routes + navigation guard
│
├── components/         # Komponen UI kecil dan reusable
│   ├── AppSidebar.vue  # Sidebar (global, dipakai di semua halaman)
│   ├── AlertPopup.vue  # Notifikasi floating (global)
│   └── ConfirmModal.vue # Dialog konfirmasi (dipakai di beberapa halaman)
│
├── views/              # Komponen halaman (satu file = satu halaman)
│   ├── LoginView.vue
│   ├── RegisterView.vue
│   ├── DashboardView.vue
│   ├── ProductDetailView.vue
│   ├── ProductFormView.vue   # Dipakai untuk Create & Edit (2 mode, 1 file)
│   └── ProfileView.vue
│
├── App.vue             # Komponen root: layout utama
└── main.js             # Entry point: inisialisasi Vue + pasang plugin
```

### Kapan Membuat Apa?

```
Butuh halaman baru?
  → Buat di views/, daftarkan di router/index.js

Butuh komponen yang dipakai > 1 halaman?
  → Buat di components/

Butuh logika reaktif yang dipakai > 1 komponen?
  → Buat composable di composables/ dengan prefix 'use'

Butuh komunikasi dengan API endpoint baru?
  → Tambahkan fungsi di services/ yang sesuai (atau buat file baru)

Butuh variabel CSS / style global?
  → Tambahkan di assets/main.css
```

### Perbedaan `views/` dan `components/`

|                    | `views/`                     | `components/`             |
| ------------------ | ---------------------------- | ------------------------- |
| **Fungsi**         | Mewakili satu halaman penuh  | Bagian kecil dari halaman |
| **Dipanggil oleh** | Vue Router                   | Komponen / View lain      |
| **Boleh berisi**   | Data fetching, state halaman | Hanya logika tampilan     |
| **Contoh**         | `DashboardView.vue`          | `ConfirmModal.vue`        |

### Perbedaan `composables/` dan `services/`

|                     | `composables/`                   | `services/`                           |
| ------------------- | -------------------------------- | ------------------------------------- |
| **Fungsi**          | Logika reaktif & state           | Komunikasi HTTP ke API                |
| **Menggunakan**     | `ref`, `computed`, lifecycle Vue | `fetch()`, token, FormData            |
| **Bisa dipakai di** | Komponen & View Vue              | Composable, View, komponen mana pun   |
| **Contoh**          | `useAlert.js`, `useAuth.js`      | `authService.js`, `productService.js` |

### Layer API — `services/api.js`

Semua request HTTP **wajib** melalui fungsi di `services/`, tidak boleh menulis `fetch()` langsung di View atau Component.

```
View.vue
  │ memanggil
  ▼
authService.js / productService.js
  │ memanggil
  ▼
api.js (base layer)
  │ fetch() dengan header otomatis (token, Accept)
  ▼
Laravel API
  │ response JSON
  ▼
api.js (parse, tangani error)
  │ kembalikan { data, error }
  ▼
Service (kembalikan ke View)
  │
  ▼
View (update state → re-render)
```

### CSS — Design Token System

Semua nilai desain (warna, ukuran, shadow) didefinisikan sebagai **CSS Custom Properties** di `assets/main.css`:

```css
:root {
  /* Warna */
  --background: #ffffff;
  --foreground: #09090b;
  --primary: #18181b;
  --muted: #f4f4f5;
  --border: #e4e4e7;
  --destructive: #dc2626;

  /* Ukuran */
  --radius: 0.5rem;
  --radius-sm: 0.25rem;

  /* Tipografi */
  --font-sans: "DM Sans", sans-serif;
  --font-mono: "DM Mono", monospace;
}
```

Gunakan variabel ini di komponen — **jangan hardcode nilai warna**:

```css
/* ✅ Benar */
color: var(--foreground);
border: 1px solid var(--border);

/* ❌ Salah */
color: #09090b;
border: 1px solid #e4e4e7;
```

---

## 🚦 Cara Memulai

### 1. Jalankan Backend (Laravel)

```bash
cd backend-laravel

composer install
cp .env.example .env

# Setup database di .env, lalu:
php artisan key:generate
php artisan jwt:secret
php artisan install:api
php artisan migrate
mkdir -p public/uploads/products public/uploads/avatars

php artisan serve
# → http://127.0.0.1:8000
```

### 2. Jalankan Frontend (Vue 3)

```bash
cd inventory-app

npm install
npm run dev
# → http://localhost:5173
```

### 3. Buka di Browser

Buka `http://localhost:5173` — pastikan backend sudah berjalan terlebih dahulu.

---

## 📚 Referensi

| Topik                   | Link                                                       |
| ----------------------- | ---------------------------------------------------------- |
| Vue 3 Docs              | https://vuejs.org/guide                                    |
| Vue Router              | https://router.vuejs.org                                   |
| Vite                    | https://vitejs.dev                                         |
| Laravel 13 Docs         | https://laravel.com/docs                                   |
| tymon/jwt-auth          | https://jwt-auth.readthedocs.io                            |
| REST API Best Practices | https://restfulapi.net                                     |
| MDN — Fetch API         | https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API |

---

> **Dibuat sebagai bahan pembelajaran mahasiswa.**
> Baca README ini sebelum membuka kode apapun. Pahami arsitektur, konvensi, dan standar terlebih dahulu — baru mulai coding.
