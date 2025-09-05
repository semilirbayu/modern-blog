import api from './api.js'

const authService = {
    async login(credentials) {
        try {
            const response = await api.post('/login', credentials)
            return response
        } catch (error) {
            throw this.handleError(error)
        }
    },

    async logout() {
        try {
            return await api.post('/logout')
        } catch (error) {
            throw this.handleError(error)
        }
    },

    async getUser() {
        try {
            return await api.get('/user')
        } catch (error) {
            throw this.handleError(error)
        }
    },

    async register(userData) {
        try {
            const response = await api.post('/register', userData)
            return response
        } catch (error) {
            throw this.handleError(error)
        }
    },

    async forgotPassword(email) {
        try {
            return await api.post('/forgot-password', { email })
        } catch (error) {
            throw this.handleError(error)
        }
    },

    async resetPassword(data) {
        try {
            return await api.post('/reset-password', data)
        } catch (error) {
            throw this.handleError(error)
        }
    },

    async updateProfile(userData) {
        try {
            return await api.put('/user/profile', userData)
        } catch (error) {
            throw this.handleError(error)
        }
    },

    async changePassword(passwordData) {
        try {
            return await api.put('/user/password', passwordData)
        } catch (error) {
            throw this.handleError(error)
        }
    },

    handleError(error) {
        if (error.response) {
            let message = error.response.data?.message || 'Request failed'
            
            // Handle validation errors
            if (error.response.data?.errors) {
                const firstError = Object.values(error.response.data.errors)[0]
                message = Array.isArray(firstError) ? firstError[0] : firstError
            }
            
            const e = new Error(message)
            e.response = error.response
            return e
        }
        return error instanceof Error ? error : new Error(error.message || 'An error occurred')
    }
}

export default authService