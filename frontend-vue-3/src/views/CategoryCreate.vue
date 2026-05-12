<template>
  <div>
    <h2>Tambah Kategori</h2>

    <div class="card" style="margin-top: 20px">
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
const BASE_URL = "http://127.0.0.1:8001/api";
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
