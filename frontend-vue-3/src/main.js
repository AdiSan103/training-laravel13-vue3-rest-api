/**
 * main.js
 * -------
 * Entry point aplikasi Vue 3.
 *
 * Urutan inisialisasi:
 * 1. Import CSS global
 * 2. Buat instance aplikasi Vue
 * 3. Pasang Vue Router
 * 4. Mount ke elemen #app di index.html
 */

import "./assets/main.css"; // CSS global (variabel, reset, komponen)
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router/index.js";

// Buat instance aplikasi dari komponen root App.vue
const app = createApp(App);

// Pasang Vue Router agar <RouterLink> dan <RouterView> bisa dipakai
app.use(router);

// Mount ke div#app di index.html
app.mount("#app");
