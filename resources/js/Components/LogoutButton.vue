<template>
    <button
        @click="handleLogout"
        :disabled="isLoggingOut"
        :class="[
            'flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200 w-full text-left',
            isLoggingOut ? 'opacity-50 cursor-not-allowed' : ''
        ]"
    >
        <div v-if="isLoggingOut" class="w-4 h-4 border-2 border-slate-400 border-t-transparent rounded-full animate-spin"></div>
        <LogOut v-else class="w-4 h-4" />
        {{ isLoggingOut ? $t('Logging out...') : $t('Logout') }}
    </button>
</template>

<script setup>
import { ref } from 'vue'
import { LogOut } from 'lucide-vue-next'

const isLoggingOut = ref(false)

const handleLogout = async () => {
    if (isLoggingOut.value) return
    
    isLoggingOut.value = true
    
    try {
        // Create a form element for logout submission
        const form = document.createElement('form')
        form.method = 'POST'
        form.action = route('logout')
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')
        if (csrfToken) {
            const csrfInput = document.createElement('input')
            csrfInput.type = 'hidden'
            csrfInput.name = '_token'
            csrfInput.value = csrfToken.getAttribute('content')
            form.appendChild(csrfInput)
        }
        
        // Add method override for DELETE
        const methodInput = document.createElement('input')
        methodInput.type = 'hidden'
        methodInput.name = '_method'
        methodInput.value = 'DELETE'
        form.appendChild(methodInput)
        
        // Append form to body and submit
        document.body.appendChild(form)
        form.submit()
        
    } catch (error) {
        console.error('Logout error:', error)
        isLoggingOut.value = false
        // Fallback: redirect to login page
        window.location.href = route('login')
    }
}

</script>
