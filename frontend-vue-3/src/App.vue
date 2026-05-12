<template>
  <div>
    <Navbar />
    <main class="container">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Navbar from "./components/Navbar.vue";
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: "Segoe UI", sans-serif;
  background: #f5f5f5;
  color: #222;
}

.container {
  max-width: 820px;
  margin: 32px auto;
  padding: 0 16px;
}

.card {
  background: white;
  border-radius: 12px;
  padding: 28px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.form-group {
  margin-bottom: 16px;
}

label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 6px;
  color: #555;
}

input,
select,
textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid #ddd;
  border-radius: 8px;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.2s;
  font-family: inherit;
}
input:focus,
select:focus,
textarea:focus {
  border-color: #89b4fa;
}
textarea {
  resize: vertical;
  min-height: 140px;
}

.btn {
  padding: 10px 22px;
  border: none;
  border-radius: 8px;
  font-size: 0.95rem;
  cursor: pointer;
  transition: opacity 0.2s;
}
.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.btn-primary {
  background: #1e1e2e;
  color: white;
}
.btn-primary:hover:not(:disabled) {
  background: #313244;
}
.btn-danger {
  background: #f38ba8;
  color: white;
}
.btn-danger:hover:not(:disabled) {
  background: #e66b84;
}
.btn-secondary {
  background: #e0e0e0;
  color: #333;
}
.btn-secondary:hover:not(:disabled) {
  background: #ccc;
}

.error-msg {
  color: #e66b84;
  font-size: 0.85rem;
  margin-top: 10px;
}
.loading {
  text-align: center;
  padding: 40px;
  color: #888;
}
</style>
