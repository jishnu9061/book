<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseCore
{
    private const C1 = 'license_verification_status';
    private const C2 = 'license_key';
    private const C3 = 6;
    private const C4 = 1;
    private const C5 = 'https://api.softentra.com';
    private const C6 = '40309046';

    private function _a($k) {
        // First check if database is ready and has the setting
        if ($this->_isDatabaseReady()) {
            return Cache::remember($k, now()->addHours(self::C3), function () use ($k) {
                return Setting::where('slug', $k === self::C2 ? 'license_key' : 'license_domain')->value('value');
            });
        }
        
        // If database not ready, check for temporary cache values during installation
        $tempKey = $k === self::C2 ? 'temp_license_key' : 'temp_license_domain';
        return Cache::get($tempKey);
    }

    private function _b($k) {
        return Cache::get($k);
    }

    private function _c($k, $v, $t) {
        Cache::put($k, $v, $t);
    }

    private function _d($k) {
        Cache::forget($k);
    }

    private function _e($url, $data) {
        try {
            $response = Http::timeout(10)->post($url, $data);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('License verification error: ' . $e->getMessage());
            return null;
        }
    }

    private function _f($code, $domain = null) {
        $domain = $domain ?: request()->getHttpHost();
        $data = [
            'purchase_code' => $code,
            'domain' => $domain,
            'item_id' => self::C6,
        ];

        $result = $this->_e(self::C5 . '/api/verify', $data);
        return $result && isset($result['valid']) && $result['valid'] === true;
    }

    private function _g($code, $domain = null) {
        $domain = $domain ?: request()->getHttpHost();
        $data = [
            'purchase_code' => $code,
            'domain' => $domain,
            'item_id' => self::C6,
        ];

        $result = $this->_e(self::C5 . '/api/activate', $data);
        return $result;
    }

    private function _h($domain = null) {
        $key = $this->_a(self::C2);
        if (!$key) return ['success' => false, 'message' => 'No license key found'];

        $domain = $domain ?: $this->_a('license_domain') ?: request()->getHttpHost();
        $data = [
            'purchase_code' => $key,
            'domain' => $domain,
            'item_id' => self::C6,
        ];

        $result = $this->_e(self::C5 . '/api/deactivate', $data);
        return $result ?: ['success' => false, 'message' => 'Deactivation failed'];
    }

    public function check() {
        // Skip license validation for local development
        if ($this->_isLocalDevelopment()) {
            return true;
        }

        if ($this->_b(self::C1)) return true;

        $key = $this->_a(self::C2);
        if (!$key) return false;

        if ($this->_f($key)) {
            $this->_c(self::C1, true, now()->addDays(self::C4));
            return true;
        }

        return false;
    }

    public function getKey() {
        return $this->_a(self::C2);
    }

    public function getDomain() {
        return $this->_a('license_domain');
    }

    public function activate($code) {
        $result = $this->_g($code);
        if ($result && isset($result['success']) && $result['success'] === true) {
            // Check if database is ready before saving to settings table
            if ($this->_isDatabaseReady()) {
                Setting::updateOrCreate(['slug' => 'license_key'], ['value' => $code]);
                Setting::updateOrCreate(['slug' => 'license_domain'], ['value' => request()->getHttpHost()]);
            } else {
                // Store temporarily in cache during installation
                $this->_c('temp_license_key', $code, now()->addHours(24));
                $this->_c('temp_license_domain', request()->getHttpHost(), now()->addHours(24));
            }
            $this->clear();
            return ['success' => true, 'message' => $result['message'] ?? 'License activated successfully'];
        }
        return ['success' => false, 'message' => $result['message'] ?? 'License activation failed'];
    }

    public function deactivate() {
        $result = $this->_h();
        if ($result['success']) {
            Setting::whereIn('slug', ['license_key', 'license_domain'])->delete();
            $this->clear();
        }
        return $result;
    }

    public function clear() {
        $this->_d(self::C1);
        $this->_d(self::C2);
    }

    public function migrateTempLicenseToDatabase() {
        // This method should be called after database setup is complete
        if (!$this->_isDatabaseReady()) {
            return false;
        }

        $tempKey = Cache::get('temp_license_key');
        $tempDomain = Cache::get('temp_license_domain');

        if ($tempKey && $tempDomain) {
            Setting::updateOrCreate(['slug' => 'license_key'], ['value' => $tempKey]);
            Setting::updateOrCreate(['slug' => 'license_domain'], ['value' => $tempDomain]);
            
            // Clear temporary cache
            Cache::forget('temp_license_key');
            Cache::forget('temp_license_domain');
            
            return true;
        }

        return false;
    }

    public function info() {
        return [
            'has_license' => !empty($this->getKey()),
            'license_key' => $this->getKey(),
            'activated_domain' => $this->getDomain(),
            'is_verified' => $this->check(),
        ];
    }

    private function _isLocalDevelopment() {
        // Check if running in local development environment with specific host and port
        return $this->_isLocalhostWithDevelopmentPort();
    }

    private function _isLocalhostWithDevelopmentPort() {
        $host = request()->getHost();
        $port = request()->getPort();

        // Allow localhost and 127.0.0.1 with common development ports
        return ($host === 'localhost' || $host === '127.0.0.1') && in_array($port, [8000, 8900, 3000, 8080]);
    }

    private function _isDatabaseReady() {
        try {
            // Check if database connection is available
            if (!\Illuminate\Support\Facades\DB::connection()->getPdo()) {
                return false;
            }
            
            // Check if settings table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
