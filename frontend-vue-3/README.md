# 📘 Panduan Vue 3 + Vite — Blog App

Panduan ini akan membimbing kamu dari **nol** hingga membuat aplikasi blog lengkap dengan Vue 3 dan Vite, terkoneksi ke Laravel API.

---

## 📋 Daftar Isi

1. [Persiapan](#1-persiapan)
2. [Buat Project Vue](#2-buat-project-vue)
3. [Struktur Folder](#3-struktur-folder)
4. [Install Vue Router](#4-install-vue-router)
5. [Setup Router](#5-setup-router)
6. [File .env](#6-file-env)
7. [Buat Halaman Login](#7-buat-halaman-login)
8. [Buat Halaman Register](#8-buat-halaman-register)
9. [Buat Halaman Posts (Daftar Post)](#9-buat-halaman-posts-daftar-post)
10. [Buat Halaman Detail Post](#10-buat-halaman-detail-post)
11. [Buat Halaman Tambah Post](#11-buat-halaman-tambah-post)
12. [Buat Halaman Edit Post](#12-buat-halaman-edit-post)
13. [Buat Halaman Kategori](#13-buat-halaman-kategori)
14. [Buat Halaman Tambah Kategori](#14-buat-halaman-tambah-kategori)
15. [Buat Halaman Edit Kategori](#15-buat-halaman-edit-kategori)
16. [Buat Component Navbar](#16-buat-component-navbar)
17. [Update App.vue](#17-update-appvue)
18. [Jalankan Project](#18-jalankan-project)

---

## 1. Persiapan

Pastikan kamu sudah menginstall:

- **Node.js** versi 18 ke atas → [download di nodejs.org](https://nodejs.org)
- **npm** (sudah termasuk saat install Node.js)
- **VS Code** (rekomendasi editor) → [download di code.visualstudio.com](https://code.visualstudio.com)

Cek versi Node.js kamu:

```bash
node -v
npm -v
```

---

## 2. Buat Project Vue

Buka terminal, lalu jalankan:

```bash
npm create vite@latest blog-frontend -- --template vue
cd blog-frontend
npm install
```

> **Penjelasan:**
>
> - `npm create vite@latest` → membuat project baru dengan Vite
> - `--template vue` → pakai template Vue 3
> - `cd blog-frontend` → masuk ke folder project
> - `npm install` → install semua dependency dasar

---

## 3. Struktur Folder

Setelah dibuat, struktur folder kamu akan seperti ini. Kita akan **menghapus file bawaan** dan menggantinya:

```
blog-frontend/
├── public/
├── src/
│   ├── components/       ← buat folder ini
│   │   └── Navbar.vue    ← component navbar
│   ├── views/            ← buat folder ini
│   │   ├── LoginView.vue
│   │   ├── RegisterView.vue
│   │   ├── PostsView.vue
│   │   ├── PostDetailView.vue
│   │   ├── PostCreateView.vue
│   │   ├── PostEditView.vue
│   │   ├── CategoriesView.vue
│   │   ├── CategoryCreateView.vue
│   │   └── CategoryEditView.vue
│   ├── router/           ← buat folder ini
│   │   └── index.js      ← konfigurasi routing
│   ├── App.vue           ← file utama
│   └── main.js           ← entry point
├── .env                  ← simpan URL API
└── package.json
```

Hapus file bawaan yang tidak dipakai:

```bash
rm src/components/HelloWorld.vue
rm src/assets/vue.svg
```

---

## 4. Install Vue Router

Vue Router digunakan untuk berpindah halaman tanpa reload.

```bash
npm install vue-router
```

---

## 5. Setup Router

Buat file `src/router/index.js`:

```js
import { createRouter, createWebHistory } from "vue-router";
import LoginView from "../views/LoginView.vue";
import RegisterView from "../views/RegisterView.vue";
import PostsView from "../views/PostsView.vue";
import PostDetailView from "../views/PostDetailView.vue";
import PostCreateView from "../views/PostCreateView.vue";
import PostEditView from "../views/PostEditView.vue";
import CategoriesView from "../views/CategoriesView.vue";
import CategoryCreateView from "../views/CategoryCreateView.vue";
import CategoryEditView from "../views/CategoryEditView.vue";

const routes = [
  { path: "/", redirect: "/posts" },
  { path: "/login", component: LoginView },
  { path: "/register", component: RegisterView },
  { path: "/posts", component: PostsView, meta: { auth: true } },
  { path: "/posts/create", component: PostCreateView, meta: { auth: true } },
  { path: "/posts/:slug", component: PostDetailView, meta: { auth: true } },
  { path: "/posts/:slug/edit", component: PostEditView, meta: { auth: true } },
  { path: "/categories", component: CategoriesView, meta: { auth: true } },
  {
    path: "/categories/create",
    component: CategoryCreateView,
    meta: { auth: true },
  },
  {
    path: "/categories/:id/edit",
    component: CategoryEditView,
    meta: { auth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Guard: halaman dengan meta.auth hanya bisa diakses jika ada token
router.beforeEach((to) => {
  const token = localStorage.getItem("token");
  if (to.meta.auth && !token) return "/login";
});

export default router;
```

> **Penjelasan:**
>
> - `meta: { auth: true }` → halaman ini butuh login
> - `router.beforeEach` → dicek setiap kali pindah halaman, jika belum login akan diarahkan ke `/login`
> - `createWebHistory()` → URL bersih tanpa `#` (misal `/posts` bukan `/#/posts`)

Daftarkan router di `src/main.js`:

```js
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

createApp(App).use(router).mount("#app");
```

---

## 6. File .env

Buat file `.env` di **root project** (sejajar dengan `package.json`):

```env
VITE_API_URL=http://127.0.0.1:8001/api
```

> **Penting:** Variabel di Vite harus diawali `VITE_` agar bisa diakses di dalam kode Vue.

Cara pakainya di setiap file Vue:

```js
const BASE_URL = import.meta.env.VITE_API_URL;
```

---

## 7. Buat Halaman Login

Buat file `src/views/LoginView.vue`:

```vue
<template>
  <div class="auth-wrapper">
    <div class="card">
      <h2>Login</h2>

      <div class="form-group">
        <label>Email</label>
        <input v-model="email" type="email" placeholder="email@example.com" />
      </div>

      <div class="form-group">
        <label>Password</label>
        <input
          v-model="password"
          type="password"
          placeholder="••••••••"
          @keyup.enter="login"
        />
      </div>

      <button class="btn btn-primary" :disabled="loading" @click="login">
        {{ loading ? "Loading..." : "Login" }}
      </button>

      <p class="error-msg" v-if="error">{{ error }}</p>

      <p class="switch-link">
        Belum punya akun? <router-link to="/register">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const BASE_URL = import.meta.env.VITE_API_URL;

const email = ref("");
const password = ref("");
const loading = ref(false);
const error = ref("");

async function login() {
  error.value = "";
  loading.value = true;

  try {
    const response = await fetch(`${BASE_URL}/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value,
      }),
    });

    if (!response.ok) throw new Error("Email atau password salah");

    const data = await response.json();
    localStorage.setItem("token", data.token);
    router.push("/posts");
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.auth-wrapper {
  max-width: 420px;
  margin: 60px auto;
}
h2 {
  margin-bottom: 24px;
  font-size: 1.5rem;
}
.switch-link {
  margin-top: 16px;
  font-size: 0.88rem;
  color: #666;
}
.switch-link a {
  color: #89b4fa;
}
</style>
```

> **Pola fetch yang dipakai di semua halaman:**
>
> ```js
> const response = await fetch(url, options); // kirim request
> if (!response.ok) throw new Error("..."); // cek jika gagal
> const data = await response.json(); // ambil datanya
> ```

---

## 8. Buat Halaman Register

Buat file `src/views/RegisterView.vue`:

```vue
<template>
  <div class="auth-wrapper">
    <div class="card">
      <h2>Register</h2>

      <div class="form-group">
        <label>Name</label>
        <input v-model="name" type="text" placeholder="Nama lengkap" />
      </div>

      <div class="form-group">
        <label>Email</label>
        <input v-model="email" type="email" placeholder="email@example.com" />
      </div>

      <div class="form-group">
        <label>Password</label>
        <input
          v-model="password"
          type="password"
          placeholder="Min. 8 karakter"
        />
      </div>

      <button class="btn btn-primary" :disabled="loading" @click="register">
        {{ loading ? "Loading..." : "Register" }}
      </button>

      <p class="error-msg" v-if="error">{{ error }}</p>

      <p class="switch-link">
        Sudah punya akun? <router-link to="/login">Login</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const BASE_URL = import.meta.env.VITE_API_URL;

const name = ref("");
const email = ref("");
const password = ref("");
const loading = ref(false);
const error = ref("");

async function register() {
  error.value = "";
  loading.value = true;

  try {
    const response = await fetch(`${BASE_URL}/register`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        name: name.value,
        email: email.value,
        password: password.value,
      }),
    });

    if (!response.ok)
      throw new Error("Registrasi gagal, cek kembali data Anda");

    const data = await response.json();
    localStorage.setItem("token", data.token);
    router.push("/posts");
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.auth-wrapper {
  max-width: 420px;
  margin: 60px auto;
}
h2 {
  margin-bottom: 24px;
  font-size: 1.5rem;
}
.switch-link {
  margin-top: 16px;
  font-size: 0.88rem;
  color: #666;
}
.switch-link a {
  color: #89b4fa;
}
</style>
```

---

## 9. Buat Halaman Posts (Daftar Post)

Buat file `src/views/PostsView.vue`:

```vue
<template>
  <div>
    <div class="page-header">
      <h2>Semua Post</h2>
    </div>

    <p v-if="loading" class="loading">Memuat posts...</p>
    <p v-if="error" class="error-msg">{{ error }}</p>

    <div v-if="!loading && posts.length === 0" class="empty">
      Belum ada post.
      <router-link to="/posts/create">Buat sekarang →</router-link>
    </div>

    <div class="post-list">
      <div v-for="post in posts" :key="post.id" class="post-card">
        <div class="post-meta">
          <span class="category">{{ post.category?.category_name }}</span>
          <span class="date">{{ formatDate(post.created_at) }}</span>
        </div>
        <h3>{{ post.title }}</h3>
        <p class="excerpt">{{ excerpt(post.content) }}</p>
        <div class="post-actions">
          <router-link :to="`/posts/${post.slug}`" class="btn btn-primary"
            >Baca</router-link
          >
          <router-link
            :to="`/posts/${post.slug}/edit`"
            class="btn btn-secondary"
            >Edit</router-link
          >
          <button class="btn btn-danger" @click="deletePost(post.slug)">
            Hapus
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const BASE_URL = import.meta.env.VITE_API_URL;
const token = localStorage.getItem("token");

const posts = ref([]);
const loading = ref(false);
const error = ref("");

async function fetchPosts() {
  loading.value = true;
  error.value = "";

  try {
    const response = await fetch(`${BASE_URL}/posts`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    if (!response.ok) throw new Error("Gagal memuat posts");

    posts.value = await response.json();
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

async function deletePost(slug) {
  if (!confirm("Yakin ingin menghapus post ini?")) return;

  try {
    const response = await fetch(`${BASE_URL}/posts/${slug}/delete`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    if (!response.ok) throw new Error("Gagal menghapus post");

    posts.value = posts.value.filter((p) => p.slug !== slug);
  } catch (err) {
    alert(err.message);
  }
}

function excerpt(text) {
  return text?.length > 120 ? text.slice(0, 120) + "..." : text;
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

onMounted(fetchPosts);
</script>
```

---

## 10. Buat Halaman Detail Post

Buat file `src/views/PostDetailView.vue`:

```vue
<template>
  <div>
    <button class="btn btn-secondary back-btn" @click="router.back()">
      ← Kembali
    </button>

    <p v-if="loading" class="loading">Memuat post...</p>
    <p v-if="error" class="error-msg">{{ error }}</p>

    <div v-if="post" class="card">
      <div class="post-meta">
        <span class="category">{{ post.category?.category_name }}</span>
        <span class="date">{{ formatDate(post.created_at) }}</span>
      </div>
      <h1>{{ post.title }}</h1>
      <p class="author">oleh {{ post.user?.name }}</p>
      <hr />
      <div class="content">{{ post.content }}</div>
      <div class="actions">
        <router-link :to="`/posts/${post.slug}/edit`" class="btn btn-secondary"
          >Edit</router-link
        >
        <button class="btn btn-danger" @click="deletePost">Hapus</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const BASE_URL = import.meta.env.VITE_API_URL;
const token = localStorage.getItem("token");
const slug = route.params.slug;

const post = ref(null);
const loading = ref(false);
const error = ref("");

async function fetchPost() {
  loading.value = true;
  error.value = "";

  try {
    const response = await fetch(`${BASE_URL}/posts/${slug}`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    if (!response.ok) throw new Error("Post tidak ditemukan");

    post.value = await response.json();
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

async function deletePost() {
  if (!confirm("Yakin ingin menghapus post ini?")) return;

  try {
    const response = await fetch(`${BASE_URL}/posts/${slug}/delete`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    if (!response.ok) throw new Error("Gagal menghapus post");

    router.push("/posts");
  } catch (err) {
    alert(err.message);
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

onMounted(fetchPost);
</script>
```

---

## 11. Buat Halaman Tambah Post

Buat file `src/views/PostCreateView.vue`:

```vue
<template>
  <div>
    <h2>Buat Post Baru</h2>
    <div class="card" style="margin-top: 20px;">
      <div class="form-group">
        <label>Judul</label>
        <input v-model="form.title" type="text" placeholder="Judul post..." />
      </div>
      <div class="form-group">
        <label>Slug</label>
        <input v-model="form.slug" type="text" placeholder="judul-post-anda" />
        <small>Otomatis dari judul, bisa diubah manual</small>
      </div>
      <div class="form-group">
        <label>Kategori</label>
        <select v-model="form.category_id">
          <option value="" disabled>Pilih kategori...</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.category_name }}
          </option>
        </select>
      </div>
      <div class="form-group">
        <label>Konten</label>
        <textarea
          v-model="form.content"
          placeholder="Tulis konten post di sini..."
        />
      </div>
      <div class="btn-row">
        <button class="btn btn-secondary" @click="router.back()">Batal</button>
        <button class="btn btn-primary" :disabled="loading" @click="createPost">
          {{ loading ? "Menyimpan..." : "Simpan Post" }}
        </button>
      </div>
      <p class="error-msg" v-if="error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const BASE_URL = import.meta.env.VITE_API_URL;
const token = localStorage.getItem("token");

const categories = ref([]);
const loading = ref(false);
const error = ref("");

const form = ref({ title: "", slug: "", category_id: "", content: "" });

// Auto-generate slug dari title
watch(
  () => form.value.title,
  (val) => {
    form.value.slug = val
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, "")
      .trim()
      .replace(/\s+/g, "-");
  },
);

async function fetchCategories() {
  try {
    const response = await fetch(`${BASE_URL}/categories`, {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    if (!response.ok) throw new Error("Gagal memuat kategori");
    categories.value = await response.json();
  } catch (err) {
    error.value = err.message;
  }
}

async function createPost() {
  error.value = "";
  loading.value = true;
  try {
    const response = await fetch(`${BASE_URL}/posts`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(form.value),
    });
    if (!response.ok) throw new Error("Gagal membuat post");
    const data = await response.json();
    router.push(`/posts/${data.slug}`);
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

onMounted(fetchCategories);
</script>
```

---

## 12. Buat Halaman Edit Post

Buat file `src/views/PostEditView.vue`:

```vue
<template>
  <div>
    <h2>Edit Post</h2>
    <p v-if="loading && !form.title" class="loading">Memuat data...</p>
    <div class="card" style="margin-top: 20px;" v-if="form.title || !loading">
      <div class="form-group">
        <label>Judul</label>
        <input v-model="form.title" type="text" />
      </div>
      <div class="form-group">
        <label>Slug</label>
        <input v-model="form.slug" type="text" />
      </div>
      <div class="form-group">
        <label>Kategori</label>
        <select v-model="form.category_id">
          <option value="" disabled>Pilih kategori...</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.category_name }}
          </option>
        </select>
      </div>
      <div class="form-group">
        <label>Konten</label>
        <textarea v-model="form.content" />
      </div>
      <div class="btn-row">
        <button class="btn btn-secondary" @click="router.back()">Batal</button>
        <button class="btn btn-primary" :disabled="loading" @click="updatePost">
          {{ loading ? "Menyimpan..." : "Update Post" }}
        </button>
      </div>
      <p class="error-msg" v-if="error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const BASE_URL = import.meta.env.VITE_API_URL;
const token = localStorage.getItem("token");
const slug = route.params.slug;

const categories = ref([]);
const loading = ref(false);
const error = ref("");
const form = ref({ title: "", slug: "", category_id: "", content: "" });

async function fetchPost() {
  loading.value = true;
  try {
    const response = await fetch(`${BASE_URL}/posts/${slug}`, {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    if (!response.ok) throw new Error("Post tidak ditemukan");
    const data = await response.json();
    form.value = {
      title: data.title,
      slug: data.slug,
      category_id: data.category_id,
      content: data.content,
    };
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

async function fetchCategories() {
  try {
    const response = await fetch(`${BASE_URL}/categories`, {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    if (!response.ok) throw new Error("Gagal memuat kategori");
    categories.value = await response.json();
  } catch (err) {
    error.value = err.message;
  }
}

async function updatePost() {
  error.value = "";
  loading.value = true;
  try {
    const response = await fetch(`${BASE_URL}/posts/${slug}/edit`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(form.value),
    });
    if (!response.ok) throw new Error("Gagal mengupdate post");
    const data = await response.json();
    router.push(`/posts/${data.slug ?? slug}`);
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await Promise.all([fetchPost(), fetchCategories()]);
});
</script>
```

---

## 13. Buat Halaman Kategori

Buat file `src/views/CategoriesView.vue`:

```vue
<template>
  <div>
    <div class="page-header">
      <h2>Kategori</h2>
      <router-link to="/categories/create" class="btn btn-primary"
        >+ Tambah Kategori</router-link
      >
    </div>

    <p v-if="loading" class="loading">Memuat kategori...</p>
    <p v-if="error" class="error-msg">{{ error }}</p>

    <div v-if="!loading && categories.length === 0" class="empty">
      Belum ada kategori.
    </div>

    <div class="category-list">
      <div v-for="cat in categories" :key="cat.id" class="category-card">
        <div class="cat-info">
          <span class="cat-name">{{ cat.category_name }}</span>
          <span class="cat-count">{{ cat.posts_count }} post</span>
        </div>
        <div class="cat-actions">
          <router-link
            :to="`/categories/${cat.id}/edit`"
            class="btn btn-secondary"
            >Edit</router-link
          >
          <button class="btn btn-danger" @click="deleteCategory(cat)">
            Hapus
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const BASE_URL = import.meta.env.VITE_API_URL;
const token = localStorage.getItem("token");
const categories = ref([]);
const loading = ref(false);
const error = ref("");

async function fetchCategories() {
  loading.value = true;
  error.value = "";
  try {
    const response = await fetch(`${BASE_URL}/categories`, {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    if (!response.ok) throw new Error("Gagal memuat kategori");
    categories.value = await response.json();
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

async function deleteCategory(cat) {
  if (!confirm(`Hapus kategori "${cat.category_name}"?`)) return;
  try {
    const response = await fetch(`${BASE_URL}/categories/${cat.id}/delete`, {
      method: "POST",
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    const data = await response.json();
    if (!response.ok) {
      alert(data.message);
      return;
    }
    categories.value = categories.value.filter((c) => c.id !== cat.id);
  } catch (err) {
    alert("Gagal menghapus kategori");
  }
}

onMounted(fetchCategories);
</script>
```

---

## 14. Buat Halaman Tambah Kategori

Buat file `src/views/CategoryCreateView.vue`:

```vue
<template>
  <div>
    <h2>Tambah Kategori</h2>
    <div class="card" style="margin-top: 20px;">
      <div class="form-group">
        <label>Nama Kategori</label>
        <input
          v-model="categoryName"
          type="text"
          placeholder="Nama kategori..."
          @keyup.enter="createCategory"
        />
      </div>
      <p class="error-msg" v-if="error">{{ error }}</p>
      <div class="btn-row">
        <button class="btn btn-secondary" @click="router.back()">Batal</button>
        <button
          class="btn btn-primary"
          :disabled="loading"
          @click="createCategory"
        >
          {{ loading ? "Menyimpan..." : "Simpan" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const BASE_URL = import.meta.env.VITE_API_URL;
const token = localStorage.getItem("token");
const categoryName = ref("");
const loading = ref(false);
const error = ref("");

async function createCategory() {
  error.value = "";
  loading.value = true;
  try {
    const response = await fetch(`${BASE_URL}/categories`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({ category_name: categoryName.value }),
    });
    if (!response.ok) {
      const data = await response.json();
      throw new Error(data.message ?? "Gagal menambah kategori");
    }
    router.push("/categories");
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}
</script>
```

---

## 15. Buat Halaman Edit Kategori

Buat file `src/views/CategoryEditView.vue`:

```vue
<template>
  <div>
    <h2>Edit Kategori</h2>
    <p v-if="loading && !categoryName" class="loading">Memuat kategori...</p>
    <div class="card" style="margin-top: 20px;" v-if="categoryName || !loading">
      <div class="form-group">
        <label>Nama Kategori</label>
        <input
          v-model="categoryName"
          type="text"
          placeholder="Nama kategori..."
          @keyup.enter="updateCategory"
        />
      </div>
      <p class="error-msg" v-if="error">{{ error }}</p>
      <div class="btn-row">
        <button class="btn btn-secondary" @click="router.back()">Batal</button>
        <button
          class="btn btn-primary"
          :disabled="loading"
          @click="updateCategory"
        >
          {{ loading ? "Menyimpan..." : "Update" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const BASE_URL = import.meta.env.VITE_API_URL;
const token = localStorage.getItem("token");
const id = route.params.id;
const categoryName = ref("");
const loading = ref(false);
const error = ref("");

async function fetchCategory() {
  loading.value = true;
  try {
    const response = await fetch(`${BASE_URL}/categories`, {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    });
    if (!response.ok) throw new Error("Gagal memuat kategori");
    const data = await response.json();
    const category = data.find((c) => c.id == id);
    if (!category) throw new Error("Kategori tidak ditemukan");
    categoryName.value = category.category_name;
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

async function updateCategory() {
  error.value = "";
  loading.value = true;
  try {
    const response = await fetch(`${BASE_URL}/categories/${id}/edit`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({ category_name: categoryName.value }),
    });
    if (!response.ok) {
      const data = await response.json();
      throw new Error(data.message ?? "Gagal mengupdate kategori");
    }
    router.push("/categories");
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

onMounted(fetchCategory);
</script>
```

---

## 16. Buat Component Navbar

Buat folder `src/components/`, lalu buat file `src/components/Navbar.vue`:

```vue
<template>
  <nav v-if="isLoggedIn" class="navbar">
    <router-link to="/posts" class="brand">📝 Blog</router-link>
    <div class="nav-links">
      <router-link to="/posts">Posts</router-link>
      <router-link to="/categories">Kategori</router-link>
      <router-link to="/posts/create">+ New Post</router-link>
      <button @click="logout" class="btn-logout">Logout</button>
    </div>
  </nav>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const isLoggedIn = computed(() => !!localStorage.getItem("token"));

function logout() {
  if (!confirm("Yakin ingin logout?")) return;

  const token = localStorage.getItem("token");

  fetch("http://127.0.0.1:8001/api/logout", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
  });

  localStorage.removeItem("token");
  window.location.href = "/login";
}
</script>

<style scoped>
.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 24px;
  background: #1e1e2e;
  color: white;
  position: sticky;
  top: 0;
  z-index: 100;
}
.brand {
  font-size: 1.1rem;
  font-weight: bold;
  color: white;
  text-decoration: none;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 16px;
}
.nav-links a {
  color: #cdd6f4;
  text-decoration: none;
  font-size: 0.9rem;
}
.nav-links a:hover {
  color: white;
}
.nav-links a.router-link-active {
  color: #89b4fa;
  font-weight: 600;
}
.btn-logout {
  background: #f38ba8;
  color: white;
  border: none;
  padding: 6px 14px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.85rem;
}
.btn-logout:hover {
  background: #e66b84;
}
</style>
```

---

## 17. Update App.vue

Edit file `src/App.vue` menjadi:

```vue
<template>
  <div>
    <Navbar />
    <main class="container">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Navbar from "./components/Navbar.vue";
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
body {
  font-family: "Segoe UI", sans-serif;
  background: #f5f5f5;
  color: #222;
}
.container {
  max-width: 820px;
  margin: 32px auto;
  padding: 0 16px;
}

.card {
  background: white;
  border-radius: 12px;
  padding: 28px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}
.form-group {
  margin-bottom: 16px;
}
label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 6px;
  color: #555;
}
input,
select,
textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid #ddd;
  border-radius: 8px;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.2s;
  font-family: inherit;
}
input:focus,
select:focus,
textarea:focus {
  border-color: #89b4fa;
}
textarea {
  resize: vertical;
  min-height: 140px;
}

.btn {
  padding: 10px 22px;
  border: none;
  border-radius: 8px;
  font-size: 0.95rem;
  cursor: pointer;
  transition: opacity 0.2s;
}
.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.btn-primary {
  background: #1e1e2e;
  color: white;
}
.btn-primary:hover:not(:disabled) {
  background: #313244;
}
.btn-danger {
  background: #f38ba8;
  color: white;
}
.btn-danger:hover:not(:disabled) {
  background: #e66b84;
}
.btn-secondary {
  background: #e0e0e0;
  color: #333;
}
.btn-secondary:hover:not(:disabled) {
  background: #ccc;
}

.error-msg {
  color: #e66b84;
  font-size: 0.85rem;
  margin-top: 10px;
}
.loading {
  text-align: center;
  padding: 40px;
  color: #888;
}
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}
.btn-row {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 8px;
}
small {
  font-size: 0.78rem;
  color: #999;
  margin-top: 4px;
  display: block;
}
</style>
```

---

## 18. Jalankan Project

```bash
npm run dev
```

Buka browser ke `http://localhost:5173`

---

## ✅ Checklist Akhir

- [ ] Laravel API sudah berjalan di `http://127.0.0.1:8001`
- [ ] File `.env` sudah dibuat dengan `VITE_API_URL`
- [ ] `npm install vue-router` sudah dijalankan
- [ ] Semua file view sudah dibuat di `src/views/`
- [ ] Navbar sudah jadi component di `src/components/Navbar.vue`
- [ ] Router sudah didaftarkan di `src/main.js`

---

## 📁 Struktur File Akhir

```
src/
├── components/
│   └── Navbar.vue
├── views/
│   ├── LoginView.vue
│   ├── RegisterView.vue
│   ├── PostsView.vue
│   ├── PostDetailView.vue
│   ├── PostCreateView.vue
│   ├── PostEditView.vue
│   ├── CategoriesView.vue
│   ├── CategoryCreateView.vue
│   └── CategoryEditView.vue
├── router/
│   └── index.js
├── App.vue
└── main.js
```
