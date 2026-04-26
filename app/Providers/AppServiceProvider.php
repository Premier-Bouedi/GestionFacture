<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

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
        // Partage des réglages de l'entreprise avec toutes les vues
        try {
            if (DB::connection()->getPdo() && Schema::hasTable('settings')) {
                $companyName = Setting::where('key', 'company_name')->value('value') ?? 'Ma Société';
                $companyLogo = Setting::where('key', 'company_logo')->value('value');
                
                View::share('companyName', $companyName);
                View::share('companyLogo', $companyLogo);
            }
        } catch (\Exception $e) {
            View::share('companyName', 'Gestion Facture');
            View::share('companyLogo', null);
        }
    }
}
