<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $billing;
    public $cartItems;
    public $plainPassword;

    public function __construct($billing, $cartItems, $plainPassword)
    {
        $this->billing = $billing;
        $this->cartItems = $cartItems;
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        return $this->subject('Your Course Payment Confirmation')
                    ->view('emails.mail');
    }
}

