import { ref } from 'vue'
import axios from 'axios'

const csrfToken = ref(null)
const isRefreshing = ref(false)

export function useCsrfToken() {
    const getCsrfToken = () => {
        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]')
        return token ? token.getAttribute('content') : null
    }

    const refreshCsrfToken = async () => {
        if (isRefreshing.value) return csrfToken.value
        
        isRefreshing.value = true
        
        try {
            // Make a simple GET request to refresh the CSRF token
            const response = await axios.get('/dashboard', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            
            // Update the CSRF token from the response
            const newToken = getCsrfToken()
            if (newToken) {
                csrfToken.value = newToken
                // Update axios default header
                axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken
            }
            
            return newToken
        } catch (error) {
            console.warn('Failed to refresh CSRF token:', error)
            return null
        } finally {
            isRefreshing.value = false
        }
    }

    const ensureValidCsrfToken = async () => {
        const currentToken = getCsrfToken()
        if (!currentToken || isRefreshing.value) {
            return await refreshCsrfToken()
        }
        return currentToken
    }

    // Initialize CSRF token
    csrfToken.value = getCsrfToken()
    if (csrfToken.value) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.value
    }

    return {
        csrfToken,
        isRefreshing,
        getCsrfToken,
        refreshCsrfToken,
        ensureValidCsrfToken
    }
}
