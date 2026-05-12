<template>
  <div>
    <h2>Edit Post</h2>

    <p v-if="loading && !form.title" class="loading">Memuat data...</p>

    <div class="card" style="margin-top: 20px" v-if="form.title || !loading">
      <div class="form-group">
        <label>Judul</label>
        <input v-model="form.title" type="text" placeholder="Judul post..." />
      </div>

      <div class="form-group">
        <label>Slug</label>
        <input v-model="form.slug" type="text" placeholder="judul-post-anda" />
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
const slug = route.params.slug;
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

async function fetchPost() {
  loading.value = true;
  error.value = "";

  try {
    const response = await fetch(`http://127.0.0.1:8001/api/posts/${slug}`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
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

async function updatePost() {
  error.value = "";
  loading.value = true;

  try {
    const response = await fetch(
      `http://127.0.0.1:8001/api/posts/${slug}/edit`,
      {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(form.value),
      },
    );

    if (!response.ok) throw new Error("Gagal mengupdate post");

    const data = await response.json();
    router.push(`/posts/${data.slug ?? data.post?.slug ?? slug}`);
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

<style scoped>
h2 {
  font-size: 1.4rem;
}
.btn-row {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 8px;
}
</style>
