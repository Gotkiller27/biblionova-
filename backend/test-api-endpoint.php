<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Services\AuthService;

echo "Testing API endpoint...\n";

try {
    $authService = new AuthService();
    $controller = new AuthController($authService);
    
    // Create a mock request
    $request = Request::create('/api/v1/auth/login', 'POST', [
        'email' => 'admin@biblionova.com',
        'password' => 'admin123',
    ]);
    
    echo "Calling login endpoint...\n";
    $response = $controller->login($request);
    
    echo "API Response Status: " . $response->getStatusCode() . "\n";
    echo "API Response Content:\n";
    echo $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\nDone!\n";
