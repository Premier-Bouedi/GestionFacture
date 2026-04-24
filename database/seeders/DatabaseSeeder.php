<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Utilisateur Admin par défaut
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrateur',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        );

        // 0. Configuration Initiale (Toujours en premier)
        \App\Models\Setting::updateOrCreate(
            ['key' => 'company_name'],
            ['value' => 'Ma Super Entreprise']
        );

        // 1. TECHNIQUE HARICK : Création massive et propre (Factories)
        \App\Models\Client::factory(10)->create();

        // 2. TECHNIQUE MATOOR : Cas d'école et Catalogue (Seeders)
        $this->call(ProductSeeder::class);

        // 3. TEST DE LA NUMÉROTATION (Vision Matoor : Borderline 2025/2026)
        $client = \App\Models\Client::first();
        \App\Models\Invoice::create([
            'number' => 'FA-2025-045', // Dernière facture de l'an dernier
            'client_id' => $client->id,
            'invoice_date' => '2025-12-31',
            'created_at' => '2025-12-31 23:59:59', // Important pour l'algo de détection d'année
        ]);
    }
}
