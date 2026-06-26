<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class, // Add this first
            AdminSeeder::class,
            CategorySeeder::class,
            AuthorSeeder::class,
            PublisherSeeder::class,
            ReferenceSeeder::class,
            DepositRequestSeeder::class,
            DepositRequestReviewSeeder::class,
            ViewSeeder::class,
            DownloadSeeder::class,
            DocumentRevisionSeeder::class,
            NotificationSeeder::class,
        ]);

        \App\Models\User::factory(15)->user()->active()->create();
    }
}
