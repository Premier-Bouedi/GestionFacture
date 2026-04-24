<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Mettre TOUS les utilisateurs en admin
\App\Models\User::query()->update(['role' => 'admin']);

$users = \App\Models\User::all();
echo "=== TOUS LES UTILISATEURS SONT MAINTENANT ADMIN ===\n";
foreach ($users as $u) {
    echo "ID: {$u->id} | {$u->name} | {$u->email} | Rôle: {$u->role}\n";
}
