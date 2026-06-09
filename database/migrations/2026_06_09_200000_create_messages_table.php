<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table messages
 * Permet la messagerie interne entre utilisateurs (Patron ↔ Caissières).
 */
return new class extends Migration
{
    /**
     * Crée la table messages avec les colonnes nécessaires.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // Expéditeur du message (clé étrangère vers users)
            $table->foreignId('sender_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Destinataire du message (clé étrangère vers users)
            $table->foreignId('receiver_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Contenu textuel du message
            $table->text('content');

            // Indique si le destinataire a lu le message (false = non lu)
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Supprime la table messages lors du rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
