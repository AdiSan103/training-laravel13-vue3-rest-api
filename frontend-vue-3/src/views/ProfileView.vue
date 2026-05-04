<template>
  <!--
    ProfileView.vue
    ---------------
    Menampilkan data user yang sedang login dan form untuk update profil.
    Bisa update: nama, password, dan avatar (foto profil).
  -->
  <div class="page-content">
    <header class="page-header">
      <h1>Profil Saya</h1>
      <p class="text-muted text-sm">Lihat dan perbarui informasi akun Anda</p>
    </header>

    <!-- Loading state -->
    <div v-if="isLoading" class="loading-wrapper">
      <div class="spinner"></div>
      <p>Memuat profil...</p>
    </div>

    <div v-else class="profile-layout">
      <!-- Kartu info user (kiri) -->
      <aside>
        <div class="card">
          <div class="card-body" style="text-align:center;padding:2rem 1.5rem;">
            <!-- Avatar user -->
            <div style="display:flex;justify-content:center;margin-bottom:1rem;">
              <img
                v-if="user?.avatar"
                :src="getAvatarUrl(user.avatar)"
                :alt="`Avatar ${user.name}`"
                class="avatar"
              />
              <!-- Placeholder avatar: inisial nama -->
              <div v-else class="avatar-placeholder" aria-hidden="true">
                {{ getInitials(user?.name) }}
              </div>
            </div>

            <h2 style="font-size:1rem;font-weight:600;">{{ user?.name }}</h2>
            <p class="text-sm text-muted" style="margin-top:0.25rem;">{{ user?.email }}</p>

            <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);">
              <dl style="display:flex;flex-direction:column;gap:0.75rem;text-align:left;">
                <div>
                  <dt class="text-xs text-muted" style="font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">ID</dt>
                  <dd class="font-mono text-sm" style="margin-top:0.125rem;">#{{ user?.id }}</dd>
                </div>
                <div>
                  <dt class="text-xs text-muted" style="font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Bergabung</dt>
                  <dd class="text-sm" style="margin-top:0.125rem;">{{ formatDate(user?.created_at) }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </aside>

      <!-- Form update profil (kanan) -->
      <section>
        <div class="card">
          <div class="card-header">
            <div>
              <h2 class="card-title">Edit Profil</h2>
              <p class="card-description">Kosongkan password jika tidak ingin mengubahnya</p>
            </div>
          </div>

          <form @submit.prevent="handleUpdate" novalidate>
            <div class="card-body" style="display:flex;flex-direction:column;gap:1.25rem;">

              <!-- Nama -->
              <div class="form-group">
                <label for="name" class="form-label">Nama</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="form-input"
                  placeholder="Nama lengkap"
                />
              </div>

              <!-- Password baru -->
              <div class="form-group">
                <label for="password" class="form-label">Password Baru</label>
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  class="form-input"
                  placeholder="Kosongkan jika tidak ingin ubah"
                  autocomplete="new-password"
                />
                <p class="form-hint">Minimal 6 karakter.</p>
              </div>

              <!-- Konfirmasi password -->
              <div class="form-group" v-if="form.password">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  class="form-input"
                  placeholder="Ulangi password baru"
                  autocomplete="new-password"
                />
              </div>

              <!-- Upload avatar -->
              <div class="form-group">
                <label for="avatar" class="form-label">Foto Profil</label>
                <input
                  id="avatar"
                  type="file"
                  class="form-input"
                  accept="image/jpg,image/jpeg,image/png"
                  @change="handleAvatarChange"
                />
                <p class="form-hint">Format: JPG, PNG. Maks: 2MB.</p>

                <!-- Preview avatar baru -->
                <img
                  v-if="avatarPreview"
                  :src="avatarPreview"
                  alt="Preview avatar baru"
                  style="width:80px;height:80px;border-radius:9999px;object-fit:cover;border:2px solid var(--border);margin-top:0.75rem;"
                />
              </div>

              <!-- Error dari API -->
              <p v-if="errorMsg" class="form-error" role="alert">{{ errorMsg }}</p>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner" style="width:14px;height:14px;border-width:2px;border-color:rgba(255,255,255,0.3);border-top-color:white;"></span>
                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import authService from '../services/authService.js'
import { getAvatarUrl } from '../services/productService.js'
import { useAuth } from '../composables/useAuth.js'
import { useAlert } from '../composables/useAlert.js'

const { currentUser, setCurrentUser } = useAuth()
const { success, error } = useAlert()

const user        = ref(null)
const isLoading   = ref(true)
const isSubmitting = ref(false)
const errorMsg    = ref('')
const avatarPreview = ref(null)

// Form update profil — semua field opsional
const form = reactive({
  name: '',
  password: '',
  password_confirmation: '',
  avatar: null,
})

/** Ambil initials dari nama untuk placeholder avatar */
function getInitials(name) {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase()
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
  })
}

/** Handle perubahan input file avatar */
function handleAvatarChange(event) {
  const file = event.target.files[0]
  if (!file) return

  form.avatar = file

  // Preview avatar sebelum upload
  const reader = new FileReader()
  reader.onload = (e) => { avatarPreview.value = e.target.result }
  reader.readAsDataURL(file)
}

/** Ambil data user dari API */
async function fetchProfile() {
  isLoading.value = true
  const { data, error: err } = await authService.getMe()

  if (err) {
    error('Gagal memuat profil', err)
  } else {
    user.value   = data.data
    form.name    = data.data.name
  }

  isLoading.value = false
}

/**
 * Proses update profil.
 * Hanya kirim field yang diisi.
 */
async function handleUpdate() {
  errorMsg.value = ''

  // Validasi jika password diisi
  if (form.password) {
    if (form.password.length < 6) {
      errorMsg.value = 'Password minimal 6 karakter.'
      return
    }
    if (form.password !== form.password_confirmation) {
      errorMsg.value = 'Password dan konfirmasi tidak sama.'
      return
    }
  }

  isSubmitting.value = true

  // Siapkan payload — hanya kirim field yang ada isinya
  const payload = {}
  if (form.name)     payload.name = form.name
  if (form.password) {
    payload.password              = form.password
    payload.password_confirmation = form.password_confirmation
  }
  if (form.avatar)   payload.avatar = form.avatar

  const { data, error: err } = await authService.updateProfile(payload)

  if (err) {
    errorMsg.value = err
  } else {
    // Update state user global agar sidebar ikut terupdate
    user.value = data.data
    setCurrentUser(data.data)

    // Reset field password setelah berhasil
    form.password              = ''
    form.password_confirmation = ''
    form.avatar                = null
    avatarPreview.value        = null

    success('Profil diperbarui', 'Perubahan profil berhasil disimpan.')
  }

  isSubmitting.value = false
}

onMounted(fetchProfile)
</script>

<style scoped>
/* Layout 2 kolom: info user | form edit */
.profile-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 1.25rem;
  align-items: start;
}

@media (max-width: 768px) {
  .profile-layout {
    grid-template-columns: 1fr;
  }
}
</style>
