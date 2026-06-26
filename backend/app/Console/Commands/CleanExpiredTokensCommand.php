<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanExpiredTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'auth:clean-expired-tokens';

    /**
     * The console command description.
     */
    protected $description = 'Clean expired password reset tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Nettoyage des tokens expirés...');

        $deletedCount = DB::table('password_reset_tokens')
            ->where('expires_at', '<', Carbon::now())
            ->delete();

        $this->info("✓ {$deletedCount} tokens expirés supprimés");

        return Command::SUCCESS;
    }
}