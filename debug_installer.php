<?php
/**
 * Installer Debug Script
 * 
 * This script helps diagnose installer issues by checking:
 * - Asset compilation status
 * - Database connectivity
 * - File permissions
 * - PHP requirements
 * - Server configuration
 */

echo "<h1>HelpDesk Installer Debug Report</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";

// Check PHP version
echo "<h2>PHP Version</h2>";
$phpVersion = phpversion();
if (version_compare($phpVersion, '8.2.0', '>=')) {
    echo "<p class='success'>✓ PHP {$phpVersion} (Required: 8.2.0+)</p>";
} else {
    echo "<p class='error'>✗ PHP {$phpVersion} (Required: 8.2.0+)</p>";
}

// Check required extensions
echo "<h2>PHP Extensions</h2>";
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✓ {$ext}</p>";
    } else {
        echo "<p class='error'>✗ {$ext} (Required)</p>";
    }
}

// Check file permissions
echo "<h2>File Permissions</h2>";
$directories = [
    'storage' => storage_path(),
    'bootstrap/cache' => base_path('bootstrap/cache'),
    'public' => public_path()
];

foreach ($directories as $name => $path) {
    if (is_writable($path)) {
        echo "<p class='success'>✓ {$name} directory is writable</p>";
    } else {
        echo "<p class='error'>✗ {$name} directory is not writable</p>";
    }
}

// Check asset compilation
echo "<h2>Asset Compilation</h2>";
$assetFiles = [
    'public/css/app.css',
    'public/js/app.js',
    'public/build/manifest.json'
];

foreach ($assetFiles as $file) {
    $fullPath = base_path($file);
    if (file_exists($fullPath)) {
        $size = filesize($fullPath);
        echo "<p class='success'>✓ {$file} exists ({$size} bytes)</p>";
    } else {
        echo "<p class='error'>✗ {$file} missing</p>";
    }
}

// Check .env file
echo "<h2>Environment Configuration</h2>";
$envPath = base_path('.env');
if (file_exists($envPath)) {
    echo "<p class='success'>✓ .env file exists</p>";
    
    $envContent = file_get_contents($envPath);
    $requiredKeys = ['APP_NAME', 'APP_URL', 'DB_CONNECTION'];
    
    foreach ($requiredKeys as $key) {
        if (strpos($envContent, $key) !== false) {
            echo "<p class='success'>✓ {$key} is set</p>";
        } else {
            echo "<p class='warning'>⚠ {$key} is not set</p>";
        }
    }
} else {
    echo "<p class='error'>✗ .env file missing</p>";
}

// Check database connectivity
echo "<h2>Database Connectivity</h2>";
try {
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        if (strpos($envContent, 'DB_CONNECTION=mysql') !== false) {
            echo "<p class='info'>ℹ Database configuration found (MySQL)</p>";
        } else {
            echo "<p class='info'>ℹ Database not configured yet</p>";
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Database check failed: " . $e->getMessage() . "</p>";
}

// Check server configuration
echo "<h2>Server Configuration</h2>";
echo "<p class='info'>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p class='info'>Memory Limit: " . ini_get('memory_limit') . "</p>";
echo "<p class='info'>Max Execution Time: " . ini_get('max_execution_time') . "</p>";
echo "<p class='info'>Upload Max Filesize: " . ini_get('upload_max_filesize') . "</p>";

// Check if already installed
echo "<h2>Installation Status</h2>";
$installedFile = storage_path('installed');
$envInstalled = false;

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    $envInstalled = strpos($envContent, 'APP_INSTALLED=true') !== false;
}

if (file_exists($installedFile) || $envInstalled) {
    echo "<p class='warning'>⚠ Application appears to be already installed</p>";
} else {
    echo "<p class='success'>✓ Application is not installed (ready for installation)</p>";
}

// Recommendations
echo "<h2>Recommendations</h2>";
echo "<div style='background:#f0f8ff;padding:15px;border-radius:5px;margin:10px 0;'>";

if (!file_exists(base_path('public/css/app.css'))) {
    echo "<p class='error'><strong>Critical:</strong> Assets are not compiled. Run: <code>npm run build</code> or <code>yarn build</code></p>";
}

if (!is_writable(storage_path())) {
    echo "<p class='error'><strong>Critical:</strong> Storage directory is not writable. Fix permissions: <code>chmod -R 775 storage</code></p>";
}

if (!file_exists($envPath)) {
    echo "<p class='warning'><strong>Warning:</strong> .env file is missing. Copy from .env.example and configure.</p>";
}

echo "<p class='info'><strong>Tip:</strong> If you see a blank screen, check browser console (F12) for JavaScript errors.</p>";
echo "<p class='info'><strong>Tip:</strong> Check Laravel logs in storage/logs/ for server-side errors.</p>";

echo "</div>";

echo "<h2>Quick Fixes</h2>";
echo "<pre>";
echo "# Compile assets\n";
echo "npm install\n";
echo "npm run build\n\n";
echo "# Fix permissions\n";
echo "chmod -R 775 storage bootstrap/cache\n";
echo "chown -R www-data:www-data storage bootstrap/cache\n\n";
echo "# Clear cache\n";
echo "php artisan config:clear\n";
echo "php artisan cache:clear\n";
echo "php artisan view:clear\n";
echo "</pre>";

echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>
