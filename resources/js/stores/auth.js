import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import authService from '../services/authService.js'
import { 
    getStoredToken, 
    setStoredToken, 
    removeStoredToken,
    getStoredUser, 
    setStoredUser, 
    removeStoredUser,
    clearAuthData,
    isTokenExpired,
    formatAuthError
} from '../utils/auth.js'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(getStoredUser())
    const token = ref(getStoredToken())
    const loading = ref(false)
    const error = ref(null)
    const initialized = ref(false)

    const isAuthenticated = computed(() => {
        return !!token.value && !!user.value && !isTokenExpired(token.value)
    })

    const login = async (credentials) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await authService.login(credentials)
            
            const { token: authToken, user: userData } = response.data
            
            token.value = authToken
            user.value = userData
            
            setStoredToken(authToken)
            setStoredUser(userData)
            
            return response
        } catch (err) {
            error.value = formatAuthError(err)
            throw new Error(error.value)
        } finally {
            loading.value = false
        }
    }

    const logout = async () => {
        loading.value = true
        error.value = null
        
        try {
            if (token.value) {
                await authService.logout()
            }
        } catch (err) {
            console.error('Logout error:', err)
        } finally {
            token.value = null
            user.value = null
            clearAuthData()
            loading.value = false
        }
    }

    const fetchUser = async () => {
        if (!token.value || isTokenExpired(token.value)) {
            await logout()
            return null
        }
        
        loading.value = true
        
        try {
            const response = await authService.getUser()
            user.value = response.data
            setStoredUser(response.data)
            return response
        } catch (err) {
            if (err.response?.status === 401) {
                await logout()
            }
            error.value = formatAuthError(err)
            throw new Error(error.value)
        } finally {
            loading.value = false
        }
    }

    const clearError = () => {
        error.value = null
    }

    const initialize = async () => {
        if (initialized.value) return
        
        loading.value = true
        
        try {
            if (token.value && !isTokenExpired(token.value)) {
                if (!user.value) {
                    await fetchUser()
                }
            } else {
                await logout()
            }
        } catch (err) {
            console.error('Failed to initialize auth:', err)
            await logout()
        } finally {
            initialized.value = true
            loading.value = false
        }
    }

    const checkTokenValidity = () => {
        if (token.value && isTokenExpired(token.value)) {
            logout()
            return false
        }
        return !!token.value
    }

    return {
        user,
        token,
        loading,
        error,
        initialized,
        isAuthenticated,
        login,
        logout,
        fetchUser,
        clearError,
        initialize,
        checkTokenValidity
    }
})