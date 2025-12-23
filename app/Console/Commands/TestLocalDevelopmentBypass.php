<?php

namespace App\Console\Commands;

use App\Services\LicenseCore;
use App\Http\Middleware\LicenseGuard;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestLocalDevelopmentBypass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:local-development-bypass {--env=} {--debug=} {--host=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test local development license bypass functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LicenseCore $licenseCore)
    {
        $this->info('Testing Local Development License Bypass...');
        $this->newLine();

        // Test 1: Current Environment Check
        $this->info('1. Current Environment Check:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['APP_ENV', config('app.env')],
                ['APP_DEBUG', config('app.debug') ? 'true' : 'false'],
                ['Current Host', request()->getHost()],
                ['Current URL', request()->getHttpHost()],
            ]
        );

        // Test 2: License Core Local Development Check
        $this->newLine();
        $this->info('2. License Core Local Development Check:');
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($licenseCore);
        $method = $reflection->getMethod('_isLocalDevelopment');
        $method->setAccessible(true);
        
        $isLocalDev = $method->invoke($licenseCore);
        $this->info('Is Local Development: ' . ($isLocalDev ? 'Yes' : 'No'));

        // Test 3: License Validation Test
        $this->newLine();
        $this->info('3. License Validation Test:');
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

        // Test 4: License Guard Local Development Check
        $this->newLine();
        $this->info('4. License Guard Local Development Check:');
        
        $licenseGuard = new LicenseGuard($licenseCore);
        $guardReflection = new \ReflectionClass($licenseGuard);
        $guardMethod = $guardReflection->getMethod('isLocalDevelopment');
        $guardMethod->setAccessible(true);
        
        $isLocalDevGuard = $guardMethod->invoke($licenseGuard);
        $this->info('License Guard Local Development: ' . ($isLocalDevGuard ? 'Yes' : 'No'));

        // Test 5: Different Host and Port Scenarios
        $this->newLine();
        $this->info('5. Different Host and Port Scenarios:');
        
        $scenarios = [
            ['localhost', 8900, 'Yes', 'Correct host and port'],
            ['127.0.0.1', 8900, 'Yes', 'Correct host and port'],
            ['localhost', 8000, 'No', 'Wrong port'],
            ['127.0.0.1', 8000, 'No', 'Wrong port'],
            ['192.168.1.100', 8900, 'No', 'Wrong host'],
            ['example.com', 8900, 'No', 'Wrong host'],
            ['localhost', 3000, 'No', 'Wrong port'],
            ['127.0.0.1', 3000, 'No', 'Wrong port'],
        ];

        $this->table(
            ['Host', 'Port', 'Expected Bypass', 'Description'],
            array_map(function($scenario) {
                return [
                    $scenario[0],
                    $scenario[1],
                    $scenario[2],
                    $scenario[3]
                ];
            }, $scenarios)
        );

        // Test 6: Custom Environment Test
        if ($env = $this->option('env')) {
            $this->newLine();
            $this->info('6. Custom Environment Test:');
            $this->info("Testing with APP_ENV: {$env}");
            
            // Temporarily change environment
            config(['app.env' => $env]);
            
            $isLocalDevCustom = $method->invoke($licenseCore);
            $this->info('Is Local Development (Custom): ' . ($isLocalDevCustom ? 'Yes' : 'No'));
        }

        // Test 7: Custom Host and Port Test
        if ($host = $this->option('host')) {
            $this->newLine();
            $this->info('7. Custom Host and Port Test:');
            $this->info("Testing with host: {$host}");
            
            // Test with port 8900
            $request = Request::create('http://' . $host . ':8900');
            app()->instance('request', $request);
            
            $isLocalDevHost = $method->invoke($licenseCore);
            $this->info('Is Local Development (Custom Host:8900): ' . ($isLocalDevHost ? 'Yes' : 'No'));
            
            // Test with port 8000
            $request = Request::create('http://' . $host . ':8000');
            app()->instance('request', $request);
            
            $isLocalDevHost8000 = $method->invoke($licenseCore);
            $this->info('Is Local Development (Custom Host:8000): ' . ($isLocalDevHost8000 ? 'Yes' : 'No'));
        }

        // Test 8: Summary
        $this->newLine();
        $this->info('8. Summary:');
        
        if ($isLocalDev) {
            $this->info('✅ License validation is BYPASSED for local development');
            $this->info('✅ You can develop without a license key');
            $this->info('✅ All features are accessible');
        } else {
            $this->warn('⚠️  License validation is ACTIVE');
            $this->warn('⚠️  You need a valid license key');
            $this->warn('⚠️  Some features may be restricted');
        }

        $this->newLine();
        $this->info('Local development bypass test completed!');
        
        return 0;
    }
}