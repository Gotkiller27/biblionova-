<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// List of tables to create
$tablesToCreate = [
    'deposit_request_reviews' => function (Blueprint $table) {
        $table->id();
        $table->foreignId('deposit_request_id')->constrained()->cascadeOnDelete();
        $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
        $table->enum('reviewer_role', ['responsable_demande', 'admin']);
        $table->enum('decision', ['approved', 'rejected', 'override', 'second_opinion_requested']);
        $table->text('justification')->nullable();
        $table->timestamps();
    },
    'document_revisions' => function (Blueprint $table) {
        $table->id();
        $table->foreignId('reference_id')->constrained()->cascadeOnDelete();
        $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
        $table->string('file_path');
        $table->text('revision_note')->nullable();
        $table->timestamps();
    },
    'notifications' => function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('type');
        $table->text('data')->nullable();
        $table->timestamp('read_at')->nullable();
        $table->timestamps();
    },
    'password_reset_tokens' => function (Blueprint $table) {
        $table->string('email')->primary();
        $table->string('token');
        $table->timestamp('created_at')->nullable();
    },
];

echo "Creating missing tables...\n";

foreach ($tablesToCreate as $tableName => $closure) {
    if (!Schema::hasTable($tableName)) {
        echo "Creating table: $tableName\n";
        Schema::create($tableName, $closure);
    } else {
        echo "Table $tableName already exists\n";
    }
}

// Now let's update the migrations table to mark all migrations as run
$migrations = [
    '2026_06_18_173813_create_deposit_requests_table',
    '2026_06_18_173817_create_deposit_request_reviews_table',
    '2026_06_18_173844_create_views_table',
    '2026_06_18_173847_create_downloads_table',
    '2026_06_18_173901_create_document_revisions_table',
    '2026_06_18_173911_create_notifications_table',
    '2026_06_19_000000_create_password_reset_tokens_table',
    '2026_06_19_000001_create_sessions_table',
];

$nextBatch = \Illuminate\Support\Facades\DB::table('migrations')->max('batch') + 1;

echo "\nUpdating migrations table...\n";

foreach ($migrations as $migration) {
    try {
        $exists = \Illuminate\Support\Facades\DB::table('migrations')->where('migration', $migration)->exists();
        if (!$exists) {
            \Illuminate\Support\Facades\DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $nextBatch,
            ]);
            echo "Marked migration as run: $migration\n";
        } else {
            echo "Migration already marked: $migration\n";
        }
    } catch (\Exception $e) {
        echo "Error with $migration: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ All done!\n";
