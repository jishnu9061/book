<?php

namespace App\Console\Commands;

use App\Services\LicenseValidationService;
use Illuminate\Console\Command;

class TestLicenseValidation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:test {--purchase-code=} {--domain=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test license validation functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LicenseValidationService $licenseService)
    {
        $this->info('Testing License Validation System...');
        $this->newLine();

        // Test 1: Check current license info
        $this->info('1. Current License Information:');
        $licenseInfo = $licenseService->getLicenseInfo();
        $this->table(
            ['Property', 'Value'],
            [
                ['Has License', $licenseInfo['has_license'] ? 'Yes' : 'No'],
                ['License Key', $licenseInfo['license_key'] ? substr($licenseInfo['license_key'], 0, 10) . '...' : 'None'],
                ['Activated Domain', $licenseInfo['activated_domain'] ?: 'None'],
                ['Is Verified', $licenseInfo['is_verified'] ? 'Yes' : 'No'],
            ]
        );

        // Test 2: Test license validation
        $this->newLine();
        $this->info('2. License Validation Test:');
        if ($licenseService->hasValidLicense()) {
            $this->info('✅ License is valid and verified');
        } else {
            $this->error('❌ License is invalid or not verified');
        }

        // Test 3: Test with custom purchase code if provided
        if ($purchaseCode = $this->option('purchase-code')) {
            $this->newLine();
            $this->info('3. Testing with custom purchase code:');
            $domain = $this->option('domain') ?: 'localhost';
            
            if ($licenseService->verifyWithServer($purchaseCode, $domain)) {
                $this->info('✅ Custom purchase code is valid');
            } else {
                $this->error('❌ Custom purchase code is invalid');
            }
        }

        // Test 4: Configuration check
        $this->newLine();
        $this->info('4. License Server Configuration:');
        $serverUrl = config('services.license.server_url');
        $itemId = config('services.license.item_id');
        
        $this->table(
            ['Setting', 'Value'],
            [
                ['Server URL', $serverUrl ?: 'Not configured'],
                ['Item ID', $itemId ?: 'Not configured'],
            ]
        );

        if (empty($serverUrl) || empty($itemId)) {
            $this->error('❌ License server configuration is incomplete');
            return 1;
        } else {
            $this->info('✅ License server configuration is complete');
        }

        $this->newLine();
        $this->info('License validation test completed!');
        
        return 0;
    }
}