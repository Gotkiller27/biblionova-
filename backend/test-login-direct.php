<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;

echo "Testing login directly...\n";

$email = 'admin@biblionova.com';
$password = 'admin123';

echo "Looking up user $email...\n";
$user = User::where('email', $email)->first();

if ($user) {
    echo "User found! ID: " . $user->id . ", Name: " . $user->first_name . " " . $user->last_name . "\n";
} else {
    die("User not found!\n");
}

echo "Testing credentials...\n";
if (Auth::attempt(['email' => $email, 'password' => $password])) {
    echo "✓ Credentials valid!\n";
    
    $authService = new AuthService();
    echo "Getting redirect URL...\n";
    $redirectUrl = $authService->getRedirectUrlByRole($user);
    echo "✓ Redirect URL: $redirectUrl\n";
    
    echo "Updating last_login_at...\n";
    try {
        $user->update(['last_login_at' => now()]);
        echo "✓ last_login_at updated!\n";
    } catch (Exception $e) {
        echo "✗ Error updating last_login_at: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
    
} else {
    echo "✗ Invalid credentials!\n";
}

echo "\nDone!\n";
