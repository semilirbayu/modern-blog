import axios from 'axios'

const api = axios.create({
    baseURL: '/api',
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
})

api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token')
        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }
        
        if (config.data instanceof FormData) {
            config.headers['Content-Type'] = 'multipart/form-data'
        }
        
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

api.interceptors.response.use(
    (response) => {
        return response
    },
    async (error) => {
        const originalRequest = error.config
        
        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true
            
            localStorage.removeItem('auth_token')
            
            if (window.location.pathname.startsWith('/admin')) {
                window.location.href = '/admin/login'
            }
        }
        
        if (error.response?.status === 419) {
            try {
                await axios.get('/sanctum/csrf-cookie')
                return api(originalRequest)
            } catch (csrfError) {
                console.error('CSRF refresh failed:', csrfError)
            }
        }
        
        return Promise.reject(error)
    }
)

export default api