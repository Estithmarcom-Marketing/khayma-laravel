<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteReceipts extends Command
{
    protected $signature = 'receipts:delete';

    protected $description = 'Delete all receipts daily';

    public function handle()
    {
        $files = Storage::disk('public')->files('receipts');

        if (empty($files)) {
            $this->info('No receipts found to delete.');

            return;
        }

        Storage::disk('public')->delete($files);

        $this->info(count($files).' receipt(s) deleted successfully.');
    }
}
