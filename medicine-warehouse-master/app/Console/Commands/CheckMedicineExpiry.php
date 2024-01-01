<?php

namespace App\Console\Commands;

use App\Models\Medicine;
use Illuminate\Console\Command;

class CheckMedicineExpiry extends Command
{
    protected $signature = 'medicine:check-expiry';
    protected $description = 'Check the expiration date of medicines every week';

    public function handle(): void
    {
        $this->info('Checking medicine expiry...');

        $medicines = Medicine::query()->where('expiry_date', '<=', now()->addWeek())->get();

        foreach ($medicines as $medicine) {
            $this->info("Medicine {$medicine->name} is expiring soon on {$medicine->expiry_date}");
        }

        $this->info('Medicine expiry check completed.');
    }
}
