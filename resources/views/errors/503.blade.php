<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 500px;
            text-align: center;
            margin: 2rem;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #e53e3e;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        p {
            color: #718096;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .error-code {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1rem;
            font-family: monospace;
            font-size: 0.875rem;
            color: #4a5568;
            margin-top: 1rem;
        }
        .contact {
            background: #edf2f7;
            border-radius: 6px;
            padding: 1rem;
            margin-top: 2rem;
        }
        .contact h3 {
            margin: 0 0 0.5rem 0;
            color: #2d3748;
            font-size: 1rem;
        }
        .contact p {
            margin: 0;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔒</div>
        <h1>System Maintenance</h1>
        <p>We're currently performing system maintenance to ensure optimal performance and security. Please try again later.</p>
        
        @if(isset($message))
        <div class="error-code">
            {{ $message }}
        </div>
        @endif
        
        <div class="contact">
            <h3>Need Help?</h3>
            <p>If you continue to experience issues, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
