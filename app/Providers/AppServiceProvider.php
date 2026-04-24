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
            config(['view.compiled' => '/tmp/views']);
            config(['cache.stores.file.path' => '/tmp/cache']);
            config(['session.files' => '/tmp/sessions']);
            
            // On s'assure que les dossiers existent
            if (!is_dir('/tmp/views')) mkdir('/tmp/views', 0777, true);
        }

        // On sécurise l'accès à la base de données pour éviter le crash 500 sur Vercel
        try {
            if (\Illuminate\Support\Facades\DB::connection()->getPdo() && Schema::hasTable('settings')) {
                $companyName = Setting::where('key', 'company_name')->value('value') ?? 'Ma Société';
                $companyLogo = Setting::where('key', 'company_logo')->value('value');
                
                View::share('companyName', $companyName);
                View::share('companyLogo', $companyLogo);
            }
        } catch (\Exception $e) {
            // En cas d'erreur de base de données, on définit des valeurs par défaut
            View::share('companyName', 'Gestion Facture');
            View::share('companyLogo', null);
        }
    }
}
