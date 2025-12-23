<!DOCTYPE html>
<html lang="en" class="light scroll-smooth " dir="ltr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="HelpDesk - Online Ticket Support" name="description" />
    <meta name="website" content="https://w3bd.com" />
    <meta name="email" content="info@w3bd.com" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="shortcut" href="/favicon.png">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
    
    <!-- Fallback for installer if assets fail to load -->
    <script>
        // Check if Vue app loaded successfully
        window.addEventListener('load', function() {
            setTimeout(function() {
                if (!document.querySelector('#app') || document.querySelector('#app').innerHTML.trim() === '') {
                    console.error('Installer failed to load - showing fallback');
                    document.body.innerHTML = `
                        <div style="padding: 20px; font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto;">
                            <h1>HelpDesk Installation</h1>
                            <p>If you're seeing this message, the installer interface failed to load. This usually happens when:</p>
                            <ul>
                                <li>Assets are not compiled (run: npm run build)</li>
                                <li>JavaScript is disabled in your browser</li>
                                <li>There's a server configuration issue</li>
                            </ul>
                            <p><strong>Solution:</strong> Please run <code>npm run build</code> in your project directory and refresh this page.</p>
                            <p>If the problem persists, check your server error logs for more details.</p>
                        </div>
                    `;
                }
            }, 3000);
        });
    </script>
</head>
<body class="font-inter leading-none antialiased">
    @inertia
</body>
</html>
