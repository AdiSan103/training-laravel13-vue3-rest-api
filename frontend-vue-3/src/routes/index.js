import { createRouter, createWebHistory } from "vue-router";
import LoginView from "../views/Login.vue";
import RegisterView from "../views/Register.vue";
import PostsView from "../views/Posts.vue";
import PostDetailView from "../views/PostDetail.vue";
import PostCreateView from "../views/PostCreate.vue";
import PostEditView from "../views/PostEdit.vue";
import CategoriesView from "../views/Categories.vue";
import CategoryCreateView from "../views/CategoryCreate.vue";
import CategoryEditView from "../views/CategoryEdit.vue";

const routes = [
  { path: "/", redirect: "/posts" },
  { path: "/login", component: LoginView },
  { path: "/register", component: RegisterView },
  { path: "/posts", component: PostsView, meta: { auth: true } },
  { path: "/posts/create", component: PostCreateView, meta: { auth: true } },
  { path: "/posts/:slug", component: PostDetailView, meta: { auth: true } },
  { path: "/posts/:slug/edit", component: PostEditView, meta: { auth: true } },
  { path: "/categories", component: CategoriesView, meta: { auth: true } },
  {
    path: "/categories/create",
    component: CategoryCreateView,
    meta: { auth: true },
  },
  {
    path: "/categories/:id/edit",
    component: CategoryEditView,
    meta: { auth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const token = localStorage.getItem("token");
  if (to.meta.auth && !token) return "/login";
});

export default router;
