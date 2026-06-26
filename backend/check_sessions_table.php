<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking if sessions table exists...\n";

if (Schema::hasTable('sessions')) {
    echo "✅ Sessions table exists!\n";
    $count = DB::table('sessions')->count();
    echo "Number of session records: $count\n";
} else {
    echo "❌ Sessions table does NOT exist!\n";
    echo "Creating sessions table...\n";
    Schema::create('sessions', function ($table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->longText('payload');
        $table->integer('last_activity')->index();
    });
    echo "✅ Sessions table created!\n";
}

echo "\nChecking users with roles...\n";
$users = \App\Models\User::with('roles')->get();
foreach ($users as $user) {
    $roleNames = $user->roles->pluck('name')->implode(', ');
    echo "- $user->email ($user->first_name $user->last_name): $roleNames\n";
}
