<?php

namespace App\Console\Commands;

use App\Services\EmailReminderService;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email pengingat untuk booking yang akan datang dalam 1 jam';

    /**
     * Execute the console command.
     */
    public function handle(EmailReminderService $reminderService)
    {
        $this->info('Memulai pengiriman reminder booking...');
        
        $count = $reminderService->sendBookingReminders();
        
        $this->info("Berhasil mengirim {$count} email reminder booking.");
        
        return Command::SUCCESS;
    }
}
