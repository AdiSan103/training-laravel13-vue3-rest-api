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
