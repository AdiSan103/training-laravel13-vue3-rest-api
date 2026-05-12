<template>
  <div>
    <h2>Edit Kategori</h2>

    <p v-if="loading && !categoryName" class="loading">Memuat kategori...</p>

    <div class="card" style="margin-top: 20px" v-if="categoryName || !loading">
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
const BASE_URL = "http://127.0.0.1:8001/api";
const token = localStorage.getItem("token");
const id = route.params.id;

const categoryName = ref("");
const loading = ref(false);
const error = ref("");

async function fetchCategory() {
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
