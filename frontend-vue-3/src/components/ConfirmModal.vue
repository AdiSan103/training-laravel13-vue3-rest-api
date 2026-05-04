<template>
  <!--
    ConfirmModal.vue
    ----------------
    Modal dialog untuk konfirmasi aksi destruktif (misalnya: hapus produk).
    Harus ada konfirmasi user sebelum data benar-benar dihapus.
  -->
  <Teleport to="body">
    <!-- Teleport: render modal langsung ke <body> agar tidak terhalang z-index parent -->
    <div
      v-if="isOpen"
      class="modal-overlay"
      @click.self="$emit('cancel')"
      role="dialog"
      aria-modal="true"
      :aria-labelledby="`modal-title-${uid}`"
    >
      <div class="modal">
        <div class="modal-header">
          <h2 class="modal-title" :id="`modal-title-${uid}`">
            {{ title }}
          </h2>
        </div>

        <div class="modal-body">
          {{ message }}
        </div>

        <div class="modal-footer">
          <!-- Tombol batal -->
          <button class="btn btn-secondary" @click="$emit('cancel')" :disabled="loading">
            Batal
          </button>

          <!-- Tombol konfirmasi (warna merah karena aksi destruktif) -->
          <button class="btn btn-destructive" @click="$emit('confirm')" :disabled="loading">
            <span v-if="loading" class="spinner" style="width:14px;height:14px;border-width:2px;border-color:rgba(255,255,255,0.3);border-top-color:white;"></span>
            {{ loading ? 'Menghapus...' : confirmText }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
// Unique ID untuk aksesibilitas (aria-labelledby)
const uid = Math.random().toString(36).slice(2, 7)

defineProps({
  isOpen:      { type: Boolean, default: false },
  title:       { type: String, default: 'Konfirmasi' },
  message:     { type: String, default: 'Apakah Anda yakin?' },
  confirmText: { type: String, default: 'Hapus' },
  loading:     { type: Boolean, default: false },
})

defineEmits(['confirm', 'cancel'])
</script>
