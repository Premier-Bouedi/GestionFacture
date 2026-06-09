<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'magnagamakelighiclainn@gmail.com';
$user = \App\Models\User::where('email', $email)->first();

if ($user) {
    echo "Utilisateur trouvé : " . $user->email . "\n";
    $user->password = \Illuminate\Support\Facades\Hash::make('admin123');
    $user->save();
    echo "✅ Mot de passe mis à jour vers 'admin123' pour le projet Gestion de Facture.\n";
} else {
    echo "❌ Utilisateur non trouvé dans Gestion de Facture.\n";
}
