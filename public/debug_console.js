// Debug script to run in browser console
console.log('=== Chat System Debug ===');

// Check if PusherManager exists
console.log('PusherManager exists:', !!window.PusherManager);
if (window.PusherManager) {
    console.log('PusherManager connection status:', window.PusherManager.getConnectionStatus());
    console.log('PusherManager echo exists:', !!window.PusherManager.echo);
}

// Check if Echo exists
console.log('Echo exists:', !!window.Echo);
if (window.Echo) {
    console.log('Echo connector exists:', !!window.Echo.connector);
    if (window.Echo.connector) {
        console.log('Echo pusher exists:', !!window.Echo.connector.pusher);
        if (window.Echo.connector.pusher) {
            console.log('Pusher connection state:', window.Echo.connector.pusher.connection.state);
            console.log('Pusher socket ID:', window.Echo.connector.pusher.connection.socket_id);
        }
    }
}

// Check if Pusher exists
console.log('Pusher exists:', !!window.Pusher);

// Test channel subscription
if (window.Echo) {
    console.log('Testing channel subscription...');
    const testChannel = window.Echo.channel('chat.17');
    console.log('Test channel created:', !!testChannel);
    
    // Listen for messages
    testChannel.listen('NewChatMessage', (data) => {
        console.log('✅ Received NewChatMessage:', data);
    });
    
    testChannel.listen('NewPublicChatMessage', (data) => {
        console.log('✅ Received NewPublicChatMessage:', data);
    });
    
    console.log('Test listeners set up for chat.17');
}

// Check for any errors
window.addEventListener('error', (e) => {
    console.error('JavaScript Error:', e.error);
});

// Check for unhandled promise rejections
window.addEventListener('unhandledrejection', (e) => {
    console.error('Unhandled Promise Rejection:', e.reason);
});

console.log('=== Debug Complete ===');
console.log('Now send a message from your chat interface to test real-time functionality');

