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

const posts = ref([]);
const loading = ref(false);
const error = ref("");

const token = localStorage.getItem("token");

async function fetchPosts() {
  loading.value = true;
  error.value = "";

  try {
    const response = await fetch("http://127.0.0.1:8001/api/posts", {
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
    const response = await fetch(
      `http://127.0.0.1:8001/api/posts/${slug}/delete`,
      {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      },
    );

    if (!response.ok) throw new Error("Gagal menghapus post");

    // hapus dari list tanpa reload
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

<style scoped>
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}
h2 {
  font-size: 1.5rem;
}

.post-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.post-card {
  background: white;
  border-radius: 12px;
  padding: 20px 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.07);
  transition: transform 0.15s;
}
.post-card:hover {
  transform: translateY(-2px);
}

.post-meta {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 8px;
}

.category {
  background: #e8f0fe;
  color: #3b5bdb;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 20px;
}

.date {
  font-size: 0.8rem;
  color: #999;
}

h3 {
  font-size: 1.1rem;
  margin-bottom: 8px;
}

.excerpt {
  color: #666;
  font-size: 0.9rem;
  line-height: 1.5;
  margin-bottom: 16px;
}

.post-actions {
  display: flex;
  gap: 8px;
}

.empty {
  text-align: center;
  padding: 60px;
  color: #888;
}
.empty a {
  color: #89b4fa;
}
</style>
