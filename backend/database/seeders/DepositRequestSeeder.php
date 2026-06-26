<?php

namespace Database\Seeders;

use App\Models\DepositRequest;
use Illuminate\Database\Seeder;

class DepositRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DepositRequest::factory(10)->create();
    }
}