<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Message — Messagerie interne entre utilisateurs.
 *
 * @property int    $id
 * @property int    $sender_id    Expéditeur
 * @property int    $receiver_id  Destinataire
 * @property string $content      Contenu du message
 * @property bool   $is_read      Lu ou non
 */
class Message extends Model
{
    /**
     * Colonnes remplissables en masse.
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'is_read',
    ];

    /**
     * Conversions de types automatiques.
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relation : l'expéditeur du message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relation : le destinataire du message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
