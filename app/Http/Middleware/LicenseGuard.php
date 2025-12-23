<?php

namespace App\Http\Middleware;

use App\Services\LicenseCore;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class LicenseGuard
{
    private $core;
    private $allowed = [
        'license.show', 'license.activate', 'license.settings', 'license.deactivate',
        'installer.index', 'installer.check-requirements', 'installer.verify-license',
        'installer.test-database', 'installer.save-environment', 'installer.run-installation',
    ];
    
    private $public = [
        '/', 'home', 'terms_service', 'privacy', 'contact', 'services', 'faq',
        'team', 'kb', 'blog', 'login', 'register', 'password.request', 'password.reset',
        'password.email', 'password.update', 'verification.notice', 'verification.verify', 'verification.resend',
    ];

    public function __construct(LicenseCore $core)
    {
        $this->core = $core;
    }

    public function handle(Request $request, Closure $next)
    {
        // Early check for home path - must be first to prevent redirect loops
        if ($request->is('/') || $request->path() === '' || $request->path() === '/') {
            return $next($request);
        }

        // Skip license validation for local development
        if ($this->isLocalDevelopment()) {
            return $next($request);
        }

        // Integrity check
        if (!$this->integrityCheck()) {
            return $this->blockAccess('System integrity violation detected');
        }

        // Skip for installer
        if ($this->isInstaller($request)) {
            return $next($request);
        }

        // Skip if database not ready
        if (!$this->dbReady()) {
            return $next($request);
        }

        // Skip for allowed routes
        if (in_array(Route::currentRouteName(), $this->allowed)) {
            return $next($request);
        }

        // Skip for public routes
        if ($this->isPublic($request)) {
            return $next($request);
        }

        // License validation
        if (!$this->core->check()) {
            $key = $this->core->getKey();
            if (empty($key)) {
                return Redirect::route('license.show')->with('license_error', 'Application not activated. Please enter your purchase code.');
            }
            return Redirect::route('license.settings')->with('error', 'License verification failed. Please check your settings.');
        }

        return $next($request);
    }

    private function integrityCheck()
    {
        // Check if critical files exist and have expected content
        $files = [
            app_path('Services/LicenseCore.php'),
            app_path('Http/Middleware/LicenseGuard.php'),
            app_path('Http/Controllers/LicenseController.php'),
        ];

        foreach ($files as $file) {
            if (!file_exists($file)) {
                return false;
            }
            
            // Check for expected class names
            $content = file_get_contents($file);
            if (strpos($content, 'LicenseCore') === false && strpos($content, 'LicenseGuard') === false && strpos($content, 'LicenseController') === false) {
                return false;
            }
        }

        // Check if middleware is registered using router
        $router = app('router');
        $middleware = $router->getMiddleware();
        
        return in_array(LicenseGuard::class, $middleware) || 
               in_array('license', $middleware) ||
               in_array(\App\Http\Middleware\AppIntegrityValidator::class, $middleware);
    }

    private function isInstaller($request)
    {
        return $request->is('install*') || $request->is('update*');
    }

    private function dbReady()
    {
        try {
            DB::connection()->getPdo();
            return Schema::hasTable('settings');
        } catch (\Exception $e) {
            return false;
        }
    }

    private function isPublic($request)
    {
        $currentRoute = Route::currentRouteName();
        return in_array($currentRoute, $this->public) || $request->is('public/*');
    }

    private function isLocalDevelopment()
    {
        // Check if running in local development environment with specific host and port
        return $this->isLocalhostWithPort8900();
    }

    private function isLocalhostWithPort8900()
    {
        $host = request()->getHost();
        $port = request()->getPort();
        
        // Allow localhost and 127.0.0.1 with common development ports
        return ($host === 'localhost' || $host === '127.0.0.1') && in_array($port, [8000, 8900, 3000, 8080]);
    }

    private function blockAccess($message)
    {
        return response()->view('errors.503', ['message' => $message], 503);
    }
}
