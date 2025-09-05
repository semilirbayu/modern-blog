import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '../stores/auth.js'
import { formatAuthError } from '../utils/auth.js'

export function useAuth() {
    const authStore = useAuthStore()
    
    const { user, isAuthenticated, loading, error, initialized } = storeToRefs(authStore)

    const login = async (credentials) => {
        try {
            await authStore.login(credentials)
            return { success: true }
        } catch (error) {
            return { 
                success: false, 
                error: error.message || formatAuthError(error) || 'Login failed' 
            }
        }
    }

    const logout = async () => {
        try {
            await authStore.logout()
            return { success: true }
        } catch (error) {
            return { 
                success: false, 
                error: error.message || 'Logout failed' 
            }
        }
    }

    const fetchUser = async () => {
        try {
            await authStore.fetchUser()
            return { success: true }
        } catch (error) {
            return { 
                success: false, 
                error: error.message || formatAuthError(error) || 'Failed to fetch user' 
            }
        }
    }

    const initialize = async () => {
        return await authStore.initialize()
    }

    const initializeAuth = async () => {
        if (!initialized.value) {
            await initialize()
        }
    }

    const checkAuthStatus = () => {
        return authStore.checkTokenValidity()
    }

    const requireAuth = () => {
        if (!isAuthenticated.value) {
            throw new Error('Authentication required')
        }
    }

    const isLoading = computed(() => loading.value)

    const clearError = () => {
        authStore.clearError()
    }

    const hasRole = (role) => {
        return user.value?.roles?.includes(role) || false
    }

    const hasPermission = (permission) => {
        return user.value?.permissions?.includes(permission) || false
    }

    const isAdmin = computed(() => {
        return hasRole('admin') || hasRole('super-admin')
    })

    const canManagePosts = computed(() => {
        return hasPermission('manage-posts') || isAdmin.value
    })

    const canManageCategories = computed(() => {
        return hasPermission('manage-categories') || isAdmin.value
    })

    return {
        user,
        isAuthenticated,
        loading,
        error,
        initialized,
        isLoading,
        login,
        logout,
        fetchUser,
        initialize,
        initializeAuth,
        checkAuthStatus,
        requireAuth,
        clearError,
        hasRole,
        hasPermission,
        isAdmin,
        canManagePosts,
        canManageCategories
    }
}