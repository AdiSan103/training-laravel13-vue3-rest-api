<template>
  <div class="auth-wrapper">
    <div class="card">
      <h2>Login</h2>

      <div class="form-group">
        <label>Email</label>
        <input v-model="email" type="email" placeholder="email@example.com" />
      </div>

      <div class="form-group">
        <label>Password</label>
        <input
          v-model="password"
          type="password"
          placeholder="••••••••"
          @keyup.enter="login"
        />
      </div>

      <button class="btn btn-primary" :disabled="loading" @click="login">
        {{ loading ? "Loading..." : "Login" }}
      </button>

      <p class="error-msg" v-if="error">{{ error }}</p>

      <p class="switch-link">
        Belum punya akun? <router-link to="/register">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const email = ref("");
const password = ref("");
const loading = ref(false);
const error = ref("");

async function login() {
  error.value = "";
  loading.value = true;

  try {
    const response = await fetch("http://127.0.0.1:8001/api/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value,
      }),
    });

    if (!response.ok) throw new Error("Email atau password salah");

    const data = await response.json();
    localStorage.setItem("token", data.token);
    window.location.href = "/posts";
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.auth-wrapper {
  max-width: 420px;
  margin: 60px auto;
}
h2 {
  margin-bottom: 24px;
  font-size: 1.5rem;
}
.switch-link {
  margin-top: 16px;
  font-size: 0.88rem;
  color: #666;
}
.switch-link a {
  color: #89b4fa;
}
</style>
