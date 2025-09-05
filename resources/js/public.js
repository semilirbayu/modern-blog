import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import { publicRoutes } from './router/public.js'
import App from './App.vue'
import '../css/app.css'

const pinia = createPinia()

const router = createRouter({
    history: createWebHistory(),
    routes: publicRoutes,
})

const app = createApp(App)

app.use(pinia)
app.use(router)

app.mount('#app')