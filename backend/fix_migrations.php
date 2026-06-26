<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$migrationsToAdd = [
    '2026_06_18_173813_create_deposit_requests_table',
    '2026_06_18_173817_create_deposit_request_reviews_table',
    '2026_06_18_173844_create_views_table',
    '2026_06_18_173847_create_downloads_table',
    '2026_06_18_173901_create_document_revisions_table',
    '2026_06_18_173911_create_notifications_table',
    '2026_06_19_000000_create_password_reset_tokens_table',
    '2026_06_19_000001_create_sessions_table'
];

$nextBatch = DB::table('migrations')->max('batch') + 1;

foreach ($migrationsToAdd as $migration) {
    try {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $nextBatch
        ]);
        echo "Marked migration as run: $migration\n";
    } catch (\Exception $e) {
        echo "Skipping $migration: " . $e->getMessage() . "\n";
    }
}

echo "\nDone!";
