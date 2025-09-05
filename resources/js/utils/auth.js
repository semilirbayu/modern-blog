export const TOKEN_KEY = 'auth_token'
export const USER_KEY = 'auth_user'

export function getStoredToken() {
    return localStorage.getItem(TOKEN_KEY)
}

export function setStoredToken(token) {
    localStorage.setItem(TOKEN_KEY, token)
}

export function removeStoredToken() {
    localStorage.removeItem(TOKEN_KEY)
}

export function getStoredUser() {
    const user = localStorage.getItem(USER_KEY)
    return user ? JSON.parse(user) : null
}

export function setStoredUser(user) {
    localStorage.setItem(USER_KEY, JSON.stringify(user))
}

export function removeStoredUser() {
    localStorage.removeItem(USER_KEY)
}

export function clearAuthData() {
    removeStoredToken()
    removeStoredUser()
}

export function isTokenExpired(token) {
    if (!token) return true
    const parts = token.split('.')
    // Non-JWT token: cannot infer expiry; treat as not expired
    if (parts.length !== 3) return false
    try {
        const payload = JSON.parse(atob(parts[1]))
        return payload.exp * 1000 < Date.now()
    } catch {
        // If decode fails, assume not expired to avoid false negatives
        return false
    }
}

export function getAuthHeaders(token = null) {
    const authToken = token || getStoredToken()
    return authToken ? { Authorization: `Bearer ${authToken}` } : {}
}

export function formatAuthError(error) {
    if (error?.response?.data?.message) {
        return error.response.data.message
    }
    if (error?.response?.data?.errors) {
        const firstError = Object.values(error.response.data.errors)[0]
        return Array.isArray(firstError) ? firstError[0] : firstError
    }
    return error?.message || 'Authentication error occurred'
}