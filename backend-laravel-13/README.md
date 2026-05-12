# 📘 Panduan Laravel 13 — Blog API

Panduan ini akan membimbing kamu dari **nol** hingga membuat REST API blog lengkap dengan Laravel 13, menggunakan JWT untuk autentikasi.

---

## 📋 Daftar Isi

1. [Persiapan](#1-persiapan)
2. [Buat Project Laravel](#2-buat-project-laravel)
3. [Konfigurasi Database](#3-konfigurasi-database)
4. [Install JWT](#4-install-jwt)
5. [Struktur Folder](#5-struktur-folder)
6. [Migration](#6-migration)
7. [Model](#7-model)
8. [Middleware CheckToken](#8-middleware-checktoken)
9. [AuthController](#9-authcontroller)
10. [CategoryController](#10-categorycontroller)
11. [PostController](#11-postcontroller)
12. [Routes API](#12-routes-api)
13. [Konfigurasi CORS](#13-konfigurasi-cors)
14. [Jalankan Server](#14-jalankan-server)

---

## 1. Persiapan

Pastikan sudah terinstall:

- **PHP** versi 8.2 ke atas
- **Composer** → [download di getcomposer.org](https://getcomposer.org)
- **MySQL** atau database lainnya
- **VS Code** atau editor pilihan kamu

Cek versi:

```bash
php -v
composer -V
```

---

## 2. Buat Project Laravel

```bash
composer create-project laravel/laravel blog-api
cd blog-api
```

> **Penjelasan:**
>
> - `composer create-project` → membuat project Laravel baru dari template resmi
> - `blog-api` → nama folder project kamu

---

## 3. Konfigurasi Database

Buka file `.env` di root project, sesuaikan bagian database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_api
DB_USERNAME=root
DB_PASSWORD=
```

> Buat dulu database `blog_api` di MySQL:
>
> ```sql
> CREATE DATABASE blog_api;
> ```

---

## 4. Install JWT

Kita pakai package `tymon/jwt-auth` sebagai pengganti Sanctum, lebih ringan dan tidak perlu tabel tambahan.

```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

> **Penjelasan:**
>
> - `vendor:publish` → menyalin file konfigurasi JWT ke project kamu
> - `jwt:secret` → generate secret key JWT, disimpan otomatis ke `.env` sebagai `JWT_SECRET`

Set guard JWT di `config/auth.php`:

```php
'defaults' => [
    'guard' => 'api',   // ← ganti dari 'web' ke 'api'
    'passwords' => 'users',
],

'guards' => [
    'api' => [
        'driver'   => 'jwt',      // ← ganti dari 'token' ke 'jwt'
        'provider' => 'users',
    ],
],
```

---

## 5. Struktur Folder

File yang akan kita buat:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   └── PostController.php
│   └── Middleware/
│       └── CheckToken.php
├── Models/
│   ├── User.php       ← edit file yang sudah ada
│   ├── Category.php   ← buat baru
│   └── Post.php       ← buat baru
database/
└── migrations/
    ├── ..._create_users_table.php      ← sudah ada
    ├── ..._create_categories_table.php ← buat baru
    └── ..._create_posts_table.php      ← buat baru
routes/
└── api.php
```

---

## 6. Migration

### Users (sudah ada, tidak perlu diubah)

File `database/migrations/0001_01_01_000000_create_users_table.php` sudah otomatis dibuat oleh Laravel.

### Buat Migration Categories

```bash
php artisan make:migration create_category_table
```

Edit file migration yang baru dibuat:

```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('category_name');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('categories');
}
```

### Buat Migration Posts

```bash
php artisan make:migration create_posts_table
```

Edit file migration yang baru dibuat:

```php
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->text('content');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('posts');
}
```

> **Penjelasan:**
>
> - `foreignId('user_id')->constrained()` → otomatis membuat foreign key ke tabel `users`
> - `cascadeOnDelete()` → jika user dihapus, semua postnya ikut terhapus
> - `slug()->unique()` → slug harus unik, dipakai sebagai pengganti ID di URL

Jalankan semua migration:

```bash
php artisan migrate
```

---

## 7. Model

### User.php

Edit file `app/Models/User.php`, tambahkan `JWTSubject`:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    // Dua method ini wajib ada karena implements JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

### Category.php

Buat file `app/Models/Category.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['category_name'];

    // Relasi: satu category punya banyak post
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

### Post.php

Buat file `app/Models/Post.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'slug', 'content', 'user_id', 'category_id'];

    // Relasi: post milik satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: post punya satu category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

> **Penjelasan relasi:**
>
> - `hasMany` → "satu category punya banyak post"
> - `belongsTo` → "post ini milik satu user / satu category"

---

## 8. Middleware CheckToken

Buat file `app/Http/Middleware/CheckToken.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Ambil dan verifikasi token dari header Authorization
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['message' => 'Token tidak valid'], 401);
            }

        } catch (\Exception $e) {
            return response()->json(['message' => 'Token tidak ada atau expired'], 401);
        }

        return $next($request);
    }
}
```

> **Cara kerja:**
>
> - Vue mengirim token di header: `Authorization: Bearer <token>`
> - Middleware membaca token tersebut via `JWTAuth::parseToken()`
> - Jika valid → request dilanjutkan ke controller
> - Jika tidak valid / tidak ada → langsung return error 401

---

## 9. AuthController

Buat file `app/Http/Controllers/AuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        return response()->json(['user' => auth()->user(), 'token' => $token]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logout berhasil']);
    }
}
```

> **Penjelasan:**
>
> - `Hash::make()` → mengenkripsi password sebelum disimpan ke database
> - `JWTAuth::fromUser($user)` → generate token dari object user
> - `JWTAuth::attempt($credentials)` → cek email & password, jika cocok langsung return token
> - `JWTAuth::invalidate()` → membuat token tidak bisa dipakai lagi (logout)

---

## 10. CategoryController

Buat file `app/Http/Controllers/CategoryController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Ambil semua kategori beserta jumlah post yang menggunakannya
    public function index()
    {
        $categories = Category::withCount('posts')->get();

        return response()->json($categories);
    }

    // Tambah kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|unique:categories,category_name',
        ]);

        $category = Category::create([
            'category_name' => $request->category_name,
        ]);

        return response()->json($category, 201);
    }

    // Update kategori
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category tidak ditemukan'], 404);
        }

        $request->validate([
            // unique kecuali untuk record dirinya sendiri (pakai $id)
            'category_name' => 'required|string|unique:categories,category_name,' . $id,
        ]);

        $category->update([
            'category_name' => $request->category_name,
        ]);

        return response()->json($category);
    }

    // Hapus kategori — tolak jika masih dipakai post
    public function destroy($id)
    {
        $category = Category::withCount('posts')->find($id);

        if (!$category) {
            return response()->json(['message' => 'Category tidak ditemukan'], 404);
        }

        if ($category->posts_count > 0) {
            return response()->json([
                'message' => "Category \"{$category->category_name}\" tidak bisa dihapus karena masih digunakan oleh {$category->posts_count} post.",
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Category berhasil dihapus']);
    }
}
```

> **Penjelasan:**
>
> - `withCount('posts')` → menambahkan kolom `posts_count` di hasil query, tanpa perlu query terpisah
> - Status `422` → artinya request valid tapi tidak bisa diproses (Unprocessable Entity)

---

## 11. PostController

Buat file `app/Http/Controllers/PostController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Ambil semua post beserta relasi user dan category
    public function index()
    {
        $posts = Post::with(['user', 'category'])->latest()->get();

        return response()->json($posts);
    }

    // Ambil satu post berdasarkan slug
    public function show($slug)
    {
        $post = Post::with(['user', 'category'])->where('slug', $slug)->first();

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        return response()->json($post);
    }

    // Buat post baru
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'content'     => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post = Post::create([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . Str::random(5),
            'content'     => $request->content,
            'category_id' => $request->category_id,
            'user_id'     => auth()->id(),
        ]);

        return response()->json($post, 201);
    }

    // Update post berdasarkan slug
    public function update(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $request->validate([
            'title'       => 'required|string',
            'content'     => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . Str::random(5),
            'content'     => $request->content,
            'category_id' => $request->category_id,
        ]);

        return response()->json($post);
    }

    // Hapus post
    public function destroy($slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Post berhasil dihapus']);
    }
}
```

> **Penjelasan:**
>
> - `Post::with(['user', 'category'])` → eager loading, mengambil relasi sekaligus dalam satu query (lebih efisien)
> - `Str::slug()` → mengubah judul menjadi format slug (huruf kecil, spasi jadi `-`)
> - `Str::random(5)` → tambahkan 5 karakter acak agar slug unik walau judul sama
> - `auth()->id()` → mengambil ID user yang sedang login dari token JWT

---

## 12. Routes API

Edit file `routes/api.php`:

```php
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\CheckToken;
use Illuminate\Support\Facades\Route;

// ─── Public (tidak perlu token) ───────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ─── Protected (wajib pakai token) ────────────────────────
Route::middleware(CheckToken::class)->group(function () {

    // Auth
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Posts
    Route::get('/posts',                [PostController::class, 'index']);
    Route::post('/posts',               [PostController::class, 'store']);
    Route::get('/posts/{slug}',         [PostController::class, 'show']);
    Route::post('/posts/{slug}/edit',   [PostController::class, 'update']);
    Route::post('/posts/{slug}/delete', [PostController::class, 'destroy']);

    // Categories
    Route::get('/categories',                  [CategoryController::class, 'index']);
    Route::post('/categories',                 [CategoryController::class, 'store']);
    Route::post('/categories/{id}/edit',       [CategoryController::class, 'update']);
    Route::post('/categories/{id}/delete',     [CategoryController::class, 'destroy']);
});
```

> **Kenapa pakai POST untuk edit dan delete?**
> Karena beberapa environment (hosting shared, proxy) memblokir method `PUT`, `PATCH`, `DELETE`. Pakai `POST` lebih aman dan simpel untuk API ini.

---

## 13. Konfigurasi CORS

Buka file `config/cors.php`, sesuaikan:

```php
'paths' => ['api/*'],

'allowed_origins' => ['http://localhost:5173'],  // URL Vue kamu

'allowed_methods' => ['*'],

'allowed_headers' => ['*'],
```

> Jika ingin izinkan semua origin (sementara untuk development):
>
> ```php
> 'allowed_origins' => ['*'],
> ```

---

## 14. Jalankan Server

```bash
php artisan serve --port=8001
```

Server akan berjalan di `http://127.0.0.1:8001`

---

## 🧪 Test API Secara Manual

Kamu bisa test API menggunakan **Thunder Client** (extension VS Code) atau **Postman**.

### Register

```
POST http://127.0.0.1:8001/api/register
Content-Type: application/json

{
  "name": "Budi",
  "email": "budi@email.com",
  "password": "password123"
}
```

### Login

```
POST http://127.0.0.1:8001/api/login
Content-Type: application/json

{
  "email": "budi@email.com",
  "password": "password123"
}
```

Response akan mengembalikan token, simpan tokennya untuk request berikutnya.

### Ambil Semua Post (butuh token)

```
GET http://127.0.0.1:8001/api/posts
Authorization: Bearer <token_dari_login>
```

---

## ✅ Checklist Akhir

- [ ] PHP 8.2+ dan Composer terinstall
- [ ] Database `blog_api` sudah dibuat di MySQL
- [ ] `.env` sudah dikonfigurasi (DB & JWT_SECRET)
- [ ] `php artisan migrate` sudah dijalankan
- [ ] `config/auth.php` sudah diubah ke guard `api` dengan driver `jwt`
- [ ] CORS sudah dikonfigurasi mengizinkan URL Vue

---

## 📁 Struktur File Akhir

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   └── PostController.php
│   └── Middleware/
│       └── CheckToken.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   └── Post.php
config/
├── auth.php     ← edit guard ke jwt
└── cors.php     ← edit allowed_origins
routes/
└── api.php
```

---

## 📌 Referensi HTTP Status Code

| Code | Arti          | Contoh Penggunaan                               |
| ---- | ------------- | ----------------------------------------------- |
| 200  | OK            | GET berhasil                                    |
| 201  | Created       | POST berhasil membuat data baru                 |
| 401  | Unauthorized  | Token tidak ada / tidak valid                   |
| 404  | Not Found     | Data tidak ditemukan                            |
| 422  | Unprocessable | Validasi gagal / kondisi bisnis tidak terpenuhi |
| 500  | Server Error  | Ada bug di server                               |
