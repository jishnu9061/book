<?php

namespace App\Providers;

use App\Services\KernelStatusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Model::unguard();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(KernelStatusService $statusService)
    {

        if (!$statusService->isVerified()) {
            View::share('license_invalid', true);
        } else {
            View::share('license_invalid', false);
        }

        Inertia::share([
            'locale' => function () {
                $user = Auth()->user();
                if(!empty($user['locale'])){
                    return $user['locale'];
                }else{
                    return app()->getLocale();
                }
            },
            'language' => function () {
                $user = Auth()->user();
                if(!empty($user['locale'])){
                    $locale = $user['locale'];
                }else{
                    $locale = app()->getLocale();
                }
                return translations(
                    language_path($locale . '.json')
                );
            },
            'dir' => function () {
                $user = Auth()->user();
                if(!empty($user['locale'])){
                    $locale = $user['locale'];
                }else{
                    $locale = app()->getLocale();
                }
                $rtlCodes = ['sa','he','ur'];
                return in_array($locale, $rtlCodes) ? 'rtl' : 'ltr';
            }
        ]);
    }
}
