<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product; // your Product model
use Illuminate\Support\Facades\Mail;
use App\Mail\LowInventoryAlertMail;

class CheckLowInventory extends Command
{
    protected $signature = 'inventory:check-low-stock';
    protected $description = 'Checks inventory and sends alerts for low stock';

    public function handle()
    {
        $threshold = 10;
        $lowStockProducts = Product::where('quantity', '<', $threshold)->get();

        if ($lowStockProducts->isNotEmpty()) {
            // Send email
            Mail::to('karthyka2k24@gmail.com')->send(new LowInventoryAlertMail($lowStockProducts));
            $this->info("Low inventory alert sent.");
        } else {
            $this->info("No low inventory found.");
        }
    }
}
