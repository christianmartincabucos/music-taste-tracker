import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router"
import { useAuthStore } from "../stores/auth"

// Views
import Login from "../views/Login.vue"
import Register from "../views/Register.vue"
import AlbumList from "../views/AlbumList.vue"

const routes: Array<RouteRecordRaw> = [
  {
    path: "/",
    redirect: "/albums",
  },
  {
    path: "/login",
    name: "login",
    component: Login,
    meta: { requiresGuest: true },
  },
  {
    path: "/register",
    name: "register",
    component: Register,
    meta: { requiresGuest: true },
  },
  {
    path: "/albums",
    name: "albums",
    component: AlbumList,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/login")
  }
  // Check if route requires guest (non-authenticated) access
  else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next("/albums")
  } else {
    next()
  }
})

export default router

