import { createRouter, createWebHistory } from 'vue-router'
import LoginViewVue from '@/views/LoginView.vue'
import DashboardViewVue from '@/views/DashboardView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'auth',
      component: LoginViewVue,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardViewVue,
    },
  ],
})

router.beforeEach((to, from, next) => {
  const isConnected = localStorage.getItem('isConnected') === '1'
  if (to.name === 'auth' && isConnected) {
    next({ name: 'dashboard' })
  } else if (to.name === 'dashboard' && !isConnected) {
    next({ name: 'auth' })
  } else {
    next()
  }
})

export default router
