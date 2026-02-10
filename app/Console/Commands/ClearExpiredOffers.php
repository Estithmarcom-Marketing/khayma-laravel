<?php

namespace App\Console\Commands;

use App\Models\ProductVariation;
use Illuminate\Console\Command;

class ClearExpiredOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:clear-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired offers from product variations';

    /**
     * Execute the console command.
     */
   public function handle()
    {
        $count = ProductVariation::whereNotNull('offer')
            ->whereNotNull('offer_expired_date')
            ->where('offer_expired_date', '<', now())
            ->update([
                'offer' => null,
                'offer_started_date' => null,
                'offer_expired_date' => null,
            ]);

        $this->info("Expired offers cleared: {$count}");

        return Command::SUCCESS;
    }
}
