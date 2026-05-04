/**
 * router/index.js
 * ---------------
 * Konfigurasi Vue Router.
 *
 * Fitur:
 * - Route protection: halaman tertentu hanya bisa diakses jika sudah login
 * - Redirect otomatis ke /login jika belum punya token
 * - Redirect otomatis ke /dashboard jika sudah login tapi buka /login
 */

import { createRouter, createWebHistory } from 'vue-router'
import { getToken } from '../services/api.js'

// Lazy loading: komponen hanya di-load saat dibutuhkan
// Ini membuat initial load lebih cepat
const LoginView      = () => import('../views/LoginView.vue')
const RegisterView   = () => import('../views/RegisterView.vue')
const DashboardView  = () => import('../views/DashboardView.vue')
const ProductDetail  = () => import('../views/ProductDetailView.vue')
const ProductForm    = () => import('../views/ProductFormView.vue')
const ProfileView    = () => import('../views/ProfileView.vue')

const routes = [
  // ============================
  // PUBLIC ROUTES (tanpa login)
  // ============================
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { requiresGuest: true }, // hanya untuk tamu (belum login)
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterView,
    meta: { requiresGuest: true },
  },

  // ==============================
  // PROTECTED ROUTES (perlu login)
  // ==============================
  {
    path: '/',
    redirect: '/dashboard', // redirect root ke dashboard
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/create',
    name: 'ProductCreate',
    component: ProductForm,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/:id',
    name: 'ProductDetail',
    component: ProductDetail,
    meta: { requiresAuth: true },
  },
  {
    path: '/products/:id/edit',
    name: 'ProductEdit',
    component: ProductForm,
    meta: { requiresAuth: true },
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfileView,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  // createWebHistory: pakai URL biasa tanpa #
  history: createWebHistory(),
  routes,
})

/**
 * Navigation Guard — dijalankan sebelum setiap perpindahan halaman.
 *
 * to   = route tujuan
 * from = route asal
 * next = fungsi untuk lanjut / redirect
 */
router.beforeEach((to, from, next) => {
  const isLoggedIn = !!getToken()

  // Halaman yang butuh login → cek token
  if (to.meta.requiresAuth && !isLoggedIn) {
    next('/login')
    return
  }

  // Halaman untuk tamu → jika sudah login, redirect ke dashboard
  if (to.meta.requiresGuest && isLoggedIn) {
    next('/dashboard')
    return
  }

  next()
})

export default router
