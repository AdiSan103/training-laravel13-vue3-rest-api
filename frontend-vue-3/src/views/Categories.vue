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

const BASE_URL = "http://127.0.0.1:8001/api";
const token = localStorage.getItem("token");

const categories = ref([]);
const loading = ref(false);
const error = ref("");

async function fetchCategories() {
  loading.value = true;
  error.value = "";

  try {
    const response = await fetch(`${BASE_URL}/categories`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
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
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
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

<style scoped>
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}
h2 {
  font-size: 1.5rem;
}

.category-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.category-card {
  background: white;
  border-radius: 10px;
  padding: 16px 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cat-info {
  display: flex;
  align-items: center;
  gap: 12px;
}
.cat-name {
  font-weight: 600;
  font-size: 0.95rem;
}

.cat-count {
  background: #e8f0fe;
  color: #3b5bdb;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 20px;
}

.cat-actions {
  display: flex;
  gap: 8px;
}
.empty {
  text-align: center;
  padding: 40px;
  color: #888;
}
</style>
