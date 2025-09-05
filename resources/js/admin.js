import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import { adminRoutes } from './router/admin.js'
import '../css/app.css'

const pinia = createPinia()

const router = createRouter({
    history: createWebHistory('/admin'),
    routes: adminRoutes,
})

router.beforeEach(async (to, from, next) => {
    const { useAuth } = await import('./composables/useAuth.js')
    const { initializeAuth, isAuthenticated, isLoading, hasPermission, isAdmin } = useAuth()
    
    // Initialize auth state if not already done
    await initializeAuth()
    
    const requiresAuth = to.matched.some(r => r.meta.requiresAuth)
    const requiresGuest = to.matched.some(r => r.meta.requiresGuest)
    const authenticated = isAuthenticated.value
    
    if (isLoading.value) {
        // Wait for auth check to complete
        return next()
    }
    
    if (requiresAuth && !authenticated) {
        return next({ name: 'admin.login' })
    }
    
    if (requiresGuest && authenticated) {
        return next({ name: 'admin.dashboard' })
    }
    
    // Check permissions/roles
    const requiredPerms = to.matched.flatMap(r => r.meta.permissions || [])
    if (requiredPerms.length && authenticated && !isAdmin.value) {
        const allowed = requiredPerms.every(p => hasPermission(p))
        if (!allowed) {
            return next({ name: 'admin.dashboard' })
        }
    }
    
    next()
})

const AdminApp = {
    template: `
        <div id="admin-app">
            <router-view />
        </div>
    `
}

const app = createApp(AdminApp)

app.use(pinia)
app.use(router)
app.mount('#app')