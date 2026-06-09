<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Statut logistique de livraison + paiement (éligibilité bon de décharge).
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('statut_livraison')->default('Non livré')->after('logo_path');
            $table->string('statut_paiement')->default('Payée')->after('statut_livraison');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['statut_livraison', 'statut_paiement']);
        });
    }
};
