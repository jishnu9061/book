<?php

namespace App\Console\Commands;

use App\Services\LicenseCore;
use App\Services\CodeIntegrityChecker;
use App\Services\RuntimeProtection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class TestInstallationLicenseFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:installation-license-flow {--purchase-code=} {--domain=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the complete installation and license validation flow';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LicenseCore $licenseCore, CodeIntegrityChecker $integrity, RuntimeProtection $protection)
    {
        $this->info('Testing Installation and License Validation Flow...');
        $this->newLine();

        // Test 1: Check if installer routes are accessible
        $this->info('1. Installer Route Accessibility:');
        $installerRoutes = [
            'installer.index',
            'installer.check-requirements',
            'installer.verify-license',
            'installer.test-database',
            'installer.save-environment',
            'installer.run-installation',
        ];

        foreach ($installerRoutes as $routeName) {
            try {
                $route = Route::getRoutes()->getByName($routeName);
                if ($route) {
                    $this->info("✅ {$routeName}: " . $route->uri());
                } else {
                    $this->error("❌ {$routeName}: Route not found");
                }
            } catch (\Exception $e) {
                $this->error("❌ {$routeName}: Error - " . $e->getMessage());
            }
        }

        // Test 2: Check middleware configuration
        $this->newLine();
        $this->info('2. Middleware Configuration:');
        
        // Check if LicenseGuard is in web middleware group
        $kernelFile = app_path('Http/Kernel.php');
        $kernelContent = file_get_contents($kernelFile);
        
        $hasLicenseGuard = strpos($kernelContent, 'LicenseGuard::class') !== false;
        $hasInstallerMiddleware = strpos($kernelContent, 'RedirectToInstallerIfNotInstalled::class') !== false;
        $hasMiddlewareAliases = strpos($kernelContent, 'middlewareAliases') !== false;
        
        if ($hasLicenseGuard) {
            $this->info('✅ LicenseGuard middleware is registered');
        } else {
            $this->error('❌ LicenseGuard middleware is not registered');
        }

        if ($hasInstallerMiddleware) {
            $this->info('✅ RedirectToInstallerIfNotInstalled middleware is registered');
        } else {
            $this->error('❌ RedirectToInstallerIfNotInstalled middleware is not registered');
        }

        if ($hasMiddlewareAliases) {
            $this->info('✅ Laravel 12 middlewareAliases structure detected');
        } else {
            $this->warn('⚠️  Using legacy routeMiddleware structure (not Laravel 12 compatible)');
        }

        // Test 3: Check installer middleware bypass
        $this->newLine();
        $this->info('3. Installer Middleware Bypass:');
        
        // Check if LicenseGuard properly bypasses installer routes
        $licenseGuardFile = app_path('Http/Middleware/LicenseGuard.php');
        $licenseGuardContent = file_get_contents($licenseGuardFile);
        
        $hasInstallerBypass = strpos($licenseGuardContent, 'isInstaller($request)') !== false;
        $hasInstallRouteCheck = strpos($licenseGuardContent, "is('install*')") !== false;
        $hasUpdateRouteCheck = strpos($licenseGuardContent, "is('update*')") !== false;
        
        if ($hasInstallerBypass && $hasInstallRouteCheck && $hasUpdateRouteCheck) {
            $this->info('✅ LicenseGuard properly bypasses installer routes');
        } else {
            $this->error('❌ LicenseGuard does not properly bypass installer routes');
            $this->error("   - hasInstallerBypass: " . ($hasInstallerBypass ? 'Yes' : 'No'));
            $this->error("   - hasInstallRouteCheck: " . ($hasInstallRouteCheck ? 'Yes' : 'No'));
            $this->error("   - hasUpdateRouteCheck: " . ($hasUpdateRouteCheck ? 'Yes' : 'No'));
        }

        // Test 4: Check license validation integration
        $this->newLine();
        $this->info('4. License Validation Integration:');
        
        // Check if ModernInstallerController uses LicenseCore
        $installerControllerFile = app_path('Http/Controllers/ModernInstallerController.php');
        $installerControllerContent = file_get_contents($installerControllerFile);
        
        $usesLicenseCore = strpos($installerControllerContent, 'LicenseCore') !== false;
        $hasLicenseActivation = strpos($installerControllerContent, 'licenseCore->activate') !== false;
        
        if ($usesLicenseCore && $hasLicenseActivation) {
            $this->info('✅ ModernInstallerController uses obfuscated LicenseCore');
        } else {
            $this->error('❌ ModernInstallerController does not use obfuscated LicenseCore');
        }

        // Test 5: Check installer Vue components
        $this->newLine();
        $this->info('5. Installer Vue Components:');
        
        $installerComponents = [
            'resources/js/Pages/Installer/Index.vue',
            'resources/js/Pages/Installer/Steps/License.vue',
        ];

        foreach ($installerComponents as $component) {
            if (File::exists(base_path($component))) {
                $this->info("✅ {$component}: Exists");
            } else {
                $this->error("❌ {$component}: Missing");
            }
        }

        // Test 6: Check license verification endpoint
        $this->newLine();
        $this->info('6. License Verification Endpoint:');
        
        $hasVerifyEndpoint = strpos($installerControllerContent, 'verifyLicense') !== false;
        $hasPostMethod = strpos($installerControllerContent, 'public function verifyLicense') !== false;
        
        if ($hasVerifyEndpoint && $hasPostMethod) {
            $this->info('✅ License verification endpoint exists');
        } else {
            $this->error('❌ License verification endpoint missing');
        }

        // Test 7: Check installation completion flow
        $this->newLine();
        $this->info('7. Installation Completion Flow:');
        
        $hasRunInstallation = strpos($installerControllerContent, 'runInstallation') !== false;
        $hasMarkInstalled = strpos($installerControllerContent, 'APP_INSTALLED') !== false;
        $hasCacheClear = strpos($installerControllerContent, 'config:cache') !== false;
        
        if ($hasRunInstallation && $hasMarkInstalled && $hasCacheClear) {
            $this->info('✅ Installation completion flow is properly configured');
        } else {
            $this->error('❌ Installation completion flow is incomplete');
        }

        // Test 8: Check post-installation license flow
        $this->newLine();
        $this->info('8. Post-Installation License Flow:');
        
        // Check if license routes are accessible after installation
        $licenseRoutes = [
            'license.show',
            'license.activate',
            'license.settings',
            'license.deactivate',
        ];

        foreach ($licenseRoutes as $routeName) {
            try {
                $route = Route::getRoutes()->getByName($routeName);
                if ($route) {
                    $this->info("✅ {$routeName}: " . $route->uri());
                } else {
                    $this->error("❌ {$routeName}: Route not found");
                }
            } catch (\Exception $e) {
                $this->error("❌ {$routeName}: Error - " . $e->getMessage());
            }
        }

        // Test 9: Check license core functionality
        $this->newLine();
        $this->info('9. License Core Functionality:');
        
        $licenseInfo = $licenseCore->info();
        $this->table(
            ['Property', 'Value'],
            [
                ['Has License', $licenseInfo['has_license'] ? 'Yes' : 'No'],
                ['License Key', $licenseInfo['license_key'] ? substr($licenseInfo['license_key'], 0, 10) . '...' : 'None'],
                ['Activated Domain', $licenseInfo['activated_domain'] ?: 'None'],
                ['Is Verified', $licenseInfo['is_verified'] ? 'Yes' : 'No'],
            ]
        );

        // Test 10: Check integrity and protection
        $this->newLine();
        $this->info('10. Integrity and Protection:');
        
        if ($integrity->checkIntegrity()) {
            $this->info('✅ Code integrity check passed');
        } else {
            $this->error('❌ Code integrity violations detected');
        }

        if ($protection->isProtected()) {
            $this->info('✅ Runtime protection is active');
        } else {
            $this->error('❌ Runtime protection failed');
        }

        // Test 11: Custom purchase code test (if provided)
        if ($purchaseCode = $this->option('purchase-code')) {
            $this->newLine();
            $this->info('11. Custom Purchase Code Test:');
            $domain = $this->option('domain') ?: 'localhost';
            
            $result = $licenseCore->activate($purchaseCode);
            
            if ($result['success']) {
                $this->info('✅ Custom purchase code activation successful');
                $this->info("Message: {$result['message']}");
            } else {
                $this->error('❌ Custom purchase code activation failed');
                $this->error("Message: {$result['message']}");
            }
        }

        // Test 12: Check installation status
        $this->newLine();
        $this->info('12. Installation Status:');
        
        $installedFile = storage_path('installed');
        $envInstalled = config('app.installed', false);
        
        if (File::exists($installedFile)) {
            $this->info('✅ Installation file exists');
        } else {
            $this->warn('⚠️  Installation file does not exist (normal for fresh installation)');
        }

        if ($envInstalled) {
            $this->info('✅ APP_INSTALLED is set to true');
        } else {
            $this->warn('⚠️  APP_INSTALLED is not set (normal for fresh installation)');
        }

        $this->newLine();
        $this->info('Installation and License Flow Test Completed!');
        
        return 0;
    }
}