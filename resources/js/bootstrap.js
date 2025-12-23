import axios from 'axios'
window.axios = axios
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

// Resolve Pusher config at runtime from initial Inertia page props (shared settings)
let pusherKey = import.meta.env.VITE_PUSHER_APP_KEY
let pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER

// Function to get Pusher config from Inertia page props
function getPusherConfigFromInertia() {
    try {
        // Method 1: Try to get from Inertia page data attribute
        const appEl = document.getElementById('app')
        if (appEl && appEl.dataset && appEl.dataset.page) {
            const initialPage = JSON.parse(appEl.dataset.page)
            const settings = initialPage?.props?.settings || {}
            
            // Get pusher config from database (stored as JSON in settings.pusher)
            if (settings.pusher && typeof settings.pusher === 'object') {
                return {
                    key: settings.pusher.key || pusherKey,
                    cluster: settings.pusher.cluster || pusherCluster
                }
            } else if (settings.pusher && typeof settings.pusher === 'string') {
                // Fallback: if it's still a string, try to parse it
                const pusher = JSON.parse(settings.pusher)
                return {
                    key: pusher?.key || pusherKey,
                    cluster: pusher?.cluster || pusherCluster
                }
            }
        }
        
        // Method 2: Try to get from window.__inertia if available (for Inertia SSR)
        if (window.__inertia && window.__inertia.page && window.__inertia.page.props) {
            const settings = window.__inertia.page.props.settings || {}
            if (settings.pusher && typeof settings.pusher === 'object') {
                return {
                    key: settings.pusher.key || pusherKey,
                    cluster: settings.pusher.cluster || pusherCluster
                }
            }
        }
    } catch (e) {
        console.warn('⚠️ Failed to read Pusher settings from Inertia:', e)
    }
    return null
}

// Function to initialize Echo with Pusher config
function initializeEcho(key, cluster) {
    // Only initialize Echo if Pusher is configured
    if (key) {
        const echoConfig = {
            broadcaster: 'pusher',
            key: key,
            cluster: cluster,
            forceTLS: false, // Use HTTP for local development
            enabledTransports: ['ws', 'wss'],
            disableStats: false,
            // Remove custom wsHost and wsPort to use Pusher's cloud service
        }

        window.Echo = new Echo(echoConfig)

        // Enhanced connection monitoring
        window.Echo.connector.pusher.connection.bind('connecting', () => {
            console.log('🔌 WebSocket: Connecting to Pusher...')
        })

        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('✅ WebSocket: Connected to Pusher successfully!')
        })

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            console.log('❌ WebSocket: Disconnected from Pusher')
        })

        window.Echo.connector.pusher.connection.bind('error', (error) => {
            console.error('🚨 WebSocket: Connection error:', error)
            console.error('Error details:', {
                type: error.type,
                error: error.error,
                data: error.data
            })
        })

        window.Echo.connector.pusher.connection.bind('state_change', (states) => {
            console.log('🔄 WebSocket: State changed from', states.previous, 'to', states.current)
        })
    } else {
        console.warn('Pusher not configured - WebSocket features disabled')
        // Create a mock Echo object to prevent errors
        window.Echo = {
            channel: () => ({
                listen: () => {},
                stopListening: () => {},
                subscribed: () => {},
                error: () => {}
            }),
            private: () => ({
                listen: () => {},
                stopListening: () => {},
                subscribed: () => {},
                error: () => {}
            }),
            join: () => ({
                listen: () => {},
                stopListening: () => {},
                subscribed: () => {},
                error: () => {}
            }),
            leave: () => {},
            disconnect: () => {}
        }
    }
}

// Try to get Pusher config from Inertia (wait for DOM if needed) and initialize Echo
function initializePusherConfig() {
    let finalKey = pusherKey
    let finalCluster = pusherCluster
    
    const config = getPusherConfigFromInertia()
    if (config) {
        finalKey = config.key
        finalCluster = config.cluster
        console.log('✅ Using Pusher config from database:', { key: finalKey, cluster: finalCluster })
    } else {
        console.log('ℹ️ Using fallback Vite env values:', { key: finalKey, cluster: finalCluster })
    }
    
    // Update outer scope variables for potential future use
    pusherKey = finalKey
    pusherCluster = finalCluster
    
    // Initialize Echo with the resolved config
    initializeEcho(finalKey, finalCluster)
}

// Initialize config when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePusherConfig)
} else {
    // DOM already loaded
    initializePusherConfig()
}

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Set up CSRF token for all requests
const token = document.head.querySelector('meta[name="csrf-token"]')

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token')
}
