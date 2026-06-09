<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Statut d'envoi email et chemin du logo personnalisé sur la facture.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status_email')->default('En attente')->after('total_ttc');
            $table->string('logo_path')->nullable()->after('status_email');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['status_email', 'logo_path']);
        });
    }
};
