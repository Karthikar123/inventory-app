<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowInventoryAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $products;

public function __construct($products)
{
    $this->products = $products;
}


    /**
     * Build the message.
     */
    public function build()
{
    return $this->subject('⚠️ Low Inventory Alert')
                ->markdown('emails.low_inventory', [
                    'products' => $this->products, // ✅ pass the products correctly
                ]);
}

}
