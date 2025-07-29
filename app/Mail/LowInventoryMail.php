<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowInventoryAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $variant;

    public function __construct($variant)
    {
        $this->variant = $variant;
    }

    public function build()
    {
        return $this->subject('⚠️ Low Inventory Alert')
                    ->view('emails.low_inventory');
    }
}
