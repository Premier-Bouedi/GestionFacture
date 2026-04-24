<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'magnagamakelighiclainn@gmail.com';
$user = User::where('email', $email)->first();

if ($user) {
    $user->update(['role' => 'admin']);
    echo "SUCCESS: Existing user $email is now ADMIN.\n";
} else {
    User::create([
        'name' => 'Claïnn Admin',
        'email' => $email,
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);
    echo "SUCCESS: New user $email has been created with ADMIN role.\n";
}
