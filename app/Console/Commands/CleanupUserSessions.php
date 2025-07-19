<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSession;

class CleanupUserSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup {--days=30 : Number of days to keep sessions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old inactive user sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');

        $this->info("Cleaning up user sessions older than {$days} days...");

        $deletedCount = UserSession::cleanupOldSessions($days);

        $this->info("Deleted {$deletedCount} old session records.");

        return Command::SUCCESS;
    }
}
