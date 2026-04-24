<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'action', 'model_type',
        'model_id', 'description', 'changes', 'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Enregistre une action dans le journal d'audit
     */
    public static function log(string $action, $model, string $description, ?array $changes = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Système',
            'action' => $action,
            'model_type' => class_basename($model),
            'model_id' => $model->id ?? null,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }
}
