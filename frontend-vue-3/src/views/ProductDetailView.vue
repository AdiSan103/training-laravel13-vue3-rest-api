<template>
  <!--
    ProductDetailView.vue
    ---------------------
    Menampilkan detail lengkap satu produk berdasarkan ID dari URL.
    Menggunakan Route Model Binding lewat params.id.
  -->
  <div class="page-content">
    <!-- Tombol kembali -->
    <div style="margin-bottom:1.25rem;">
      <RouterLink to="/dashboard" class="btn btn-ghost btn-sm" style="padding-left:0;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>
        </svg>
        Kembali ke Dashboard
      </RouterLink>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading" class="loading-wrapper">
      <div class="spinner"></div>
      <p>Memuat detail produk...</p>
    </div>

    <!-- Error state (produk tidak ditemukan) -->
    <div v-else-if="!product" class="empty-state">
      <p style="font-weight:500;">Produk tidak ditemukan</p>
      <RouterLink to="/dashboard" class="btn btn-primary mt-4">Kembali</RouterLink>
    </div>

    <!-- Konten detail produk -->
    <div v-else>
      <div class="detail-layout">
        <!-- Gambar produk -->
        <aside class="detail-image-col">
          <div class="card">
            <div class="card-body" style="padding:1rem;">
              <img
                v-if="product.image"
                :src="getProductImageUrl(product.image)"
                :alt="product.name"
                class="detail-image"
              />
              <div v-else class="detail-image-placeholder" aria-label="Tidak ada gambar">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                </svg>
                <p>Tidak ada gambar</p>
              </div>
            </div>
          </div>
        </aside>

        <!-- Info produk -->
        <section class="detail-info-col">
          <div class="card">
            <!-- Header card dengan tombol aksi -->
            <div class="card-header">
              <div>
                <h1 class="card-title" style="font-size:1.125rem;">{{ product.name }}</h1>
                <p class="card-description">ID: #{{ product.id }}</p>
              </div>
              <div class="flex items-center gap-2">
                <RouterLink :to="`/products/${product.id}/edit`" class="btn btn-outline btn-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                  </svg>
                  Edit
                </RouterLink>
                <button class="btn btn-destructive btn-sm" @click="showDeleteModal = true">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                  </svg>
                  Hapus
                </button>
              </div>
            </div>

            <div class="card-body">
              <!-- Detail rows -->
              <dl class="detail-list">
                <div class="detail-row">
                  <dt>Harga</dt>
                  <dd class="font-mono" style="font-size:1.125rem;font-weight:600;">
                    {{ formatCurrency(product.price) }}
                  </dd>
                </div>
                <div class="detail-row">
                  <dt>Stok</dt>
                  <dd>
                    <span class="font-mono" style="font-size:1rem;font-weight:600;">{{ product.stock }}</span>
                    <span class="badge ml-2" :class="getStockBadgeClass(product.stock)">
                      {{ getStockLabel(product.stock) }}
                    </span>
                  </dd>
                </div>
                <div class="detail-row">
                  <dt>Deskripsi</dt>
                  <dd>{{ product.description || 'Tidak ada deskripsi' }}</dd>
                </div>
                <div class="detail-row">
                  <dt>Dibuat</dt>
                  <dd class="text-muted text-sm">{{ formatDate(product.created_at) }}</dd>
                </div>
                <div class="detail-row">
                  <dt>Diperbarui</dt>
                  <dd class="text-muted text-sm">{{ formatDate(product.updated_at) }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </section>
      </div>
    </div>

    <!-- Modal konfirmasi hapus -->
    <ConfirmModal
      :isOpen="showDeleteModal"
      title="Hapus Produk"
      :message="`Apakah Anda yakin ingin menghapus &quot;${product?.name}&quot;?`"
      confirmText="Hapus"
      :loading="isDeleting"
      @confirm="handleDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import productService, { getProductImageUrl } from '../services/productService.js'
import { useAlert } from '../composables/useAlert.js'
import ConfirmModal from '../components/ConfirmModal.vue'

const route  = useRoute()
const router = useRouter()
const { success, error } = useAlert()

const product       = ref(null)
const isLoading     = ref(true)
const showDeleteModal = ref(false)
const isDeleting    = ref(false)

// Ambil ID dari parameter URL (/products/:id)
const productId = route.params.id

/** Ambil detail produk dari API */
async function fetchProduct() {
  isLoading.value = true
  const { data, error: err } = await productService.getOne(productId)

  if (err) {
    error('Gagal memuat produk', err)
  } else {
    product.value = data.data
  }

  isLoading.value = false
}

async function handleDelete() {
  isDeleting.value = true
  const { error: err } = await productService.remove(productId)

  if (err) {
    error('Gagal menghapus', err)
  } else {
    success('Produk dihapus', `"${product.value.name}" berhasil dihapus.`)
    router.push('/dashboard')
  }

  isDeleting.value = false
}

/** Format tanggal ISO ke format lokal Indonesia */
function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
  }).format(value)
}

function getStockBadgeClass(stock) {
  if (stock === 0) return 'badge-destructive'
  if (stock <= 5)  return 'badge-warning'
  return 'badge-success'
}

function getStockLabel(stock) {
  if (stock === 0) return 'Habis'
  if (stock <= 5)  return 'Menipis'
  return 'Tersedia'
}

onMounted(fetchProduct)
</script>

<style scoped>
/* Layout 2 kolom: gambar | info */
.detail-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 1.25rem;
  align-items: start;
}

.detail-image {
  width: 100%;
  aspect-ratio: 1;
  object-fit: cover;
  border-radius: var(--radius-sm);
}

.detail-image-placeholder {
  width: 100%;
  aspect-ratio: 1;
  background-color: var(--muted);
  border-radius: var(--radius-sm);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  color: var(--muted-foreground);
  font-size: 0.875rem;
}

/* Definition list untuk detail info */
.detail-list {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.detail-row {
  display: grid;
  grid-template-columns: 120px 1fr;
  gap: 1rem;
  padding: 0.875rem 0;
  border-bottom: 1px solid var(--border);
  align-items: baseline;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row dt {
  font-size: 0.8125rem;
  font-weight: 500;
  color: var(--muted-foreground);
}

.detail-row dd {
  font-size: 0.875rem;
}

.ml-2 { margin-left: 0.5rem; }

@media (max-width: 768px) {
  .detail-layout {
    grid-template-columns: 1fr;
  }

  .detail-image {
    max-height: 250px;
  }
}
</style>
