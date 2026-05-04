<template>
  <!--
    DashboardView.vue
    -----------------
    Halaman utama setelah login.
    Menampilkan:
    - Statistik singkat (total produk, total stok, dll)
    - Tabel daftar produk dengan filter dan delete
  -->
  <div class="page-content">
    <!-- Header halaman -->
    <header class="page-header flex items-center justify-between">
      <div>
        <h1>Dashboard</h1>
        <p class="text-muted text-sm">Kelola semua produk inventaris Anda</p>
      </div>
      <RouterLink to="/products/create" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 5v14m-7-7h14"/>
        </svg>
        Tambah Produk
      </RouterLink>
    </header>

    <!-- Statistik singkat -->
    <div class="stats-grid" v-if="!isLoading">
      <div class="stat-card">
        <p class="stat-label">Total Produk</p>
        <p class="stat-value">{{ products.length }}</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Total Stok</p>
        <p class="stat-value">{{ totalStock }}</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Stok Habis</p>
        <p class="stat-value" style="color: var(--destructive)">{{ outOfStock }}</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Stok Menipis</p>
        <p class="stat-value" style="color: var(--warning)">{{ lowStock }}</p>
      </div>
    </div>

    <!-- Card tabel produk -->
    <div class="card">
      <div class="card-header">
        <div>
          <h2 class="card-title">Daftar Produk</h2>
          <p class="card-description">{{ filteredProducts.length }} produk ditemukan</p>
        </div>

        <!-- Filter pencarian -->
        <div class="filter-bar">
          <div class="search-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon" aria-hidden="true">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input
              v-model="searchQuery"
              type="search"
              class="form-input search-input"
              placeholder="Cari produk..."
              aria-label="Cari produk"
            />
          </div>

          <!-- Filter stok -->
          <select v-model="stockFilter" class="form-input filter-select" aria-label="Filter stok">
            <option value="all">Semua Stok</option>
            <option value="available">Tersedia</option>
            <option value="low">Menipis (≤ 5)</option>
            <option value="empty">Habis</option>
          </select>
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="isLoading" class="loading-wrapper">
        <div class="spinner"></div>
        <p>Memuat data produk...</p>
      </div>

      <!-- Tabel produk -->
      <div v-else-if="filteredProducts.length > 0" class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th scope="col">Produk</th>
              <th scope="col">Harga</th>
              <th scope="col">Stok</th>
              <th scope="col">Status</th>
              <th scope="col" style="text-align:right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in filteredProducts" :key="product.id">
              <!-- Gambar + nama produk -->
              <td>
                <div class="flex items-center gap-3">
                  <img
                    v-if="product.image"
                    :src="getProductImageUrl(product.image)"
                    :alt="product.name"
                    class="product-image"
                  />
                  <div v-else class="product-image-placeholder" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                      <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-sm">{{ product.name }}</p>
                    <p class="text-xs text-muted">{{ product.description || 'Tanpa deskripsi' }}</p>
                  </div>
                </div>
              </td>

              <!-- Harga -->
              <td class="font-mono text-sm">{{ formatCurrency(product.price) }}</td>

              <!-- Stok -->
              <td class="font-mono text-sm">{{ product.stock }}</td>

              <!-- Badge status stok -->
              <td>
                <span class="badge" :class="getStockBadgeClass(product.stock)">
                  {{ getStockLabel(product.stock) }}
                </span>
              </td>

              <!-- Tombol aksi -->
              <td>
                <div class="flex items-center gap-2" style="justify-content:flex-end;">
                  <RouterLink
                    :to="`/products/${product.id}`"
                    class="btn btn-ghost btn-sm"
                    title="Lihat detail"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                  </RouterLink>
                  <RouterLink
                    :to="`/products/${product.id}/edit`"
                    class="btn btn-ghost btn-sm"
                    title="Edit produk"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                    </svg>
                  </RouterLink>
                  <button
                    class="btn btn-ghost btn-sm"
                    style="color: var(--destructive);"
                    @click="openDeleteModal(product)"
                    title="Hapus produk"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty state: tidak ada produk sama sekali -->
      <div v-else-if="products.length === 0" class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M5 8h14M5 8a2 2 0 1 0-4 0v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8m-4-4v4M9 4v4"/>
        </svg>
        <p style="font-weight:500;">Belum ada produk</p>
        <p>Klik tombol "Tambah Produk" untuk memulai.</p>
        <RouterLink to="/products/create" class="btn btn-primary mt-4">Tambah Produk</RouterLink>
      </div>

      <!-- Empty state: hasil filter kosong -->
      <div v-else class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
        </svg>
        <p style="font-weight:500;">Tidak ada hasil</p>
        <p>Coba ubah kata kunci atau filter pencarian.</p>
      </div>
    </div>

    <!-- Modal konfirmasi hapus -->
    <ConfirmModal
      :isOpen="showDeleteModal"
      title="Hapus Produk"
      :message="`Apakah Anda yakin ingin menghapus produk &quot;${selectedProduct?.name}&quot;? Tindakan ini tidak dapat dibatalkan.`"
      confirmText="Hapus"
      :loading="isDeleting"
      @confirm="handleDelete"
      @cancel="closeDeleteModal"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import productService, { getProductImageUrl } from '../services/productService.js'
import { useAlert } from '../composables/useAlert.js'
import ConfirmModal from '../components/ConfirmModal.vue'

const { success, error } = useAlert()

// State utama
const products       = ref([])
const isLoading      = ref(true)
const searchQuery    = ref('')
const stockFilter    = ref('all')
const showDeleteModal = ref(false)
const selectedProduct = ref(null)
const isDeleting      = ref(false)

// =============================================
// COMPUTED: Statistik
// =============================================

const totalStock = computed(() =>
  products.value.reduce((sum, p) => sum + p.stock, 0)
)

const outOfStock = computed(() =>
  products.value.filter(p => p.stock === 0).length
)

const lowStock = computed(() =>
  products.value.filter(p => p.stock > 0 && p.stock <= 5).length
)

// =============================================
// COMPUTED: Filter produk
// Filter berjalan di sisi client (statis pada tabel)
// =============================================
const filteredProducts = computed(() => {
  let result = products.value

  // Filter berdasarkan pencarian nama/deskripsi
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(
      p => p.name.toLowerCase().includes(q) ||
           (p.description && p.description.toLowerCase().includes(q))
    )
  }

  // Filter berdasarkan status stok
  if (stockFilter.value === 'available') {
    result = result.filter(p => p.stock > 5)
  } else if (stockFilter.value === 'low') {
    result = result.filter(p => p.stock > 0 && p.stock <= 5)
  } else if (stockFilter.value === 'empty') {
    result = result.filter(p => p.stock === 0)
  }

  return result
})

// =============================================
// HELPERS
// =============================================

/**
 * Format angka ke format Rupiah.
 * Contoh: 10000 → "Rp 10.000"
 */
function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(value)
}

/** Tentukan class badge berdasarkan jumlah stok */
function getStockBadgeClass(stock) {
  if (stock === 0)  return 'badge-destructive'
  if (stock <= 5)   return 'badge-warning'
  return 'badge-success'
}

/** Tentukan label badge berdasarkan jumlah stok */
function getStockLabel(stock) {
  if (stock === 0) return 'Habis'
  if (stock <= 5)  return 'Menipis'
  return 'Tersedia'
}

// =============================================
// DATA FETCHING
// =============================================

/** Ambil semua produk dari API saat komponen pertama kali dimuat */
async function fetchProducts() {
  isLoading.value = true
  const { data, error: err } = await productService.getAll()

  if (err) {
    error('Gagal memuat produk', err)
  } else {
    // data.data adalah array produk dari response Laravel
    products.value = data.data || []
  }

  isLoading.value = false
}

// =============================================
// DELETE
// =============================================

function openDeleteModal(product) {
  selectedProduct.value = product
  showDeleteModal.value  = true
}

function closeDeleteModal() {
  showDeleteModal.value  = false
  selectedProduct.value  = null
}

async function handleDelete() {
  if (!selectedProduct.value) return

  isDeleting.value = true

  const { error: err } = await productService.remove(selectedProduct.value.id)

  if (err) {
    error('Gagal menghapus', err)
  } else {
    // Hapus dari array lokal tanpa perlu fetch ulang
    products.value = products.value.filter(p => p.id !== selectedProduct.value.id)
    success('Produk dihapus', `"${selectedProduct.value.name}" berhasil dihapus.`)
    closeDeleteModal()
  }

  isDeleting.value = false
}

// Panggil saat komponen dimount
onMounted(fetchProducts)
</script>

<style scoped>
/* Filter bar di header card */
.filter-bar {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.search-wrapper {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted-foreground);
  pointer-events: none;
}

.search-input {
  padding-left: 2.25rem;
  width: 220px;
}

.filter-select {
  width: auto;
  min-width: 140px;
}

/* Responsive: filter menjadi full width di layar kecil */
@media (max-width: 640px) {
  .card-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .filter-bar {
    width: 100%;
  }

  .search-input {
    width: 100%;
  }

  .filter-select {
    width: 100%;
  }
}
</style>
