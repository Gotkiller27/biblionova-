<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Création des tokens de test pour password reset...');

        // Supprimer les anciens tokens
        DB::table('password_reset_tokens')->truncate();

        // Créer quelques tokens de test (pour développement uniquement)
        $testTokens = [
            [
                'email' => 'user@example.com',
                'token' => Hash::make('test-token-123'),
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addHours(1),
            ],
            [
                'email' => 'admin@example.com',
                'token' => Hash::make('admin-token-456'),
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addHours(1),
            ],
            [
                'email' => 'expired@example.com',
                'token' => Hash::make('expired-token-789'),
                'created_at' => Carbon::now()->subHours(2),
                'expires_at' => Carbon::now()->subHours(1), // Expiré
            ],
        ];

        foreach ($testTokens as $token) {
            DB::table('password_reset_tokens')->insert($token);
        }

        $this->command->info('✓ Tokens de test créés');
        $this->command->warn('⚠️  Ces tokens sont pour le développement uniquement !');
    }
}