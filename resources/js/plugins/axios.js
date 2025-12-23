import axios from 'axios'
import { router } from '@inertiajs/vue3'

// Create axios instance
const api = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    }
})

// Request interceptor to add CSRF token
api.interceptors.request.use(
    (config) => {
        const token = document.querySelector('meta[name="csrf-token"]')
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token.getAttribute('content')
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Response interceptor to handle 419 errors globally
api.interceptors.response.use(
    (response) => {
        return response
    },
    async (error) => {
        const originalRequest = error.config

        // Handle 419 CSRF token mismatch
        if (error.response?.status === 419 && !originalRequest._retry) {
            originalRequest._retry = true

            try {
                // Try to refresh the CSRF token by making a GET request
                await axios.get('/dashboard', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })

                // Retry the original request
                return api(originalRequest)
            } catch (refreshError) {
                console.error('Failed to refresh CSRF token:', refreshError)
                
                // If refresh fails, redirect to login
                router.visit('/login', {
                    method: 'get',
                    data: {
                        message: 'Your session has expired. Please log in again.'
                    }
                })
                
                return Promise.reject(refreshError)
            }
        }

        return Promise.reject(error)
    }
)

export default api
