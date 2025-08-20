import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'

const routes = [
  { path: '/', name: 'home', component: Home },
  { path: '/calendar', name: 'calendar', component: Home },
  { path: '/notes', name: 'notes', component: Home },
  { path: '/diary', name: 'diary', component: Home },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router


