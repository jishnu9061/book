<?php

namespace App\Http\Controllers;

use App\Install\Requirement;
use App\Models\Role;
use App\Models\User;
use App\Services\LicenseCore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Exception;

class ModernInstallerController extends Controller
{
    protected $licenseCore;

    public function __construct(LicenseCore $licenseCore)
    {
        $this->licenseCore = $licenseCore;
    }

    /**
     * Check system requirements
     */
    public function checkRequirements()
    {
        try {
            $requirement = new Requirement();

            $requirements = [
                'php' => version_compare(phpversion(), '8.2.0', '>='),
                'extensions' => $requirement->extensions(),
                'permissions' => $requirement->directories()
            ];

            $serverInfo = [
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'memoryLimit' => ini_get('memory_limit'),
                'maxExecutionTime' => ini_get('max_execution_time')
            ];

            return response()->json([
                'success' => true,
                'phpVersion' => phpversion(),
                'requirements' => $requirements,
                'serverInfo' => $serverInfo
            ]);
        } catch (Exception $e) {
            \Log::error('Requirements check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check system requirements: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify purchase code using obfuscated license system
     */
    public function verifyLicense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase code is required'
            ], 400);
        }

        $purchaseCode = $request->input('purchase_code');

        // Use obfuscated license system for verification
        $result = $this->licenseCore->activate($purchaseCode);

        if ($result['success']) {
            // Store purchase code in .env for installer reference
            $env = DotenvEditor::load();
            $env->setKey('APP_PCE', $purchaseCode);
            $env->save();

            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Test database connection
     */
    public function testDatabase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'connection' => 'required|string|in:mysql,pgsql,sqlite',
            'host' => 'required_if:connection,mysql,pgsql|string',
            'port' => 'required_if:connection,mysql,pgsql|integer',
            'name' => 'required_if:connection,mysql,pgsql|string',
            'username' => 'required_if:connection,mysql,pgsql|string',
            'password' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid database configuration'
            ], 400);
        }

        try {
            $config = $request->all();

            // Test database connection
            $connection = $this->createTestConnection($config);
            DB::connection($connection)->getPdo();

            return response()->json([
                'success' => true,
                'message' => 'Database connection successful'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Save environment configuration
     */
    public function saveEnvironment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:50',
            'app_url' => 'required|url',
            'app_env' => 'required|string|in:production,staging,local',
            'app_debug' => 'boolean',
            'database_connection' => 'required|string|in:mysql,pgsql,sqlite',
            'database_hostname' => 'required_if:database_connection,mysql,pgsql|string',
            'database_port' => 'required_if:database_connection,mysql,pgsql|integer',
            'database_name' => 'required_if:database_connection,mysql,pgsql|string',
            'database_username' => 'required_if:database_connection,mysql,pgsql|string',
            'database_password' => 'nullable|string',
            'mail_driver' => 'required|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'pusher_app_id' => 'nullable|string',
            'pusher_app_key' => 'nullable|string',
            'pusher_app_secret' => 'nullable|string',
            'pusher_app_cluster' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid configuration data',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $env = DotenvEditor::load();

            // Application settings
            $env->setKey('APP_NAME', $request->input('app_name'));
            $env->setKey('APP_URL', $request->input('app_url'));
            $env->setKey('APP_ENV', $request->input('app_env'));
            $env->setKey('APP_DEBUG', $request->input('app_debug', false) ? 'true' : 'false');

            // Database settings
            $env->setKey('DB_CONNECTION', $request->input('database_connection'));
            $env->setKey('DB_HOST', $request->input('database_hostname', 'localhost'));
            $env->setKey('DB_PORT', $request->input('database_port', 3306));
            $env->setKey('DB_DATABASE', $request->input('database_name'));
            $env->setKey('DB_USERNAME', $request->input('database_username'));
            $env->setKey('DB_PASSWORD', $request->input('database_password', ''));

            // Mail settings
            $env->setKey('MAIL_MAILER', $request->input('mail_driver'));
            $env->setKey('MAIL_HOST', $request->input('mail_host', ''));
            $env->setKey('MAIL_PORT', $request->input('mail_port', 587));
            $env->setKey('MAIL_USERNAME', $request->input('mail_username', ''));
            $env->setKey('MAIL_PASSWORD', $request->input('mail_password', ''));
            $env->setKey('MAIL_ENCRYPTION', $request->input('mail_encryption', 'tls'));
            $env->setKey('MAIL_FROM_ADDRESS', $request->input('mail_from_address', ''));
            $env->setKey('MAIL_FROM_NAME', $request->input('app_name'));

            // Pusher settings
            $env->setKey('PUSHER_APP_ID', $request->input('pusher_app_id', ''));
            $env->setKey('PUSHER_APP_KEY', $request->input('pusher_app_key', ''));
            $env->setKey('PUSHER_APP_SECRET', $request->input('pusher_app_secret', ''));
            $env->setKey('PUSHER_APP_CLUSTER', $request->input('pusher_app_cluster', 'us2'));

            $env->save();

            return response()->json([
                'success' => true,
                'message' => 'Environment configuration saved successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run the installation process
     */
    public function runInstallation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid admin data',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Step 1: Run migrations (only if tables don't exist)
            if (!Schema::hasTable('users')) {
                \Log::info('Starting fresh migration');
                Artisan::call('migrate:fresh', ['--force' => true]);
                \Log::info('Fresh migration completed');

                // Step 2: Migrate temporary license data to database
                \Log::info('Migrating license data');
                $this->licenseCore->migrateTempLicenseToDatabase();
                \Log::info('License data migration completed');

                // Step 3: Seed the database
                \Log::info('Starting database seeding');
                Artisan::call('db:seed', ['--force' => true]);
                \Log::info('Database seeding completed');
            } else {
                // Database already exists, just migrate temporary license data
                \Log::info('Database exists, migrating license data only');
                $this->licenseCore->migrateTempLicenseToDatabase();
                \Log::info('License data migration completed');
            }

            // Step 4: Create admin role if it doesn't exist
            $adminRole = Role::where('slug', 'admin')->first();

            if (!$adminRole) {
                $adminRole = Role::create([
                    'name' => 'Administrator',
                    'slug' => 'admin'
                ]);
            }

            $admin = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role_id' => $adminRole->id,
                'email_verified_at' => now()
            ]);

            // Step 5: Mark as installed
            $env = DotenvEditor::load();
            $env->setKey('APP_INSTALLED', 'true');
            $env->setKey('APP_ENV', 'production');
            $env->setKey('APP_DEBUG', 'false');
            $env->save();

            // Create installed file for backward compatibility
            file_put_contents(storage_path('installed'), 'Installation completed on ' . now());

            // Step 6: Clear cache
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            return response()->json([
                'success' => true,
                'message' => 'Installation completed successfully',
                'admin' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Installation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a test database connection
     */
    private function createTestConnection($config)
    {
        $connectionName = 'installer_test';

        config([
            "database.connections.{$connectionName}" => [
                'driver' => $config['connection'],
                'host' => $config['host'] ?? 'localhost',
                'port' => $config['port'] ?? 3306,
                'database' => $config['name'] ?? '',
                'username' => $config['username'] ?? '',
                'password' => $config['password'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => false,
                'engine' => null,
            ]
        ]);

        return $connectionName;
    }
}
