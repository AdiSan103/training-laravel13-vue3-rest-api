<template>
  <!--
    ProductFormView.vue
    -------------------
    Dipakai untuk DUA keperluan: Buat produk baru & Edit produk.
    Perbedaannya hanya pada:
    - Apakah ada params.id di URL
    - Judul halaman dan teks tombol submit
    - Fungsi yang dipanggil (create vs update)
  -->
  <div class="page-content">
    <!-- Tombol kembali -->
    <div style="margin-bottom:1.25rem;">
      <RouterLink to="/dashboard" class="btn btn-ghost btn-sm" style="padding-left:0;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>
        </svg>
        Kembali
      </RouterLink>
    </div>

    <header class="page-header">
      <h1>{{ isEditMode ? 'Edit Produk' : 'Tambah Produk' }}</h1>
      <p class="text-muted text-sm">
        {{ isEditMode ? 'Perbarui informasi produk yang sudah ada' : 'Isi form untuk menambahkan produk baru' }}
      </p>
    </header>

    <!-- Loading data produk (mode edit) -->
    <div v-if="isFetching" class="loading-wrapper">
      <div class="spinner"></div>
      <p>Memuat data produk...</p>
    </div>

    <!-- Form produk -->
    <div v-else class="card" style="max-width:700px;">
      <div class="card-header">
        <h2 class="card-title">Informasi Produk</h2>
      </div>

      <form @submit.prevent="handleSubmit" novalidate>
        <div class="card-body" style="display:flex;flex-direction:column;gap:1.25rem;">

          <!-- Nama produk -->
          <div class="form-group">
            <label for="name" class="form-label required">Nama Produk</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="form-input"
              placeholder="Contoh: Laptop ASUS VivoBook"
              required
            />
          </div>

          <!-- Deskripsi -->
          <div class="form-group">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea
              id="description"
              v-model="form.description"
              class="form-input form-textarea"
              placeholder="Deskripsi singkat produk (opsional)"
            ></textarea>
          </div>

          <!-- Harga & Stok dalam 2 kolom -->
          <div class="form-grid">
            <div class="form-group">
              <label for="price" class="form-label required">Harga (Rp)</label>
              <input
                id="price"
                v-model="form.price"
                type="number"
                class="form-input"
                placeholder="10000"
                min="0"
                required
              />
            </div>
            <div class="form-group">
              <label for="stock" class="form-label required">Stok</label>
              <input
                id="stock"
                v-model="form.stock"
                type="number"
                class="form-input"
                placeholder="10"
                min="0"
                required
              />
            </div>
          </div>

          <!-- Upload gambar -->
          <div class="form-group">
            <label for="image" class="form-label">Gambar Produk</label>
            <input
              id="image"
              type="file"
              class="form-input"
              accept="image/jpg,image/jpeg,image/png"
              @change="handleImageChange"
            />
            <p class="form-hint">Format: JPG, PNG. Maks: 2MB.</p>

            <!-- Preview gambar baru yang dipilih -->
            <img
              v-if="imagePreview"
              :src="imagePreview"
              alt="Preview gambar produk"
              class="image-preview"
            />
            <!-- Gambar lama (mode edit, sebelum ganti) -->
            <div v-else-if="isEditMode && currentImageUrl" style="margin-top:0.5rem;">
              <p class="text-xs text-muted mb-1">Gambar saat ini:</p>
              <img :src="currentImageUrl" alt="Gambar produk saat ini" class="image-preview" />
            </div>
          </div>

          <!-- Error dari API -->
          <p v-if="errorMsg" class="form-error" role="alert">{{ errorMsg }}</p>
        </div>

        <!-- Footer form dengan tombol -->
        <div class="card-footer">
          <RouterLink to="/dashboard" class="btn btn-secondary">Batal</RouterLink>
          <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
            <span v-if="isSubmitting" class="spinner" style="width:14px;height:14px;border-width:2px;border-color:rgba(255,255,255,0.3);border-top-color:white;"></span>
            {{ isSubmitting ? 'Menyimpan...' : (isEditMode ? 'Simpan Perubahan' : 'Tambah Produk') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import productService, { getProductImageUrl } from '../services/productService.js'
import { useAlert } from '../composables/useAlert.js'

const route  = useRoute()
const router = useRouter()
const { success, error } = useAlert()

// =============================================
// Mode: Edit atau Create?
// Jika ada params.id di URL → mode edit
// =============================================
const isEditMode = computed(() => !!route.params.id)
const productId  = route.params.id

// State form
const form = reactive({
  name: '',
  description: '',
  price: '',
  stock: '',
  image: null, // File object dari input file
})

const isFetching    = ref(false) // loading data produk (mode edit)
const isSubmitting  = ref(false) // loading saat submit form
const errorMsg      = ref('')
const imagePreview  = ref(null)  // URL preview gambar baru
const currentImageUrl = ref(null) // URL gambar lama (mode edit)

/**
 * Handle perubahan input file gambar.
 * Buat preview menggunakan FileReader.
 */
function handleImageChange(event) {
  const file = event.target.files[0]
  if (!file) {
    form.image   = null
    imagePreview.value = null
    return
  }

  form.image = file

  // Buat URL sementara untuk preview gambar
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

/**
 * Jika mode edit: ambil data produk dulu lalu isi form.
 */
async function fetchProductData() {
  if (!isEditMode.value) return

  isFetching.value = true

  const { data, error: err } = await productService.getOne(productId)

  if (err) {
    error('Gagal memuat produk', err)
  } else {
    const p = data.data
    // Isi form dengan data produk yang ada
    form.name        = p.name
    form.description = p.description || ''
    form.price       = p.price
    form.stock       = p.stock

    // Simpan URL gambar lama untuk ditampilkan jika belum ganti gambar
    if (p.image) {
      currentImageUrl.value = getProductImageUrl(p.image)
    }
  }

  isFetching.value = false
}

/**
 * Submit form: panggil create atau update tergantung mode.
 */
async function handleSubmit() {
  errorMsg.value = ''

  // Validasi sederhana di sisi client
  if (!form.name || !form.price || !form.stock) {
    errorMsg.value = 'Nama, harga, dan stok wajib diisi.'
    return
  }

  isSubmitting.value = true

  let result

  if (isEditMode.value) {
    // Mode edit: panggil update
    result = await productService.update(productId, form)
  } else {
    // Mode create: panggil create
    result = await productService.create(form)
  }

  if (result.error) {
    errorMsg.value = result.error
  } else {
    success(
      isEditMode.value ? 'Produk diperbarui' : 'Produk ditambahkan',
      `"${form.name}" berhasil ${isEditMode.value ? 'diperbarui' : 'ditambahkan'}.`
    )
    router.push('/dashboard')
  }

  isSubmitting.value = false
}

onMounted(fetchProductData)
</script>
