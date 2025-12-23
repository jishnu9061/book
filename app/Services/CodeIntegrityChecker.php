<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CodeIntegrityChecker
{
    private $criticalFiles = [
        'app/Services/LicenseCore.php',
        'app/Http/Middleware/LicenseGuard.php',
        'app/Http/Controllers/LicenseController.php',
        'app/Http/Kernel.php',
        'config/services.php',
    ];

    private $expectedHashes = [
        // These will be generated and stored securely
    ];

    public function checkIntegrity()
    {
        $violations = [];

        foreach ($this->criticalFiles as $file) {
            $fullPath = base_path($file);
            
            if (!File::exists($fullPath)) {
                $violations[] = "Missing critical file: {$file}";
                continue;
            }

            // Check file size (too small might indicate tampering)
            $size = File::size($fullPath);
            if ($size < 100) {
                $violations[] = "Suspicious file size for: {$file}";
            }

            // Check for expected content
            $content = File::get($fullPath);
            if (!$this->hasExpectedContent($file, $content)) {
                $violations[] = "Unexpected content in: {$file}";
            }

            // Check for suspicious patterns
            if ($this->hasSuspiciousPatterns($content)) {
                $violations[] = "Suspicious patterns detected in: {$file}";
            }
        }

        // Check middleware registration
        if (!$this->isMiddlewareRegistered()) {
            $violations[] = "License middleware not properly registered";
        }

        // Check route protection
        if (!$this->areRoutesProtected()) {
            $violations[] = "Protected routes are not properly secured";
        }

        if (!empty($violations)) {
            Log::warning('Code integrity violations detected', [
                'violations' => $violations,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ]);
            
            // For debugging - output violations to console
            if (app()->runningInConsole()) {
                foreach ($violations as $violation) {
                    echo "VIOLATION: {$violation}\n";
                }
            }
        }

        return empty($violations);
    }

    private function hasExpectedContent($file, $content)
    {
        $expectedPatterns = [
            'app/Services/LicenseCore.php' => ['LicenseCore', 'check()', 'activate(', 'deactivate('],
            'app/Http/Middleware/LicenseGuard.php' => ['LicenseGuard', 'handle(', 'integrityCheck('],
            'app/Http/Controllers/LicenseController.php' => ['LicenseController', 'activate(', 'deactivate('],
            'app/Http/Kernel.php' => ['LicenseGuard', 'middlewareGroups'],
            'config/services.php' => ['license', 'server_url', 'item_id'],
        ];

        if (!isset($expectedPatterns[$file])) {
            return true;
        }

        foreach ($expectedPatterns[$file] as $pattern) {
            if (strpos($content, $pattern) === false) {
                return false;
            }
        }

        return true;
    }

    private function hasSuspiciousPatterns($content)
    {
        $suspiciousPatterns = [
            '// bypass license',
            '// skip license',
            'return true; // bypass',
            'if (false)',
            'if (1 == 1)',
            'if (true)',
            '// TODO: remove license',
            '// FIXME: remove license',
            '// HACK: remove',
            '// TEMP: remove',
            '// DEBUG: remove',
            'var_dump(',
            'dd(',
            'dump(',
            'exit(',
            'die(',
            '// disable license',
            '// remove license',
            '// comment out license',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    private function isMiddlewareRegistered()
    {
        // Check if LicenseGuard class exists and is loadable
        if (!class_exists(\App\Http\Middleware\LicenseGuard::class)) {
            return false;
        }

        // Check if the middleware is in the Kernel file
        $kernelFile = app_path('Http/Kernel.php');
        if (!File::exists($kernelFile)) {
            return false;
        }

        $kernelContent = File::get($kernelFile);
        return strpos($kernelContent, 'LicenseGuard::class') !== false ||
               strpos($kernelContent, 'AppIntegrityValidator::class') !== false ||
               strpos($kernelContent, 'middlewareAliases') !== false;
    }

    private function areRoutesProtected()
    {
        // Check if web middleware group has license protection
        $kernelFile = app_path('Http/Kernel.php');
        if (!File::exists($kernelFile)) {
            return false;
        }

        $kernelContent = File::get($kernelFile);
        
        // Check if LicenseGuard is in web middleware group
        $hasLicenseGuard = strpos($kernelContent, 'LicenseGuard::class') !== false;
        $hasWebGroup = strpos($kernelContent, "'web' => [") !== false;
        
        return $hasLicenseGuard && $hasWebGroup;
    }

    public function generateFileHashes()
    {
        $hashes = [];
        
        foreach ($this->criticalFiles as $file) {
            $fullPath = base_path($file);
            if (File::exists($fullPath)) {
                $hashes[$file] = Hash::make(File::get($fullPath));
            }
        }

        return $hashes;
    }

    public function validateFileHashes()
    {
        if (empty($this->expectedHashes)) {
            return true; // No hashes to validate against
        }

        foreach ($this->criticalFiles as $file) {
            $fullPath = base_path($file);
            if (File::exists($fullPath) && isset($this->expectedHashes[$file])) {
                $currentHash = Hash::make(File::get($fullPath));
                if ($currentHash !== $this->expectedHashes[$file]) {
                    return false;
                }
            }
        }

        return true;
    }
}
