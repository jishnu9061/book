<?php

namespace App\Console\Commands;

use App\Services\LicenseCore;
use App\Services\CodeIntegrityChecker;
use App\Services\RuntimeProtection;
use Illuminate\Console\Command;

class TestObfuscatedLicense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:test-obfuscated {--purchase-code=} {--domain=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the obfuscated license validation system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LicenseCore $core, CodeIntegrityChecker $integrity, RuntimeProtection $protection)
    {
        $this->info('Testing Obfuscated License Protection System...');
        $this->newLine();

        // Test 1: Code Integrity
        $this->info('1. Code Integrity Check:');
        if ($integrity->checkIntegrity()) {
            $this->info('✅ Code integrity check passed');
        } else {
            $this->error('❌ Code integrity violations detected');
        }

        // Test 2: Runtime Protection
        $this->newLine();
        $this->info('2. Runtime Protection Check:');
        if ($protection->isProtected()) {
            $this->info('✅ Runtime protection is active');
        } else {
            $this->error('❌ Runtime protection failed');
        }

        // Test 3: License Core Functionality
        $this->newLine();
        $this->info('3. License Core Functionality:');
        $licenseInfo = $core->info();
        $this->table(
            ['Property', 'Value'],
            [
                ['Has License', $licenseInfo['has_license'] ? 'Yes' : 'No'],
                ['License Key', $licenseInfo['license_key'] ? substr($licenseInfo['license_key'], 0, 10) . '...' : 'None'],
                ['Activated Domain', $licenseInfo['activated_domain'] ?: 'None'],
                ['Is Verified', $licenseInfo['is_verified'] ? 'Yes' : 'No'],
            ]
        );

        // Test 4: License Validation
        $this->newLine();
        $this->info('4. License Validation Test:');
        if ($core->check()) {
            $this->info('✅ License validation passed');
        } else {
            $this->error('❌ License validation failed');
        }

        // Test 5: Custom Purchase Code Test
        if ($purchaseCode = $this->option('purchase-code')) {
            $this->newLine();
            $this->info('5. Custom Purchase Code Test:');
            $domain = $this->option('domain') ?: 'localhost';
            
            // Use reflection to access private method for testing
            $reflection = new \ReflectionClass($core);
            $method = $reflection->getMethod('_f');
            $method->setAccessible(true);
            
            if ($method->invoke($core, $purchaseCode, $domain)) {
                $this->info('✅ Custom purchase code is valid');
            } else {
                $this->error('❌ Custom purchase code is invalid');
            }
        }

        // Test 6: Middleware Registration
        $this->newLine();
        $this->info('6. Middleware Registration Check:');
        $kernelFile = app_path('Http/Kernel.php');
        $kernelContent = file_get_contents($kernelFile);
        
        $hasLicenseGuard = strpos($kernelContent, 'LicenseGuard::class') !== false;
        $hasOldValidator = strpos($kernelContent, 'AppIntegrityValidator::class') !== false;
        
        if ($hasLicenseGuard) {
            $this->info('✅ LicenseGuard middleware is registered');
        } else {
            $this->error('❌ LicenseGuard middleware is not registered');
        }

        if ($hasOldValidator) {
            $this->warn('⚠️  Old AppIntegrityValidator middleware is still registered');
        }

        // Test 7: File Obfuscation Check
        $this->newLine();
        $this->info('7. File Obfuscation Check:');
        $obfuscatedFiles = [
            'app/Services/LicenseCore.php',
            'app/Http/Middleware/LicenseGuard.php',
        ];

        foreach ($obfuscatedFiles as $file) {
            $fullPath = base_path($file);
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $hasObfuscatedMethods = strpos($content, '_a(') !== false || 
                                      strpos($content, '_b(') !== false || 
                                      strpos($content, '_c(') !== false;
                
                if ($hasObfuscatedMethods) {
                    $this->info("✅ {$file} appears to be obfuscated");
                } else {
                    $this->warn("⚠️  {$file} may not be properly obfuscated");
                }
            } else {
                $this->error("❌ {$file} not found");
            }
        }

        // Test 8: Protection Status
        $this->newLine();
        $this->info('8. Protection Status:');
        $status = $protection->getProtectionStatus();
        $this->table(
            ['Property', 'Value'],
            [
                ['Initialized', $status['initialized'] ? 'Yes' : 'No'],
                ['Check Count', $status['check_count']],
                ['Last Check', date('Y-m-d H:i:s', $status['last_check'])],
                ['Max Checks', $status['max_checks']],
                ['Check Interval', $status['check_interval'] . ' seconds'],
            ]
        );

        $this->newLine();
        $this->info('Obfuscated license protection test completed!');
        
        return 0;
    }
}