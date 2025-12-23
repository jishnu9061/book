<?php

namespace App\Http\Middleware;

use App\Models\FrontPage;
use App\Models\Language;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Routes that should be excluded from certain shared data.
     */
    protected array $excludedRoutes = ['install', 'install/*', 'update', 'update/*'];

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Check if the current request is on an excluded route.
     */
    protected function isExcludedRoute(Request $request): bool
    {
        return $request->is($this->excludedRoutes);
    }

    /**
     * Check if database is available.
     */
    protected function isDatabaseAvailable(Request $request = null): bool
    {
        // Skip database check for installer routes
        if ($request && $this->isExcludedRoute($request)) {
            return false;
        }

        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            Log::warning('Database connection unavailable in HandleInertiaRequests', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get application settings with caching and error handling.
     */
    protected function getSettings(Request $request): array
    {
        if ($this->isExcludedRoute($request) || !$this->isDatabaseAvailable($request)) {
            return [];
        }

        return Cache::remember('app_settings', now()->addMinutes(60), function () {
            try {
                $settings = Setting::all();
                $settingsArray = [];
                
                foreach ($settings as $setting) {
                    // Decode JSON values for JSON type settings
                    if ($setting->type === 'json' && $setting->value) {
                        $settingsArray[$setting->slug] = json_decode($setting->value, true);
                    } else {
                        $settingsArray[$setting->slug] = $setting->value;
                    }
                }
                
                return $settingsArray;
            } catch (\Exception $e) {
                Log::error('Failed to fetch settings', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get enable options setting.
     */
    protected function getEnableOptions(Request $request): ?array
    {
        if ($this->isExcludedRoute($request) || !$this->isDatabaseAvailable($request)) {
            return null;
        }

        try {
            $setting = Setting::where('slug', 'enable_options')->first();
            if (!$setting) {
                return null;
            }
            // Return as array to avoid serialization issues
            return $setting->only(['id', 'name', 'slug', 'type', 'value']);
        } catch (\Exception $e) {
            Log::error('Failed to fetch enable_options setting', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get available languages.
     */
    protected function getLanguages(Request $request): array
    {
        if ($this->isExcludedRoute($request)) {
            return [];
        }

        if (!$this->isDatabaseAvailable($request)) {
            return $this->getDefaultLanguage();
        }

        try {
            $languages = Language::select('code', 'name', 'flag')->get();
            return $languages->isEmpty() ? $this->getDefaultLanguage() : $languages->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to fetch languages', ['error' => $e->getMessage()]);
            return $this->getDefaultLanguage();
        }
    }

    /**
     * Get default language fallback.
     */
    protected function getDefaultLanguage(): array
    {
        return [
            ['code' => 'en', 'name' => 'English', 'flag' => 'us']
        ];
    }

    /**
     * Get footer content.
     */
    protected function getFooter(Request $request): ?array
    {
        if ($this->isExcludedRoute($request) || $request->is('dashboard/*') || !$this->isDatabaseAvailable($request)) {
            return null;
        }

        try {
            $footer = FrontPage::where('slug', 'footer')->first();
            if (!$footer) {
                return null;
            }
            // Return as array to avoid serialization issues
            return $footer->only(['id', 'title', 'slug', 'is_active', 'html']);
        } catch (\Exception $e) {
            Log::error('Failed to fetch footer content', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get user data safely.
     */
    protected function getUserData(Request $request): ?array
    {
        $user = $request->user();

        if (!$user) {
            return null;
        }

        // Load the role relationship to ensure access permissions are available
        $user->load('role');

        return [
            'id' => $user->id,
            'first_name' => $user->first_name ?? '',
            'last_name' => $user->last_name ?? '',
            'email' => $user->email ?? '',
            'city' => $user->city ?? '',
            'locale' => $user->locale ?? 'en',
            'country_id' => $user->country_id ?? null,
            'role' => $user->role ?? ['slug' => 'na', 'name' => 'Not Assigned'],
            'access' => $user->access ?? [],
            'photo' => $user->photo_path ?? null,
        ];
    }

    /**
     * Get user notifications safely.
     */
    protected function getUserNotifications(Request $request): array
    {
        $user = $request->user();
        return $user ? $user->unreadNotifications->toArray() : [];
    }

    /**
     * Get notification count safely.
     */
    protected function getNotificationCount(Request $request): int
    {
        $user = $request->user();
        return $user ? $user->unreadNotifications()->count() : 0;
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $this->getUserData($request),
            ],
            'notifications' => $this->getUserNotifications($request),
            'notification_count' => $this->getNotificationCount($request),
            'settings' => $this->getSettings($request),
            'enable_options' => $this->getEnableOptions($request),
            'languages' => $this->getLanguages($request),
            'footer' => $this->getFooter($request),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
            ],
        ]);
    }

}
