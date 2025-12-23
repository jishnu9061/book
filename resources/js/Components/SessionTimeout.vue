<template>
    <div v-if="showWarning" class="fixed top-4 right-4 z-50 max-w-sm">
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 border-2 border-yellow-600 border-t-transparent rounded-full animate-spin mr-2"></div>
                    <div>
                        <p class="font-semibold text-sm">{{ $t('Session Expiring Soon') }}</p>
                        <p class="text-xs">{{ $t('Your session will expire in') }} {{ timeLeft }} {{ $t('seconds') }}</p>
                    </div>
                </div>
                <button @click="extendSession" class="ml-2 text-yellow-600 hover:text-yellow-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const showWarning = ref(false)
const timeLeft = ref(0)
const warningTimer = ref(null)
const countdownTimer = ref(null)

// Session timeout settings (in minutes)
const SESSION_LIFETIME = 120 // 2 hours
const WARNING_TIME = 5 // Show warning 5 minutes before expiry

onMounted(() => {
    startSessionTimer()
})

onUnmounted(() => {
    clearTimers()
})

const startSessionTimer = () => {
    // Calculate when to show warning (5 minutes before session expires)
    const warningTime = (SESSION_LIFETIME - WARNING_TIME) * 60 * 1000 // Convert to milliseconds
    
    warningTimer.value = setTimeout(() => {
        showWarning.value = true
        timeLeft.value = WARNING_TIME * 60 // 5 minutes in seconds
        startCountdown()
    }, warningTime)
}

const startCountdown = () => {
    countdownTimer.value = setInterval(() => {
        timeLeft.value--
        
        if (timeLeft.value <= 0) {
            // Session expired, redirect to login
            clearTimers()
            router.visit('/login', {
                method: 'get',
                data: {
                    message: 'Your session has expired. Please log in again.'
                }
            })
        }
    }, 1000)
}

const extendSession = async () => {
    try {
        // Make a request to extend the session
        await router.get('/dashboard', {}, {
            preserveState: true,
            preserveScroll: true
        })
        
        // Hide warning and restart timer
        showWarning.value = false
        clearTimers()
        startSessionTimer()
        
    } catch (error) {
        console.error('Failed to extend session:', error)
        // If extending fails, redirect to login
        router.visit('/login', {
            method: 'get',
            data: {
                message: 'Your session has expired. Please log in again.'
            }
        })
    }
}

const clearTimers = () => {
    if (warningTimer.value) {
        clearTimeout(warningTimer.value)
        warningTimer.value = null
    }
    if (countdownTimer.value) {
        clearInterval(countdownTimer.value)
        countdownTimer.value = null
    }
}
</script>
