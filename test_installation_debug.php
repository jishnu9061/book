<?php

/**
 * Installation Debug Script
 * 
 * This script helps debug installation issues by testing various components
 * that might cause the HTML response instead of JSON during migration.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

echo "=== Installation Debug Script ===\n\n";

// Test 1: Check if database connection works
echo "1. Testing database connection...\n";
try {
    $connection = \Illuminate\Support\Facades\DB::connection();
    $connection->getPdo();
    echo "   ✓ Database connection successful\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 2: Check if migrations can run
echo "\n2. Testing migration capability...\n";
try {
    $migrations = \Illuminate\Support\Facades\Artisan::call('migrate:status');
    echo "   ✓ Migration status check successful\n";
} catch (Exception $e) {
    echo "   ✗ Migration status check failed: " . $e->getMessage() . "\n";
}

// Test 3: Check if tables exist
echo "\n3. Checking existing tables...\n";
try {
    $hasUsers = \Illuminate\Support\Facades\Schema::hasTable('users');
    echo "   Users table exists: " . ($hasUsers ? 'Yes' : 'No') . "\n";
    
    $hasRoles = \Illuminate\Support\Facades\Schema::hasTable('roles');
    echo "   Roles table exists: " . ($hasRoles ? 'Yes' : 'No') . "\n";
} catch (Exception $e) {
    echo "   ✗ Table check failed: " . $e->getMessage() . "\n";
}

// Test 4: Check environment configuration
echo "\n4. Checking environment configuration...\n";
echo "   APP_ENV: " . config('app.env') . "\n";
echo "   APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "   APP_INSTALLED: " . (config('app.installed') ? 'true' : 'false') . "\n";
echo "   DB_CONNECTION: " . config('database.default') . "\n";

// Test 5: Check if installer routes are accessible
echo "\n5. Testing installer route accessibility...\n";
try {
    $request = Request::create('/install/run-installation', 'POST', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_CONTENT_TYPE' => 'application/json'
    ]);
    
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/json');
    
    echo "   ✓ Request object created successfully\n";
    echo "   Expects JSON: " . ($request->expectsJson() ? 'Yes' : 'No') . "\n";
} catch (Exception $e) {
    echo "   ✗ Request creation failed: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
echo "If you see any ✗ marks above, those are likely the cause of your installation issues.\n";
echo "Check the Laravel logs in storage/logs/laravel.log for more detailed error information.\n";
