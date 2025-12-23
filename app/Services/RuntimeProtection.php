<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class RuntimeProtection
{
    private $protectionKey = 'runtime_protection_check';
    private $maxChecks = 10;
    private $checkInterval = 300; // 5 minutes

    public function __construct()
    {
        $this->initializeProtection();
    }

    public function checkProtection()
    {
        $lastCheck = Cache::get($this->protectionKey . '_last', 0);
        $checkCount = Cache::get($this->protectionKey . '_count', 0);

        // Don't check too frequently
        if (time() - $lastCheck < $this->checkInterval) {
            return true;
        }

        // Don't check too many times
        if ($checkCount >= $this->maxChecks) {
            return $this->performDeepCheck();
        }

        $isValid = $this->performQuickCheck();
        
        Cache::put($this->protectionKey . '_last', time(), now()->addHour());
        Cache::increment($this->protectionKey . '_count');

        if (!$isValid) {
            $this->handleViolation();
        }

        return $isValid;
    }

    private function initializeProtection()
    {
        if (!Cache::has($this->protectionKey . '_initialized')) {
            Cache::put($this->protectionKey . '_initialized', true, now()->addDay());
            Cache::put($this->protectionKey . '_count', 0, now()->addDay());
            Cache::put($this->protectionKey . '_last', 0, now()->addDay());
        }
    }

    private function performQuickCheck()
    {
        // Check if critical classes exist and are loadable
        $criticalClasses = [
            \App\Services\LicenseCore::class,
            \App\Http\Middleware\LicenseGuard::class,
            \App\Services\CodeIntegrityChecker::class,
        ];

        foreach ($criticalClasses as $class) {
            if (!class_exists($class)) {
                return false;
            }

            // Check if class has expected methods
            $reflection = new \ReflectionClass($class);
            if (!$reflection->hasMethod('check') && !$reflection->hasMethod('handle')) {
                return false;
            }
        }

        // Check if middleware is registered
        $kernelFile = app_path('Http/Kernel.php');
        if (!File::exists($kernelFile)) {
            return false;
        }

        $kernelContent = File::get($kernelFile);
        $hasLicenseMiddleware = strpos($kernelContent, 'LicenseGuard::class') !== false ||
                               strpos($kernelContent, 'AppIntegrityValidator::class') !== false ||
                               strpos($kernelContent, 'middlewareAliases') !== false;

        if (!$hasLicenseMiddleware) {
            return false;
        }

        return true;
    }

    private function performDeepCheck()
    {
        $integrityChecker = app(CodeIntegrityChecker::class);
        return $integrityChecker->checkIntegrity();
    }

    private function handleViolation()
    {
        Log::critical('Runtime protection violation detected', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'timestamp' => now(),
            'session_id' => session()->getId(),
        ]);

        // Clear all caches to force re-validation
        Cache::flush();

        // Optionally, you could:
        // - Block the IP address
        // - Send alert to admin
        // - Log to external security service
        // - Disable the application temporarily
    }

    public function isProtected()
    {
        return $this->checkProtection();
    }

    public function resetProtection()
    {
        Cache::forget($this->protectionKey . '_count');
        Cache::forget($this->protectionKey . '_last');
        Cache::forget($this->protectionKey . '_initialized');
    }

    public function getProtectionStatus()
    {
        return [
            'initialized' => Cache::get($this->protectionKey . '_initialized', false),
            'check_count' => Cache::get($this->protectionKey . '_count', 0),
            'last_check' => Cache::get($this->protectionKey . '_last', 0),
            'max_checks' => $this->maxChecks,
            'check_interval' => $this->checkInterval,
        ];
    }
}
