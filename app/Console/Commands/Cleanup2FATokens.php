<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User2FAToken;

class Cleanup2FATokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '2fa:cleanup {--days=30 : Number of days to keep tokens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired 2FA tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');

        $this->info("Cleaning up 2FA tokens older than {$days} days...");

        $deletedCount = User2FAToken::cleanupExpiredTokens();

        $this->info("Deleted {$deletedCount} expired 2FA tokens.");

        return Command::SUCCESS;
    }
}
