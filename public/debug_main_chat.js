// Debug script for main chat page
console.log('=== Main Chat Debug ===');

// Check if PusherManager exists
console.log('PusherManager exists:', !!window.PusherManager);
if (window.PusherManager) {
    console.log('PusherManager connection status:', window.PusherManager.getConnectionStatus());
    console.log('PusherManager echo exists:', !!window.PusherManager.echo);
} else {
    console.error('❌ PusherManager is NOT available!');
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

// Test manual channel subscription
if (window.Echo) {
    console.log('Testing manual channel subscription...');
    const testChannel = window.Echo.channel('chat.17');
    console.log('Test channel created:', !!testChannel);
    
    // Listen for messages
    testChannel.listen('NewChatMessage', (data) => {
        console.log('✅ Manual subscription received NewChatMessage:', data);
        alert('Message received: ' + (data.chatMessage ? data.chatMessage.message : 'No message'));
    });
    
    console.log('Manual subscription set up for chat.17');
}

// Test PusherManager if available
if (window.PusherManager) {
    console.log('Testing PusherManager...');
    window.PusherManager.listenToChat(17, (data) => {
        console.log('✅ PusherManager received message:', data);
        alert('PusherManager received: ' + (data.chatMessage ? data.chatMessage.message : 'No message'));
    });
    console.log('PusherManager test set up for chat.17');
}

console.log('=== Debug Complete ===');
console.log('Now send a message from your chat interface to test');

