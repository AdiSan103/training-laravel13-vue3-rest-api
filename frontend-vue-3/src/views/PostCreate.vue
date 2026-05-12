<template>
  <div>
    <h2>Buat Post Baru</h2>

    <div class="card" style="margin-top: 20px">
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
const token = localStorage.getItem("token");

const categories = ref([]);
const loading = ref(false);
const error = ref("");

const form = ref({
  title: "",
  slug: "",
  category_id: "",
  content: "",
});

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
    const response = await fetch("http://127.0.0.1:8001/api/categories", {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
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
    const response = await fetch("http://127.0.0.1:8001/api/posts", {
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
    router.push(`/posts/${data.slug ?? data.post?.slug}`);
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}

onMounted(fetchCategories);
</script>

<style scoped>
h2 {
  font-size: 1.4rem;
}
small {
  font-size: 0.78rem;
  color: #999;
  margin-top: 4px;
  display: block;
}
.btn-row {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 8px;
}
</style>
