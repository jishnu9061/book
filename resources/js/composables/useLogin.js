import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useCsrfToken } from './useCsrfToken'

export function useLogin() {
    const isLoggingIn = ref(false)
    const loginError = ref(null)
    const { ensureValidCsrfToken } = useCsrfToken()

    const login = async (formData, options = {}) => {
        if (isLoggingIn.value) return
        
        isLoggingIn.value = true
        loginError.value = null
        
        try {
            // Ensure we have a valid CSRF token
            await ensureValidCsrfToken()
            
            // Attempt login with retry mechanism
            await attemptLogin(formData, options)
            
        } catch (error) {
            console.error('Login error:', error)
            handleLoginError(error)
        }
    }

    const attemptLogin = async (formData, options, retryCount = 0) => {
        const maxRetries = 2
        
        try {
            router.post(route('login.store'), formData, {
                onSuccess: (page) => {
                    // Login successful
                    isLoggingIn.value = false
                    loginError.value = null
                    
                    // Call success callback if provided
                    if (options.onSuccess) {
                        options.onSuccess(page)
                    }
                },
                onError: (errors) => {
                    console.error('Login error:', errors)
                    
                    // If CSRF error and we haven't exceeded retries
                    if ((errors.status === 419 || errors.message?.includes('419')) && retryCount < maxRetries) {
                        console.log(`Retrying login (attempt ${retryCount + 1}/${maxRetries + 1})`)
                        // Refresh CSRF token and retry
                        ensureValidCsrfToken().then(() => {
                            attemptLogin(formData, options, retryCount + 1)
                        })
                    } else {
                        // Max retries exceeded or other error
                        handleLoginError(errors)
                    }
                },
                onFinish: () => {
                    // Reset loading state after a delay
                    setTimeout(() => {
                        isLoggingIn.value = false
                    }, 1000)
                },
                ...options
            })
        } catch (error) {
            if (retryCount < maxRetries) {
                console.log(`Retrying login due to error (attempt ${retryCount + 1}/${maxRetries + 1})`)
                await ensureValidCsrfToken()
                attemptLogin(formData, options, retryCount + 1)
            } else {
                handleLoginError(error)
            }
        }
    }

    const handleLoginError = (error) => {
        console.error('Final login error:', error)
        
        // Set error message
        if (error.status === 419) {
            loginError.value = 'Your session has expired. Please try again.'
        } else if (error.email) {
            loginError.value = error.email
        } else if (error.message) {
            loginError.value = error.message
        } else {
            loginError.value = 'Login failed. Please check your credentials and try again.'
        }
        
        isLoggingIn.value = false
        
        // Call error callback if provided
        if (options.onError) {
            options.onError(error)
        }
    }

    const clearError = () => {
        loginError.value = null
    }

    return {
        isLoggingIn,
        loginError,
        login,
        clearError
    }
}
