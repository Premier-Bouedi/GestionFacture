<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS en production
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Configuration spécifique pour Vercel (Stockage en /tmp)
        if (isset($_SERVER['VERCEL_URL'])) {
            $tmpPath = '/tmp/storage/framework';
            if (!is_dir($tmpPath . '/views')) mkdir($tmpPath . '/views', 0777, true);
            if (!is_dir($tmpPath . '/cache')) mkdir($tmpPath . '/cache', 0777, true);
            if (!is_dir($tmpPath . '/sessions')) mkdir($tmpPath . '/sessions', 0777, true);
            
            config(['view.compiled' => $tmpPath . '/views']);
            config(['cache.stores.file.path' => $tmpPath . '/cache']);
            config(['session.files' => $tmpPath . '/sessions']);
        }

        // On vérifie si la table existe pour éviter les erreurs lors des migrations
        if (Schema::hasTable('settings')) {
            $companyName = Setting::where('key', 'company_name')->value('value') ?? 'Ma Société';
            $companyLogo = Setting::where('key', 'company_logo')->value('value');
            
            View::share('companyName', $companyName);
            View::share('companyLogo', $companyLogo);
        }
    }
}
