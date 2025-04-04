<?php

namespace App\Console\Commands;

use App\Services\EmailReminderService;
use Illuminate\Console\Command;

class SendActivityReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email pengingat untuk aktivitas yang akan datang dalam 1 jam';

    /**
     * Execute the console command.
     */
    public function handle(EmailReminderService $reminderService)
    {
        $this->info('Memulai pengiriman reminder aktivitas...');
        
        $count = $reminderService->sendActivityReminders();
        
        $this->info("Berhasil mengirim {$count} email reminder aktivitas.");
        
        return Command::SUCCESS;
    }
}
