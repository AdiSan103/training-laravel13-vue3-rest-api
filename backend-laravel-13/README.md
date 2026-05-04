# 🗄️ Inventory API — Laravel 13 + JWT Auth

Backend REST API untuk aplikasi manajemen inventaris, dibangun dengan **Laravel 13** dan autentikasi berbasis **JWT (JSON Web Token)** menggunakan library `tymon/jwt-auth`.

---

## 🗂️ Daftar Isi

- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Fitur API](#-fitur-api)
- [Struktur Folder](#-struktur-folder)
- [Prasyarat](#-prasyarat)
- [Cara Clone & Install](#-cara-clone--install)
- [Konfigurasi](#-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Daftar Endpoint API](#-daftar-endpoint-api)
- [Skema Database](#-skema-database)
- [Penjelasan Konsep Penting](#-penjelasan-konsep-penting)
- [Troubleshooting](#-troubleshooting)

---

## 🧰 Teknologi yang Digunakan

| Teknologi                                                  | Versi | Keterangan                 |
| ---------------------------------------------------------- | ----- | -------------------------- |
| [Laravel](https://laravel.com/)                            | 13    | PHP Framework utama        |
| [PHP](https://www.php.net/)                                | ^8.2  | Bahasa pemrograman backend |
| [MySQL](https://www.mysql.com/)                            | 8.x   | Database relasional        |
| [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth) | ^2.x  | Library autentikasi JWT    |
| [Composer](https://getcomposer.org/)                       | ^2.x  | Package manager PHP        |

---

## ✨ Fitur API

| Fitur              | Keterangan                                            |
| ------------------ | ----------------------------------------------------- |
| **Register**       | Registrasi user baru                                  |
| **Login**          | Login dan mendapatkan JWT token                       |
| **Get Me**         | Ambil data user yang sedang login                     |
| **Logout**         | Invalidasi token JWT                                  |
| **Update Profil**  | Update nama, password, dan avatar user                |
| **CRUD Produk**    | Create, Read, Update, Delete produk                   |
| **Upload Gambar**  | Upload gambar produk & avatar user                    |
| **Proteksi Route** | Semua route produk & profil dilindungi JWT middleware |

---

## 📁 Struktur Folder

```
backend-laravel/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php      # Login, Register, Me, Logout, Update Profil
│   │       └── ProductController.php   # CRUD Produk (index, store, show, update, destroy)
│   │
│   └── Models/
│       ├── User.php                    # Model User (implements JWTSubject)
│       └── Product.php                 # Model Product
│
├── bootstrap/
│   └── app.php                         # ⚠️ Wajib daftarkan routes/api.php di sini
│
├── config/
│   └── auth.php                        # Konfigurasi guard JWT
│
├── database/
│   └── migrations/
│       ├── xxxx_create_users_table.php
│       ├── xxxx_add_avatar_to_users_table.php
│       └── xxxx_create_products_table.php
│
├── public/
│   └── uploads/
│       ├── products/                   # Folder penyimpanan gambar produk
│       └── avatars/                    # Folder penyimpanan avatar user
│
├── routes/
│   └── api.php                         # Definisi semua route API
│
├── .env                                # Konfigurasi environment (jangan di-commit)
├── .env.example                        # Template konfigurasi environment
└── composer.json                       # Daftar dependencies PHP
```

### Penjelasan File Kunci

| File                    | Fungsi                                                          |
| ----------------------- | --------------------------------------------------------------- |
| `AuthController.php`    | Menangani semua proses autentikasi user                         |
| `ProductController.php` | Menangani CRUD produk beserta upload gambar                     |
| `User.php`              | Model user, wajib `implements JWTSubject` agar JWT bisa bekerja |
| `Product.php`           | Model produk dengan `$fillable` dan `$casts`                    |
| `routes/api.php`        | Semua route API, dibagi public dan protected                    |
| `bootstrap/app.php`     | Konfigurasi bootstrap Laravel, tempat mendaftarkan `api.php`    |
| `config/auth.php`       | Mengatur default guard menjadi `api` dengan driver `jwt`        |

---

## 🛠️ Prasyarat

Pastikan semua software berikut sudah terpasang sebelum memulai.

| Software     | Versi Minimum | Cara Cek                                  |
| ------------ | ------------- | ----------------------------------------- |
| **PHP**      | 8.2 ke atas   | `php --version`                           |
| **Composer** | 2.x           | `composer --version`                      |
| **MySQL**    | 8.x           | Cek via phpMyAdmin atau `mysql --version` |
| **Git**      | —             | `git --version`                           |

> **Rekomendasi:** Gunakan [XAMPP](https://www.apachefriends.org/) atau [Laragon](https://laragon.org/) untuk kemudahan setup PHP & MySQL di Windows.

---

## 🚀 Cara Clone & Install

Ikuti langkah-langkah berikut secara berurutan.

### Langkah 1 — Clone Repository

```bash
git clone https://github.com/username/backend-laravel-inventory.git
```

Masuk ke folder proyek:

```bash
cd backend-laravel-inventory
```

### Langkah 2 — Install Dependencies PHP

```bash
composer install
```

> Perintah ini mengunduh semua package PHP yang dibutuhkan ke folder `vendor/`.

### Langkah 3 — Salin File Environment

```bash
cp .env.example .env
```

> Di Windows (CMD), gunakan: `copy .env.example .env`

### Langkah 4 — Konfigurasi Database

Buka file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_training_inventory
DB_USERNAME=root
DB_PASSWORD=
```

> Pastikan database `laravel_training_inventory` sudah dibuat di MySQL. Buat via phpMyAdmin atau perintah:
>
> ```sql
> CREATE DATABASE laravel_training_inventory;
> ```

### Langkah 5 — Generate Application Key

```bash
php artisan key:generate
```

### Langkah 6 — Generate JWT Secret

```bash
php artisan jwt:secret
```

> Perintah ini men-generate `JWT_SECRET` di file `.env`. **Wajib dijalankan** agar autentikasi JWT bisa bekerja. Tanpa ini, login akan selalu error.

### Langkah 7 — Setup Route API

Di Laravel 11+, file `routes/api.php` tidak otomatis terdaftar. Jalankan:

```bash
php artisan install:api
```

> Perintah ini mendaftarkan `api.php` dan memastikan prefix `/api` aktif.

### Langkah 8 — Jalankan Migration

```bash
php artisan migrate
```

> Perintah ini membuat semua tabel di database (users, products, dll).

### Langkah 9 — Buat Folder Upload

Buat folder untuk menyimpan gambar produk dan avatar user:

```bash
mkdir -p public/uploads/products
mkdir -p public/uploads/avatars
```

> Di Windows: `mkdir public\uploads\products` dan `mkdir public\uploads\avatars`

---

## ⚙️ Konfigurasi

### 1. `bootstrap/app.php` — Daftarkan Route API

Pastikan file `bootstrap/app.php` mendaftarkan `routes/api.php`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // ← wajib ada baris ini
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

> ⚠️ Jika baris `api:` tidak ada, semua request ke `/api/*` akan mengembalikan halaman HTML bukan JSON.

### 2. `config/auth.php` — Konfigurasi Guard JWT

```php
'defaults' => [
    'guard' => 'api',           // ← ubah dari 'web' ke 'api'
    'passwords' => 'users',
],

'guards' => [
    'api' => [
        'driver'   => 'jwt',    // ← driver harus 'jwt'
        'provider' => 'users',
    ],
],
```

### 3. `app/Models/User.php` — Implements JWTSubject

Model User **wajib** mengimplementasikan interface `JWTSubject` beserta dua method-nya:

```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // ...

    // Wajib: mengembalikan primary key sebagai identifier di token
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Wajib: data tambahan di dalam token (boleh kosong)
    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

> ⚠️ Tanpa `implements JWTSubject`, `auth()->attempt()` akan error: _"Argument #1 must be of type JWTSubject"_

---

## ▶️ Menjalankan Aplikasi

```bash
php artisan serve
```

Server akan berjalan di:

```
INFO  Server running on [http://127.0.0.1:8000].
```

> Biarkan terminal ini tetap berjalan selama menggunakan aplikasi.

### Perintah Berguna Lainnya

```bash
# Cek semua route yang terdaftar
php artisan route:list

# Cek hanya route API
php artisan route:list --path=api

# Bersihkan cache jika ada perubahan config
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

## 📡 Daftar Endpoint API

Base URL: `http://127.0.0.1:8000/api`

> Semua request ke route **Protected** wajib menyertakan header:
>
> ```
> Authorization: Bearer {token}
> Accept: application/json
> ```

### 🔓 Public Routes (Tanpa Token)

| Method | Endpoint    | Deskripsi            | Body                                                 |
| ------ | ----------- | -------------------- | ---------------------------------------------------- |
| `POST` | `/login`    | Login user           | `email`, `password`                                  |
| `POST` | `/register` | Registrasi user baru | `name`, `email`, `password`, `password_confirmation` |

### 🔒 Protected Routes (Perlu Token)

| Method   | Endpoint          | Deskripsi                       | Body                                                      |
| -------- | ----------------- | ------------------------------- | --------------------------------------------------------- |
| `GET`    | `/me`             | Data user yang sedang login     | —                                                         |
| `POST`   | `/logout`         | Logout & invalidasi token       | —                                                         |
| `POST`   | `/update-profile` | Update profil user              | `name`?, `password`?, `password_confirmation`?, `avatar`? |
| `GET`    | `/products`       | Ambil semua produk              | —                                                         |
| `POST`   | `/products`       | Buat produk baru                | `name`, `price`, `stock`, `description`?, `image`?        |
| `GET`    | `/products/{id}`  | Ambil detail produk             | —                                                         |
| `POST`   | `/products/{id}`  | Update produk (+ `_method=PUT`) | `name`?, `price`?, `stock`?, `description`?, `image`?     |
| `DELETE` | `/products/{id}`  | Hapus produk                    | —                                                         |

### Format Response

Semua response menggunakan format yang seragam:

```json
{
    "status": 200,
    "message": "Products retrieved successfully",
    "data": { ... }
}
```

**Response Error (Validasi):**

```json
{
    "message": "The name field is required.",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

**Response Error (Unauthorized):**

```json
{
    "status": 401,
    "message": "Unauthorized",
    "data": null
}
```

---

## 🗃️ Skema Database

### Tabel `users` (bawaan Laravel + tambahan kolom `avatar`)

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('avatar')->nullable(); // ← kolom tambahan untuk foto profil
    $table->rememberToken();
    $table->timestamps();
});
```

### Tabel `products`

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->integer('stock');
    $table->decimal('price', 10, 2);    // 10 digit total, 2 angka di belakang koma
    $table->string('image')->nullable(); // hanya menyimpan nama file, bukan path penuh
    $table->timestamps();
});
```

> **Catatan:** Kolom `image` hanya menyimpan **nama file** (contoh: `1716888123_aB3xYz.jpg`), bukan path lengkap. Path lengkapnya dibangun di frontend: `http://127.0.0.1:8000/uploads/products/{image}`.

---

## 💡 Penjelasan Konsep Penting

### Mengapa Menggunakan JWT?

JWT (JSON Web Token) adalah standar autentikasi **stateless** — server tidak perlu menyimpan session di database. Token dikirim di header setiap request, dan server memverifikasinya menggunakan secret key.

```
Client                          Server
  │── POST /api/login ──────────▶ │
  │◀── { token: "eyJ..." } ──────│
  │                               │
  │── GET /api/products ─────────▶│
  │   Header: Bearer eyJ...       │ (verifikasi token)
  │◀── { data: [...] } ──────────│
```

### Mengapa `POST` untuk Update Produk?

PHP tidak mendukung parsing `FormData` dari request `PUT` atau `PATCH`. Karena upload gambar membutuhkan `multipart/form-data`, maka digunakan:

- Method HTTP: `POST`
- Tambahkan field `_method=PUT` di body
- Laravel akan membaca `_method` dan memprosesnya sebagai `PUT`

```
// Di Postman atau frontend
POST /api/products/1
Body (form-data):
  _method = PUT
  name    = Updated Product
  image   = [file]
```

### Mengapa Perlu Header `Accept: application/json`?

Tanpa header ini, Laravel akan mengembalikan **halaman HTML** (redirect) saat terjadi error autentikasi atau validasi, bukan JSON. Dengan header ini, Laravel tahu bahwa client mengharapkan response JSON.

```
// Selalu sertakan di setiap request
Accept: application/json
```

### Penyimpanan Gambar

Gambar disimpan di folder `public/uploads/` agar bisa diakses langsung via URL:

```
File tersimpan di:
  public/uploads/products/1716888123_aB3xYz.jpg

Bisa diakses via:
  http://127.0.0.1:8000/uploads/products/1716888123_aB3xYz.jpg
```

Format nama file menggunakan `timestamp + random string` untuk memastikan nama selalu unik dan tidak saling menimpa.

---

## 🔧 Troubleshooting

### ❌ Response HTML bukan JSON saat hit endpoint API

**Penyebab:** Route API belum terdaftar atau header `Accept` tidak ada.

**Solusi:**

1. Cek `bootstrap/app.php` — pastikan ada `api: __DIR__.'/../routes/api.php'`
2. Jalankan `php artisan install:api`
3. Tambahkan header `Accept: application/json` di setiap request
4. Jalankan `php artisan route:clear && php artisan serve`

---

### ❌ Error: `Tymon\JWTAuth\JWTGuard::login(): Argument #1 must be of type JWTSubject`

**Penyebab:** Model `User` belum mengimplementasikan interface `JWTSubject`.

**Solusi:** Buka `app/Models/User.php` dan pastikan:

```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }
}
```

---

### ❌ Error 401 Unauthorized padahal token sudah dikirim

**Penyebab:** Token sudah expired, atau format header salah.

**Solusi:**

1. Login ulang untuk mendapatkan token baru
2. Pastikan format header benar: `Authorization: Bearer {token}` (ada spasi setelah Bearer)
3. Pastikan `config/auth.php` sudah menggunakan driver `jwt`

---

### ❌ Gambar tidak tersimpan saat upload

**Penyebab:** Folder `public/uploads/` belum ada, atau route update menggunakan `PUT` langsung.

**Solusi:**

1. Buat folder: `mkdir -p public/uploads/products public/uploads/avatars`
2. Pastikan route update di `api.php` menggunakan `Route::post()`, bukan `Route::put()`
3. Pastikan frontend mengirim `_method=PUT` di body form-data
4. Di controller, gunakan `User::find(auth()->id())` bukan `auth()->user()` untuk update & save

---

### ❌ Error 404 pada semua route `/api/*`

**Penyebab:** File `routes/api.php` belum di-bootstrap oleh Laravel.

**Solusi:**

```bash
php artisan install:api
php artisan route:clear
php artisan route:list  # verifikasi route muncul
php artisan serve
```

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan **edukasi dan pelatihan mahasiswa**. Bebas digunakan dan dimodifikasi untuk pembelajaran.
