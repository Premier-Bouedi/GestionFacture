<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'magnagamakelighiclainn@gmail.com')->first();
if ($user) {
    $user->password = Hash::make('password');
    $user->save();
    echo "Compte mis à jour avec succès.\n";
} else {
    echo "Utilisateur non trouvé.\n";
}
