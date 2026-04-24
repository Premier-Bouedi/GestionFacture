<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all();
echo "=== LISTE DES UTILISATEURS ===\n";
foreach ($users as $u) {
    echo "ID: {$u->id} | Nom: {$u->name} | Email: {$u->email} | Rôle: {$u->role}\n";
}
echo "\nTotal: " . $users->count() . " utilisateur(s)\n";
